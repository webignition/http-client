<?php

use webignition\Http\Mock\Request\Command;
use webignition\Http\Mock\Client\Client as HttpClient;

abstract class BaseTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @var HttpClient
     */
    protected $httpClient;
    
    
    protected function setUp() {
        $this->httpClient = new HttpClient();
    }
    
}