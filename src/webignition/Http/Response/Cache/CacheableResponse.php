<?php

namespace webignition\Http\Response\Cache;

/**
 *
 *
 * @package webignition\Http\Response\Cache
 *
 */
class CacheableResponse extends \HttpMessage {


    /**
     *
     * @var \webignition\Http\Header\Header
     */
    var $header = null;


    /**
     * List of status codes for which responses can be cached
     *
     * Note: 206 (Partial Content) is potentially cachable, however we don't
     *       yet support the range and content-range headers.
     *
     * @var array
     */
    var $cacheableStatusCodes = array(200, 203, 300, 301, 410);


    /**
     * List of response header fields that are not to be modified
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html#sec13.5.2
     * @var array
     */
    var $nonModifiableHeaderFieldNames = array(
        'content-location',
        'content-md5',
        'etag',
        'last-modified',
        'expires'
    );
    
    public function __construct($message = '') {
        parent::__construct($message);

        $this->header = new \webignition\Http\Header\CachedResponseHeader();
        $this->header->populate($this->getHeaders()->toArray());
    }



    /**
     *
     * @param string $serialized
     */
    public function unserialize($serialized) {
        parent::unserialize($serialized);

        $this->header = new \webignition\Http\Header\CachedResponseHeader();
        $this->header->populate($this->getHeaders()->toArray());
    }


    /**
     *
     * @return bool
     */
    public function isFresh() {
        return $this->getFreshnessLifetime() > $this->getCurrentAge();
    }


    /**
     *
     * @return bool
     */
    public function isStale() {
        return !$this->isFresh();
    }


    /**
     *
     * @return bool
     */
    public function  isCacheable() {
        if ($this->hasErrorCode()) {
            return false;
        }

        /**
         * Analyse status code
         *
         * "A response received with a status code of 200, 203, 206, 300, 301
         *  or 410 MAY be stored by a cache and used in reply to a subsequent
         *  request, subject to the expiration mechanism, unless a cache-control
         *  directive prohibits caching. However, a cache that does not support
         *  the Range and Content-Range headers MUST NOT cache 206
         *  (Partial Content) responses."
         */
        if (!\in_array($this->getResponseCode(), $this->cacheableStatusCodes)) {
            return false;
        }


        // Analyse cache-control header
        if ($this->getHeaders()->hasField(\webignition\Http\Header\AbstractHeader::CACHE_CONTROL_FIELD_NAME)) {
            $cacheControl = $this->getHeaders()->getCacheControlField();

            /**
             * no-store: don't store any of the response
             */
            if ($cacheControl->hasDirective('no-store')) {
                return false;
            }


            /**
             * no-cache: no field names, don't cache all response
             *           with field names, don't cache those listed
             *
             * We currently implement this lazily and don't cache anything
             * if there is a no-cache directive.
             */
            if ($cacheControl->hasDirective('no-cache')) {
                return false;
            }
        }


        // Fallback to HTTP/1.0 pragma header
        if ($this->getHeaders()->hasField('pragma')) {
            if ($this->getHeaders()->getField('pragma')->getValue()->get() == 'no-cache') {
                return false;
            }
        }

        

        return true;
    }


    /**
     *
     * @return \webignition\Http\Header\AbstractHeader
     */
    public function getHeaders() {
        return $this->header;
    }


    /**
     * Get the number of seconds for which the response is fresh i.e. non-stale
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html#sec13.2.4
     *
     */
    public function getFreshnessLifetime() {
        if ($this->getHeaders()->hasField(\webignition\Http\Header\AbstractHeader::CACHE_CONTROL_FIELD_NAME)) {
            return $this->getHeaders()->getCacheControlField()->getMaxAgeDelta();
        }

        if (!$this->getHeaders()->hasField('expires')) {
            return 0;
        }

        if (!$this->getHeaders()->hasField('date')) {
            return 0;
        }

        return $this->getHeaders()->getField('expires')->getValue()->format('U') - $this->getHeaders()->getField('date')->getValue()->format('U');
    }

