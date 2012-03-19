<?php

namespace webignition\Http\QueryString;

/**
 * Normalises a query string
 *
 * Removes duplicate keys, arranges keys alphabetically
 *
 * @package webignition\Http\QueryString
 *
 */
class Normaliser {

    /**
     *
     * @var \HttpQueryString
     */
    private $queryString;


    /**
     *
     * @var string
     */
    private $normalisedQueryString = null;


    /**
     *
     * @param string $rawQueryString 
     */
    public function __construct($rawQueryString = '') {
        if (!is_string($rawQueryString)) {
            $rawQueryString = '';
        }

        $this->queryString = new \HttpQueryString(false, $rawQueryString);
    }
    

    /**
     *
     * @return string
     */
    public function __toString() {
        if (is_null($this->normalisedQueryString)) {
            $this->normalisedQueryString = $this->getNormalisedQueryString();
        }

        return $this->normalisedQueryString;
    }


    /**
     *
     * @return string
     */
    private function getNormalisedQueryString() {
        $queryStringParameters = $this->queryString->toArray();
        ksort($queryStringParameters);

        return \http_build_query($queryStringParameters);
    }


}