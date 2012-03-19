<?php

namespace webignition\Http\Client\Test;

/**
 *
 */
abstract class Test {
    
    
    /**
     *
     * @var \webignition\Http\Client\CachingClient
     */
    private $client = null;
    
    
    /**
     *
     * @var string
     */
    private $output = '';
    
    
    /**
     *
     * @var float
     */
    private $timerStartTime = null;
    
    abstract public function run();
    
    public function execute() {
        $this->output("Executing ".get_class($this)."\n");
        
        $this->run();
        echo $this->output;
    }
    
    
    /**
     *
     * @param \webignition\Http\Client\CachingClient $client 
     */
    public function setClient(\webignition\Http\Client\CachingClient $client) {
        $this->client = $client;
    }
    
    
    /**
     *
     * @return \webignition\Http\Client\CachingClient 
     */
    protected function client() {
        return $this->client;
    }
    
    
    /**
     *
     * @return float
     */
    protected function startTimer() {
        $this->timerStartTime = microtime(true);
        return $this->timerStartTime;
    }
    
    
    /**
     *
     * @return float
     */
    protected function getDuration() {
        if (is_null($this->timerStartTime)) {
            return 0;
        }
        
        return microtime(true) - $this->timerStartTime;
    }
    
    
    /**
     *
     * @param string $content 
     */
    protected function output($content) {
        $this->output .= $content;
    }
            

}