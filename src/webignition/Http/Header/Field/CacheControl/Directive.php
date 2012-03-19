<?php

namespace webignition\Http\Header\Field\CacheControl;

/**
 * A part of a HTTP cache control header value
 *
 * @package webignition\Http\Header\Field\CacheControl
 *
 */
class Directive {

    const SEPARATOR = '=';


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
     * @param string $rawDirective
     */
    public function __construct($rawDirective = '') {
        $this->parseRawDirective($rawDirective);
    }


    /**
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }


    /**
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }


    /**
     *
     * @param string $name
     * @return bool
     */
    public function setName($name) {
        if (!is_string($name)) {
            return false;
        }

        return $this->name = strtolower(trim($name));
    }


    /**
     *
     * @param string $value
     * @return bool
     */
    public function setValue($value = null) {
        if (!is_string($value)) {
            return $this->value = null;
        }

        return $this->value = $value;
    }


    /**
     *
     * @return string
     */
    public function __toString() {
        $string = $this->name;
        if (!is_null($this->value)) {
            $string .= self::SEPARATOR . $this->value;
        }

        return $string;
    }


    /**
     *
     * @param string $rawDirective
     */
    private function parseRawDirective($rawDirective = '') {
        $parser = new \webignition\Http\Header\Field\CacheControl\DirectiveParser($rawDirective);
        $this->setName($parser->getName());
        $this->setValue($parser->getValue());
    }


}