<?php

namespace webignition\Http\Mock\Request\PostFields;

/**
 * Translate a collection of POST request fields into a hash unique to that 
 * collection 
 */
class Hash {
   
    /**
     *
     * @var array
     */
    private $postFields = array();    
    
    /**
     *
     * @param type $postFields
     * @return \webignition\Http\Mock\Request\PostFields\Hash 
     */
    public function setPostFields($postFields) {
        $this->postFields = $postFields;
        return $this;
    }
    
    
    /**
     *
     * @return string
     */
    public function getHash() {
        if (!is_array($this->postFields)) {
            return '';
        }

        $postFieldContent = '';
        
        foreach ($this->postFields as $key => $value) {
            $postFieldContent .= urlencode($key) . '=' . urlencode($value);
        }
        
        return md5($postFieldContent);        
    }
    
}