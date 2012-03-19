<?php

namespace webignition\Http\Client;

/**
 * An HttpClient.
 *
 * @package webignition\Http\Client
 *
 */
class Client {

    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage
     */
    public function getResponse(\HttpRequest $request) {
        return $request->send();
    }

}