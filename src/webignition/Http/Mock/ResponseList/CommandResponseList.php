<?php

namespace webignition\Http\Mock\ResponseList;

use webignition\Http\Mock\IdentityHash\CommandHash;
use webignition\Http\Mock\Request\Command as RequestCommand;

class CommandResponseList extends ResponseList {

    /**
     *
     * @param string $command
     * @param \HttpMessage $response 
     */
    public function set($command, \HttpMessage $response) {        
        $hash = new CommandHash();
        $hash->setCommand($command);
        parent::setResponse($hash, $response);
    }
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return boolean
     */
    public function hasResponseFor(\HttpRequest $request) {
        $requestCommand = new RequestCommand();
        $requestCommand->setRequest($request);
        
        $hash = new CommandHash();
        $hash->setCommand($requestCommand->getCommand());
        
        return $this->hasResponseForHash($hash);
    }
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage
     */
    public function getResponseFor(\HttpRequest $request) {
        $requestCommand = new RequestCommand();
        $requestCommand->setRequest($request);
        
        $hash = new CommandHash();
        $hash->setCommand($requestCommand->getCommand());
        
        return $this->getResponseForHash($hash);
    }
}