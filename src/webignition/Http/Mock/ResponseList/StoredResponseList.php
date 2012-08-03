<?php

namespace webignition\Http\Mock\ResponseList;

use webignition\Http\Mock\IdentityHash\CommandHash;
use webignition\Http\Mock\Request\Command as RequestCommand;

class StoredResponseList extends ResponseList {
    
    
    /**
     *
     * @var string
     */
    private $fixturesPath = null;
    
    
    /**
     *
     * @param string $fixturesPath
     * @return \webignition\Http\Mock\ResponseList\StoredResponseList 
     */
    public function setFixturesPath($fixturesPath) {
        $this->fixturesPath = $fixturesPath;
        return $this;
    }
    
    
    /**
     *
     * @return string
     */
    public function getFixturesPath() {
        return $this->fixturesPath;
    }
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return boolean
     */
    public function hasResponseFor(\HttpRequest $request) {        
        return file_exists($this->getRequestFixturePath($request));
    }
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage
     */
    public function getResponseFor(\HttpRequest $request) {        
        return new \HttpMessage(file_get_contents($this->getRequestFixturePath($request)));
    }
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return string
     */
    private function getRequestFixturePath(\HttpRequest $request) {
        $requestCommand = new RequestCommand();
        $requestCommand->setRequest($request);
        
        $hash = new CommandHash();
        $hash->setCommand($requestCommand->getCommand());        
        
        return $this->getFixturesPath() . '/' . $hash->getHash();
    }
}