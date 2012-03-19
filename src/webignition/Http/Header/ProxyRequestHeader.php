<?php

namespace webignition\Http\Header;

/**
 * Represents a request header used by a proxy
 *
 * HTTP/1.1 introduces rules regarding what headers are to be forwarded
 * by proxies
 *
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html#sec13.5.1
 * @package webignition\Http\Header
 *
 */
class ProxyRequestHeader extends \webignition\Http\Header\RequestHeader {

    /**
     *
     */
    public function __construct() {
    }


    /**
     * Populate this header object from an array of raw header fields,
     * removing those not stored by caches or proxies
     *
     * @param array $rawHeaders
     * @return bool
     */
    public function populate(array $rawHeaders) {
        $filteredHeaders = array();

        foreach ($rawHeaders as $fieldName => $rawFieldValue) {            
            if (!\in_array(strtolower($fieldName), $this->getHopByHopFields())) {
                $filteredHeaders[$fieldName] = $rawFieldValue;
            }
        }
        
        parent::populate($filteredHeaders);
    }

}