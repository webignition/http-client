<?php

namespace webignition\Http\Header;

/**
 * Represents a request header
 *
 * @package webignition\Http\Header
 *
 */
class RequestHeader extends \webignition\Http\Header\AbstractHeader {


    /**
     * List of header fields not relevant to requests
     * This is not an exhaustive list
     *
     * @var array
     */
    private $nonRequestFields = array();


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
            if (!\in_array(strtolower($fieldName), $this->nonRequestFields)) {
                $filteredHeaders[$fieldName] = $rawFieldValue;
            }
        }

        parent::populate($filteredHeaders);
    }

}