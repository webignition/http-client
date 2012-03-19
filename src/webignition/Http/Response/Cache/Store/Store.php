<?php

namespace webignition\Http\Response\Cache\Store;

/**
 * Defines methods required of a Http Cache Store
 *
 *
 * @package webignition\Http\Response\Cache\Store
 *
 */
interface Store {

    /**
     * Does the cache store contain a response for a given request?
     *
     * @param \HttpRequest $request
     * @return bool
     */
    public function contains(\HttpRequest $request);


    /**
     * Store the response for the given request key
     *
     * @param \HttpRequest $request
     * @param \webignition\Http\Response\Cache\CacheableResponse $response
     * @return bool
     */
    public function set(\HttpRequest $request, \webignition\Http\Response\Cache\CacheableResponse $response);


    /**
     * Get the response for the given request key
     *
     * @param \HttpRequest $request
     * @return \webignition\Http\Response\Cache\CacheableResponse $response
     */
    public function get(\HttpRequest $request);


    /**
     * Remove cached response for given request key
     *
     * @param \HttpRequest $request
     * @return bool
     */
    public function remove(\HttpRequest $request);
    
    
    /**
     * Remove all cached responses
     *  
     */
    public function clear();

}