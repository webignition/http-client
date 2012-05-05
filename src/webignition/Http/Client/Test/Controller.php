<?php

namespace webignition\Http\Client\Test;

/**
 *
 */
class Controller {
    
    /**
     *
     * @var string
     */
    private $libraryPath = null;

    
    /**
     *
     * @var \webignition\Http\Client\Client
     */
    private $client = null;
    
    
    /**
     *
     * @var array
     */
    private $testPaths = null;
    
    
    /**
     *
     * @var array
     */
    private $tests = null;

    /**
     *
     * @param string $libraryPath 
     */
    public function setLibraryPath($libraryPath) {
        $this->libraryPath = $libraryPath;
        $this->testPaths = null;
        $this->tests = null;
    }
    
    
    /**
     *
     * @return \webignition\Http\Client\Client
     */
    private function client() {
        if (is_null($this->client)) {
            $this->client = new \webignition\Http\Client\Client();         
        }
        
        return $this->client;
    }
    
    
    public function runTests() {
        foreach ($this->tests() as $currentTest) {            
            $currentTest->setClient($this->client());            
            $currentTest->execute();
        }
    }
    
    
    /**
     *
     * @return array
     */
    private function testPaths() {
        if (is_null($this->testPaths)) {
            $this->testPaths = array();
            
            $directoryIterator = new \DirectoryIterator($this->libraryPath);
            foreach ($directoryIterator as $directoryIteratorItem) {                
                if ($this->isTestFilename($directoryIteratorItem)) {
                    $this->testPaths[] = $directoryIteratorItem->getPathname();
                }
            }
        }
        
        return $this->testPaths;
    }
    
    
    /**
     *
     * @return array
     */
    private function tests() {
        if (is_null($this->tests)) {
            foreach ($this->testPaths() as $testFilePath) {
                require_once($testFilePath);                
                $className = $this->getTestClassName($testFilePath);               
                $this->tests[] = new $className;
            }
        }
        
        return $this->tests;
    }
    
    
    /**
     *
     * @param \DirectoryIterator $directoryItem
     * @return boolean 
     */
    private function isTestFilename(\DirectoryIterator $directoryItem) {
        return preg_match('/.+Test\.php$/', $directoryItem->getFilename()) > 0;
    }
    
    
    
    /**
     *
     * @param \DirectoryIterator $directoryItem
     * @return string 
     */
    private function getTestClassName($testFilePath) {        
        $relativeClassName = pathinfo($testFilePath, PATHINFO_FILENAME);        
        $namespace = $this->getTestClassNamespace($testFilePath);
        
        return $namespace . '\\' . $relativeClassName;
    }
    
    
    /**
     *
     * @param type $testFilePath
     * @return string 
     */
    private function getTestClassNamespace($testFilePath) {
        $testLines = explode("\n", file_get_contents($testFilePath));
        
        foreach ($testLines as $testLine) {
            if (preg_match('/namespace .+;/', $testLine)) {
                return '\\' . str_replace(array('namespace ', ';'), '', $testLine);
            }
        }
        
        return '\\';
    }

}