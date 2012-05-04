<?php

namespace webignition\Http\Response\Cache\DiskStore;

/**
 * A disk-based cache store
 *
 *
 * @package webignition\Http\Response\Cache\DiskStore
 *
 */
class DiskStore implements \webignition\Http\Response\Cache\Store\Store {
    
    const CACHE_FILE_EXTENSION = '.http-client-cache';

    const EXCEPTION_DIRECTORY_PATH_DOES_NOT_EXIST = 100;
    const EXCEPTION_DIRECTORY_IS_NOT_A_DIRECTORY = 101;
    const EXCEPTION_DIRECTORY_IS_NOT_WRITEABLE = 102;

    private $exceptionMessages = array(
        self::EXCEPTION_DIRECTORY_PATH_DOES_NOT_EXIST => 'Specified directory path does not exist',
        self::EXCEPTION_DIRECTORY_IS_NOT_A_DIRECTORY => 'Specified directory path is not a directory',
        self::EXCEPTION_DIRECTORY_IS_NOT_WRITEABLE => 'Specified directory path is not writeable'
    );


    /**
     *
     * @var string
     */
    private $directoryPath = null;


    /**
     *
     * @var \DirectoryIterator
     */
    private $directoryIterator = null;
    

    /**
     *
     * @param string $directoryPath
     */
    public function __construct($directoryPath = null) {
        $this->directoryPath = (is_null($directoryPath)) ? $this->getDefaultCachePath() : $directoryPath;
    } 
    
    
    /**
     *
     * @return string 
     */
    private function getDefaultCachePath() {
        $defaultCachePath = sys_get_temp_dir().'/http-client-cache';  
        
        if (!is_dir($defaultCachePath)) {
            mkdir($defaultCachePath);
        }
        
        return $defaultCachePath;
    }

        /**
     *
     * @see \webignition\Http\Response\Cache\Store\Store::contains
     * @param \HttpRequest $request
     * @return bool
     */
    public function contains(\HttpRequest $request) {
        return @file_exists($this->getCacheFilename($request));
    }


    /**
     *
     * @see \webignition\Http\Response\Cache\Store\Store::get
     * @param \HttpRequest $request
     * @param \webignition\Http\Response\Cache\CacheableResponse $response
     * @return bool
     */
    public function set(\HttpRequest $request, \webignition\Http\Response\Cache\CacheableResponse $response) {        
        //var_dump($this->getCacheFilename($request));
        
        return (file_put_contents($this->getCacheFilename($request), \serialize($response)) === false) ? false : true;
    }


    /**
     *
     * @see \webignition\Http\Response\Cache\Store\Store::set
     * @param \HttpRequest $request
     * @return \webignition\Http\Response\Cache\CacheableResponse $response
     */
    public function get(\HttpRequest $request) {        
        if (!$this->contains($request)) {
            return new \HttpMessage();
        }
        
        return \unserialize(file_get_contents($this->getCacheFilename($request)));
    }


    /**
     *
     * @see \webignition\Http\Response\Cache\Store\Store::remove
     * @param \HttpRequest $request
     * @return bool
     */
    public function remove(\HttpRequest $request) {
        if (!$this->contains($request)) {
            return true;
        }

        return \unlink($this->getCacheFilename($request));
    }
    
    
    /**
     *
     * @see \webignition\Http\Response\Cache\Store\Store::clear
     */    
    public function clear() {
        /* @var $directoryItem \DirectoryIterator */       
        
        foreach ($this->directoryIterator() as $directoryItem) {            
            if ($this->isCacheFile($directoryItem)) {
                unlink($directoryItem->getPathname());
            }
        }
        
        $this->directoryIterator()->rewind();
    }    


    /**
     *
     * @param \HttpRequest $request
     * @return string
     */
    private function getCacheFilename(\HttpRequest $request) {              
        return $this->directoryIterator()->getPathInfo()->getPathname().'/'.$this->getRequestKey($request).self::CACHE_FILE_EXTENSION;
    }


    /**
     *
     * @return \DirectoryIterator
     */
    private function directoryIterator() {
        if (!@\file_exists($this->directoryPath())) {
            throw new \webignition\Http\Response\Cache\DiskStore\Exception(
                $this->exceptionMessages[self::EXCEPTION_DIRECTORY_PATH_DOES_NOT_EXIST],
                self::EXCEPTION_DIRECTORY_PATH_DOES_NOT_EXIST
            );
        }

        if (!@\is_dir($this->directoryPath())) {
            throw new \webignition\Http\Response\Cache\DiskStore\Exception(
                $this->exceptionMessages[self::EXCEPTION_DIRECTORY_PATH_DOES_NOT_EXIST],
                self::EXCEPTION_DIRECTORY_PATH_DOES_NOT_EXIST
            );
        }

        $this->directoryIterator = new \DirectoryIterator($this->directoryPath());
       

        return $this->directoryIterator;
    }
    
    
    /**
     *
     * @return string
     */
    private function directoryPath() {
        if (!is_dir($this->directoryPath)) {
            mkdir($this->directoryPath, true);
        }
        
        return $this->directoryPath;
    }
    
    
    /**
     *
     * @param \DirectoryIterator $directoryItem
     * @return boolean 
     */
    private function isCacheFile(\DirectoryIterator $directoryItem) {
        if (!$directoryItem->isFile()) {
            return false;
        }
        
        return preg_match($this->getCacheFilenamePattern(), $directoryItem->getFilename()) > 0;
    }
    
    
    /**
     *
     * @return string
     */
    private function getCacheFilenamePattern() {        
        return '/[a-z0-9]{32}'.  preg_quote(self::CACHE_FILE_EXTENSION).'/';
    }


    /**
     *
     * @param \HttpRequest $request
     * @return string
     */
    private function getRequestKey(\HttpRequest $request) {
        $key = new \webignition\Http\Request\Key($request);
        return (string)$key;
    }


}