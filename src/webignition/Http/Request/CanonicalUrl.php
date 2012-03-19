<?php

namespace webignition\Http\Request;

/**
 * Get the canonical URL for a given HTTP request
 *
 * @package webignition\Http\Request
 *
 */
class CanonicalUrl {

    /**
     *
     * @var \HttpRequest
     */
    private $request;


    /**
     *
     * @var string
     */
    private $canonicalUrl = null;


    /**
     *
     * @param \HttpRequest $request 
     */
    public function __construct(\HttpRequest $request) {
        $this->request = $request;
    }


    /**
     *
     * @return string
     */
    public function __toString() {
        if (is_null($this->canonicalUrl)) {
            $this->canonicalUrl = $this->buildCanonicalUrl();
        }

        return $this->canonicalUrl;
    }

     /**
      *
      * @return string
      */
     private function buildCanonicalUrl() {
         $url = $this->getUrl();         
         $queryString = $this->getNormalisedQueryString();
          
         if ($queryString != '') {
             $url .= '?'.$queryString;
         }

         return $url;
     }


     /**
      *
      * @return bool
      */
     private function hasQueryString() {
         if ($this->urlContainsQueryString()) {
             return true;
         }

         return $this->request->getQueryData() != '';
     }
     

     /**
      *
      * @return bool
      */
     private function urlContainsQueryString() {
         return (\substr_count($this->request->getUrl(), '?') > 0);
     }


     /**
      *
      * @return string
      */
     private function getUrl() {
         if (!$this->hasQueryString()) {
             return $this->request->getUrl();
         }
         
         if (!$this->urlContainsQueryString()) {
             return $this->request->getUrl();
         }

         $rawUrl = $this->request->getUrl();
         $queryStringPosition = \strpos($rawUrl, '?');

         return \substr($rawUrl, 0, $queryStringPosition);
     }


     /**
      *
      * @return string
      */
     private function getQueryStringFromRequestUrl() {
         $rawUrl = $this->request->getUrl();
         if (($queryStringPosition = \strpos($rawUrl, '?')) === false) {
             return '';
         }

         return \substr($rawUrl, $queryStringPosition + 1);
     }


     
     /**
      *
      * @return \webignition\Http\QueryString\Normaliser
      */
     private function getNormalisedQueryString() {
         if (!$this->hasQueryString()) {
             return '';
         }

         $queryStringFromUrl = $this->getQueryStringFromRequestUrl();
         $queryData = $this->request->getQueryData();

         if ($queryStringFromUrl == '' && $queryData == '') {
             return new \webignition\Http\QueryString\Normaliser();
         }

         if ($queryStringFromUrl == '' && $queryData != '') {
             return new \webignition\Http\QueryString\Normaliser($this->request->getQueryData());
         }

         if ($queryStringFromUrl != '' && $queryData == '') {
             return new \webignition\Http\QueryString\Normaliser($queryStringFromUrl);
         }

         return new \webignition\Http\QueryString\Normaliser($this->getQueryStringFromRequestUrl().'&'.$this->request->getQueryData());
     }




}