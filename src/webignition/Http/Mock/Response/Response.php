<?php
namespace webignition\Http\Mock\Response;

class Response extends \HttpMessage {
    
    public function __construct() {
        $this->setType(HTTP_MSG_RESPONSE);
    }
    
}