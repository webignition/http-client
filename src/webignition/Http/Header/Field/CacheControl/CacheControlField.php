<?php

namespace webignition\Http\Header\Field\CacheControl;

/**
 * A HTTP Cache-Control header
 *
 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9
 *
 * @package webignition\Http\Header\Field\CacheControl
 *
 */
class CacheControlField extends \webignition\Http\Header\Field\Field {

    /**
     *
     * @param string $value
     */
    public function __construct($value = '') {       
        parent::__construct('cache-control', $value);
    }


    /**
     *
     * @param string $value
     */
    public function setValue($value = '') {
        if (!$value instanceof \webignition\Http\Header\Field\CacheControl\CacheControlValue) {
            $value = new \webignition\Http\Header\Field\CacheControl\CacheControlValue($value);
        }

        return parent::setValue($value);
    }


    /**
     * Get collection of \webignition\Http\Header\CacheControl\Directive objects
     *
     * @return array
     */
    public function getValue() {
        return parent::getValue();
    }


    /**
     *
     * @param string $name
     * @return bool
     */
    public function hasDirective($name) {
        if (!is_string($name)) {
            return false;
        }

        $directives = $this->getValue()->get();
        if (!isset($directives[$name])) {
            return false;
        }

        return $directives[$name] instanceof \webignition\Http\Header\Field\CacheControl\Directive;
    }


    /**
     *
     * @param string $name
     * @return \webignition\Http\Header\Field\CacheControl\Directive
     */
    public function getDirective($name) {
        if (!$this->hasDirective($name)) {
            return null;
        }

        $directives = $this->getValue()->get();

        return $directives[$name];
    }

    
    
    /**
     *
     * @return bool
     */
    public function isPrivate() {
        return ($this->hasDirective('private'));
    }    


    /**
     *
     * @return bool
     */
    public function isPublic() {
        return !$this->isPrivate();
    }


    /**
     *
     * @return bool
     */
    public function isNoCache() {
        return ($this->hasDirective('no-cache'));
    }


    /**
     * Is the whole response marked as being not storeable?
     *
     * @return bool
     */
    public function isNoStore() {
        return ($this->hasDirective('no-store'));
    }


    /**
     * Get the maximum age, in seconds, that the response is to be cached
     * relative to the Date header
     *
     * Note: The HTTP/1.1 spec doesn't define what the relative expiration
     *       time is if there is no max-age directive.
     *
     * Note: a Cache-Control max-age directive supersedes an Expires header
     *
     * @return int
     */
    public function getMaxAgeDelta() {
        if ($this->hasDirective('s-maxage')) {
            return (int)$this->getDirective('s-maxage')->getValue();
        }

        if ($this->hasDirective('max-age')) {
            return (int)$this->getDirective('max-age')->getValue();
        }

        return 0;
    }


    /**
     * Remove all cache control directives from this header
     *
     * @return bool
     */
    public function clear() {
        return $this->directives = array();
    }
    
}


/**
 * Reference
 *
    Cache-Control   = "Cache-Control" ":" 1#cache-directive

    cache-directive = cache-request-directive
         | cache-response-directive

    cache-request-directive =
           "no-cache"                          ; Section 14.9.1
         | "no-store"                          ; Section 14.9.2
         | "max-age" "=" delta-seconds         ; Section 14.9.3, 14.9.4
         | "max-stale" [ "=" delta-seconds ]   ; Section 14.9.3
         | "min-fresh" "=" delta-seconds       ; Section 14.9.3
         | "no-transform"                      ; Section 14.9.5
         | "only-if-cached"                    ; Section 14.9.4
         | cache-extension                     ; Section 14.9.6

     cache-response-directive =
           "public"                               ; Section 14.9.1
         | "private" [ "=" <"> 1#field-name <"> ] ; Section 14.9.1
         | "no-cache" [ "=" <"> 1#field-name <"> ]; Section 14.9.1
         | "no-store"                             ; Section 14.9.2
         | "no-transform"                         ; Section 14.9.5
         | "must-revalidate"                      ; Section 14.9.4
         | "proxy-revalidate"                     ; Section 14.9.4
         | "max-age" "=" delta-seconds            ; Section 14.9.3
         | "s-maxage" "=" delta-seconds           ; Section 14.9.3
         | cache-extension                        ; Section 14.9.6

    cache-extension = token [ "=" ( token | quoted-string ) ]
 */