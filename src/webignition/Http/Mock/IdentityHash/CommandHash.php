<?php

namespace webignition\Http\Mock\IdentityHash;

/**
 * Translates a HTTP request command into a hash unique to the request properties.
 *  
 */
class CommandHash extends IdentityHash {  

    /**
     *
     * @var string
     */
    private $command;
    
    
    /**
     *
     * @param string $command
     * @return \webignition\Http\Mock\RequestHash\CommandHash 
     */
    public function setCommand($command) {
        $this->command = $command;
        return $this;
    }
    
    
    /**
     *
     * @return string
     */
    public function getHash() {
        return $this->get($this->command);
    }
}