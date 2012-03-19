<?php

namespace webignition\Http\Request;

/**
 * Generates a unique key for a given HTTP request
 *
 *
 * @package webignition\Http\Request
 *
 */
class Key {

    /**
     *
     * @var \HttpRequest
     */
    private $request;


    /**
     *
     * @var string
     */
    private $key = null;


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
        if (is_null($this->key)) {
            $this->key = $this->buildKey();
        }

        return $this->key;
    }


     /**
      *
      * @return string
      */
     private function buildKey() {
         $canonicalUrl = new \webignition\Http\Request\CanonicalUrl($this->request);
         return md5((string)$canonicalUrl);
     }

}