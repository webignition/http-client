<?php

namespace webignition\Http\Client;

/**
 * A caching HttpClient.
 * 
 * Introduces a transparent caching layer to an HTTP client
 *
 * @package webignition\Http\Client
 *
 */
class CachingClient extends \webignition\Http\Client\Client {

    /**
     *
     * @var \webignition\Http\Response\Cache\Store\Store
     */
    private static $cacheStore = null;


    /**
     * List of request methods for which the response MAY be cached
     *
     * @var array
     */
    private $cacheableRequestMethods = array(
        HTTP_METH_GET
    );


    /**
     *
     * @return \webignition\Http\Response\Cache\Store\Store
     */
    public function getStore() {
        if (!self::$cacheStore instanceof \webignition\Http\Response\Cache\Store\Store) {
            self::$cacheStore = new \webignition\Http\Response\Cache\DiskStore\DiskStore();
        }

        return self::$cacheStore;
    }


    /**
     *
     * @param \webignition\Http\Response\Cache\Store\Store $store
     * @return bool
     */
    public function setStore(\webignition\Http\Response\Cache\Store\Store $store) {
        return self::$cacheStore = $store;
    }


    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage
     */
    public function getResponse(\HttpRequest $request) {
        if (!$this->isRequestTypeCacheable($request)) {
            return parent::getResponse($request);
        }

        if (!$this->getStore()->contains($request)) {
            $response = $this->getNewCacheableResponse($request);            
            if ($response->isCacheable()) {
                $this->getStore()->set($request, $response);
            }

            return $response;
        }       

        $response = $this->getCachedResponse($request);
        if ($response->isFresh()) {
            return $response;
        }        

        if (!$response->canBeValidated()) {
            $this->getStore()->remove($request);
            $response = $this->getNewCacheableResponse($request);
            if ($response->isCacheable()) {
                $this->getStore()->set($request, $response);
            }

            return $response;
        }        
       

        $validationResponse = $this->getValidationResponse($request, $response);        
        if ($validationResponse->getResponseCode() == 200) {
            $this->getStore()->remove($request);
            if ($validationResponse->isCacheable()) {
                $this->getStore()->set($request, $validationResponse);
            }

            return $validationResponse;
        }

        if ($validationResponse->getResponseCode() == 304) {
            return $this->getRebuiltCachedResponse($request, $validationResponse);
        }

        return $validationResponse;
    }


    /**
     * Can the response to the type of request to be made by cached?
     *
     * @return bool
     */
    private function isRequestTypeCacheable(\HttpRequest $request) {
        return in_array($request->getMethod(), $this->cacheableRequestMethods);
    }


    /**
     *
     * @param \HttpRequest $request
     * @return \webignition\Http\Response\Cache\CacheableResponse
     */
    private function getNewCacheableResponse(\HttpRequest $request) {
        $requestHeader = new \webignition\Http\Header\ProxyRequestHeader();
        $requestHeader->populate($request->getHeaders());

        $this->setCommonRequestHeaders($request);

        $requestTime = new \DateTime();
        $cacheableResponse = new \webignition\Http\Response\Cache\CacheableResponse((string)parent::getResponse($request));
        $responseTime = new \DateTime();

        $cacheableResponse->setHeaderField(new \webignition\Http\Header\Field\DateField('x-request-time', $requestTime->format('D, d M Y H:i:s e')));
        $cacheableResponse->setHeaderField(new \webignition\Http\Header\Field\DateField('x-response-time', $responseTime->format('D, d M Y H:i:s e')));

        return $cacheableResponse;
    }   



    /**
     * Set additional headers common to all types of outbound \HttpRequest
     *
     * @param \HttpRequest $request
     */
    private function setCommonRequestHeaders(\HttpRequest $request) {
        $currentHeaders = $request->getHeaders();
        $newHeaders = array();
        
        foreach ($currentHeaders as $fieldName => $fieldValue) {           
            $newHeaders[strtolower($fieldName)] = $fieldValue;
        }       

        // Ensure a compressed response is requested
        $newHeaders['accept-encoding'] = 'gzip;q=1.0, *;q=0';
        
        $request->setHeaders($newHeaders);
    }


    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage
     */
    private function getCachedResponse(\HttpRequest $request) {
        $response = $this->getStore()->get($request);
        $response->setHeaderField(new \webignition\Http\Header\Field\IntegerField('age', $response->getCurrentAge()));

        return $response;
    }
    

    /**
     * Get response from origin server validating existing cached response
     *
     * @param \HttpRequest $request
     * @param \webignition\Http\Response\Cache\CacheableResponse $response
     */
    private function getValidationResponse(\HttpRequest $request, \webignition\Http\Response\Cache\CacheableResponse $response) {
        $validationRequest = new \HttpRequest((string)new \webignition\Http\Request\CanonicalUrl($request));
        $this->setCommonRequestHeaders($validationRequest);

        $validationHeader = new \webignition\Http\Header\ProxyRequestHeader();
        $validationHeader->populate($validationRequest->getHeaders());

        $now = new \DateTime();
        $validationHeader->setField(new \webignition\Http\Header\Field\DateField('date', new \webignition\Http\Header\Field\DateValue($now->format('D, j M Y H:i:s e'))));

        if ($response->getHeaders()->hasField('etag')) {
            $validationHeader->setField(new \webignition\Http\Header\Field\Field('if-none-match', $response->getHeaders()->getField('etag')->getValue().''));
        }

        if ($response->getHeaders()->hasField('last-modified')) {
            $validationHeader->setField(new \webignition\Http\Header\Field\Field('if-modified-since', $response->getHeaders()->getField('last-modified')->getValue().''));
        }

        $validationRequest->setHeaders($validationHeader->toArray());
        return $this->getNewCacheableResponse($validationRequest);
    }


    /**
     *
     * @param \HttpRequest $request
     * @param \webignition\Http\Response\Cache\CacheableResponse $validationResponse
     * @return bool
     */
    private function getRebuiltCachedResponse(\HttpRequest $request, \webignition\Http\Response\Cache\CacheableResponse $validationResponse) {
        if (!$validationResponse->getResponseCode() == 304) {
            return $this->getCachedResponse($request);
        }

        $storedResponse = $this->getCachedResponse($request);
        $storedResponse->setHeader($validationResponse->getHeaders());

        $this->getStore()->set($request, $storedResponse);
        return $storedResponse;
    }



}