<?php

namespace webignition\Http\Header\Field\CacheControl;

/**
 * Parses a raw cache control directive
 *
 * @package webignition\Http\Header\Field\CacheControl
 *
 */
class DirectiveParser {

    /**
     *
     * @var string
     */
    private $rawDirective = null;


    /**
     *
     * @var string
     */
    private $name = null;

    
    /**
     *
     * @var string
     */
    private $value = null;


    /**
     *
     * @var bool
     */
    private $hasParsed = false;

    /**
     *
     * @param string $rawDirective
     */
    public function __construct($rawDirective = '') {
        $this->rawDirective = $rawDirective;
    }

    /**
     *
     * @return string
     */
    public function getName() {
        if (!$this->hasParsed()) {
            $this->parseRawDirective();
        }

        return $this->name;
    }


    /**
     *
     * @return string
     */    
    public function getValue() {
        if (!$this->hasParsed()) {
            $this->parseRawDirective();
        }

        return $this->value;
    }
    

    /**
     *
     * @return bool
     */
    private function hasParsed() {
        return $this->hasParsed;
    }
    

    /**
     * Populate name and value from raw cache control value part
     *
     * @return bool
     */
    private function parseRawDirective() {
        $this->hasParsed = true;
        
        if (!$this->rawDirectiveHasNameAndValue()) {
            return $this->name = trim($this->rawDirective);
        }

        $parts = \explode(\webignition\Http\Header\Field\CacheControl\Directive::SEPARATOR, $this->rawDirective);
        $this->name = trim($parts[0]);
        $this->value = $parts[1];

        return true;
    }
    

    /**
     *
     * @return bool
     */
    private function rawDirectiveHasNameAndValue() {
        return \substr_count($this->rawDirective, \webignition\Http\Header\Field\CacheControl\Directive::SEPARATOR) > 0;
    }

    
}