<?php

namespace webignition\Http\Client\Test;

/**
 *
 */
abstract class Test {
    
    
    /**
     *
     * @var \webignition\Http\Client\Client
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
    
    
    /**
     * Allows a test to disable itself, we may want to only run
     * a subset of tests when developing against a failing test
     * 
     * @var boolean
     */
    private $isEnabled = true;
    
    abstract public function run();
    
    
    public function execute() {        
        if ($this->isEnabled === true) {
            $this->output("Executing ".get_class($this)."\n");
        
            $this->run();
            echo $this->output;
            echo "\n";
        }
    }
    
    protected function enable() {
        $this->isEnabled = true;
    }
    
    protected function disable() {
        $this->isEnabled = false;
    }    
    
    /**
     *
     * @param \webignition\Http\Client\Client $client 
     */
    public function setClient(\webignition\Http\Client\Client $client) {
        $this->client = $client;
    }
    
    
    /**
     *
     * @return \webignition\Http\Client\Client 
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