    /**
     *
     * @return int
     */
    public function getAgeValue() {        
        return ($this->getHeaders()->hasField('age')) ? (int)$this->getHeaders()->getField('age')->getValue()->get() : 0;
    }


    /**
     *
     * @param \webignition\Http\Header\Field\Field $field
     * @return bool
     */
    public function setHeaderField(\webignition\Http\Header\Field\Field $field) {
        if (!$this->isHeaderFieldModifiable($field->getName())) {
            return false;
        }

        $this->getHeaders()->setField( $field);
        parent::setHeaders($this->getHeaders()->toArray());
        return true;
    }


    public function setHeader(\webignition\Http\Header\AbstractHeader $header) {
        $this->header = $header;
        parent::setHeaders($this->getHeaders()->toArray());
        return true;
    }


    /**
     *
     * @return int
     */
    public function getCurrentAge() {
        return max(0, (int)($this->getCorrectedInitialAge() + $this->getResidentTime()));
    }


    /**
     *
     * @param string $name
     * @return bool
     */
    public function isHeaderFieldModifiable($name) {
        if (!\is_string($name)) {
            return false;
        }

        return !\in_array(strtolower($name), $this->nonModifiableHeaderFieldNames);
    }


    /**
     * Is this response capable of being validated after expiring?
     *
     * @return bool
     */
    public function canBeValidated() {
        if ($this->getHeaders()->hasField('etag')) {
            return true;
        }

        if ($this->getHeaders()->hasField('last-modified')) {
            return true;
        }

        return false;
    }
    

    /**
     *
     * @return bool
     */
    private function hasErrorCode() {
        $errorCodeFirstDigit = array(4,5);
        return \in_array(\substr($this->getResponseCode(), 0, 1), $errorCodeFirstDigit);
    }


    /**
     *
     * @return int
     */
    private function getRequestTimeValue() {
        return $this->getDateHeaderFieldAsInteger('x-request-time');
    }


    /**
     *
     * @return int
     */
    private function getResponseTimeValue() {
        return $this->getDateHeaderFieldAsInteger('x-response-time');
    }


    /**
     *
     * @param string $name
     * @return int
     */
    private function getDateHeaderFieldAsInteger($name) {
        return ($this->getHeaders()->hasField($name)) ? (int)$this->getHeaders()->getDateField($name)->getValue()->get()->format('U') : 0;
    }


    /**
     *
     * @return int
     */
    private function getApparentAge() {
        $dateValue = $this->getDateHeaderFieldAsInteger('date');

        return max(0, $this->getResponseTimeValue() - $dateValue);
    }


    /**
     *
     * @return int
     */
    private function getCorrectedRecievedAge() {
        return max($this->getApparentAge(), $this->getAgeValue());
    }


    /**
     *
     * @return int
     */
    private function getResponseDelay() {
        return $this->getResponseTimeValue() - $this->getRequestTimeValue();
    }


    /**
     *
     * @return int
     */
    private function getCorrectedInitialAge() {
        return $this->getCorrectedRecievedAge() - $this->getResponseDelay();
    }


    /**
     *
     * @return int
     */
    private function getResidentTime() {
        $now = new \DateTime();
        return (int)($now->format('U') - $this->getResponseTimeValue());
    }

    
      /*
       * Reference:
       * 13.2.3 Age Calculations
       * http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html#sec13.2.3
       *
       * age_value
       *      is the value of Age: header received by the cache with
       *              this response.
       * date_value
       *      is the value of the origin server's Date: header
       * request_time
       *      is the (local) time when the cache made the request
       *              that resulted in this cached response
       * response_time
       *      is the (local) time when the cache received the
       *              response
       * now
       *      is the current (local) time
       * 
       * apparent_age = max(0, response_time - date_value);
       * corrected_received_age = max(apparent_age, age_value);
       * response_delay = response_time - request_time;
       * corrected_initial_age = corrected_received_age + response_delay;
       * resident_time = now - response_time;
       * current_age   = corrected_initial_age + resident_time;
       *
       */
    


}