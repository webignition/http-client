<?php

namespace webignition\Http\Response\Cache\Store;

/**
 * A default empty cache store implementation.
 * Is always empty, never stores anything.
 *
 * @package webignition\Http\Response\Cache\Store
 *
 */
class EmptyStore implements \webignition\Http\Response\Cache\Store\Store {

    /**
     *
     * @see \webignition\Http\Response\Cache\Store\Store::contains
     * @param \HttpRequest $request
     * @return bool
     */
    public function contains(\HttpRequest $request) {
        return false;
    }


    /**
     *
     * @see \webignition\Http\Response\Cache\Store\Store::get
     * @param \HttpRequest $request
     * @param \webignition\Http\Response\Cache\CacheableResponse $response
     * @return bool
     */
    public function set(\HttpRequest $request, \webignition\Http\Response\Cache\CacheableResponse $response) {
        return false;
    }


    /**
     *
     * @see \webignition\Http\Response\Cache\Store\Store::set
     * @param \HttpRequest $request
     * @return \webignition\Http\Response\Cache\CacheableResponse $response
     */
    public function get(\HttpRequest $request) {
        return new \HttpMessage();
    }


    /**
     *
     * @see \webignition\Http\Response\Cache\Store\Store::remove
     * @param \HttpRequest $request
     * @return bool
     */
    public function remove(\HttpRequest $request) {
        return false;
    }
    
    
    /**
     *
     * @see \webignition\Http\Response\Cache\Store\Store::clear
     */    
    public function clear() {        
    }


}