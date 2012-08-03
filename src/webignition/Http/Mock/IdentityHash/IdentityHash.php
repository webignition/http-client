<?php

namespace webignition\Http\Mock\IdentityHash;

abstract class IdentityHash {

    
    /**
     *
     * @param string $requestContent
     * @return string
     */
    protected function get($requestContent) {
        return md5($requestContent);
    }
    
}