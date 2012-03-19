<?php

namespace webignition\Http\Header\Field\CacheControl;

/**
 * A cache-control header field value
 *
 * @package webignition\Http\Header\Field\CacheControl
 *
 */
class CacheControlValue extends \webignition\Http\Header\Field\Value {

    const CACHE_DIRECTIVE_LIST_SEPARATOR = ',';

    /**
     *
     * @param string $value
     */
    public function __construct($value = null) {
        $this->set($value);
    }


    /**
     * Get collection of \webignition\Http\Header\CacheControl\Directive objects
     *
     * @return array
     */
    public function get() {
        return parent::get();
    }


    /**
     *
     * @param mixed $value
     * @return bool
     */
    public function set($value = null) {
        if (!is_string($value)) {
            return parent::set(array());
        }

        $directives = array();
        $rawDirectives = \explode(self::CACHE_DIRECTIVE_LIST_SEPARATOR, $value);

        foreach ($rawDirectives as $currentRawDirective) {
            $currentDirective = new \webignition\Http\Header\Field\CacheControl\Directive($currentRawDirective);
            $directives[$currentDirective->getName()] = $currentDirective;
        }

        return parent::set($directives);
    }


    /**
     *
     * @return string
     */
    public function __toString() {
        return \implode(self::CACHE_DIRECTIVE_LIST_SEPARATOR.' ', $this->get());
    }


}