<?php

namespace webignition\Http\Header\Field;

/**
 * Factory for producing specific header field objects from raw field names and values
 *
 * @package webignition\Http\Header\Field
 *
 */
class Factory {


    /**
     * Header field names where values assumed to be dates, will be parsed as such
     *
     * @var array
     */
    private static $dateFields = array(
        'date',
        'expires',
        'last-modified'
    );


    /**
     * Header field names where values assumed to be integers
     *
     * @var array
     */
    private static $integerFields = array(
        'content-length',
        'age'
    );


    /**
     *
     * @param string $name
     * @param string $value
     * @return \webignition\Http\Header\Field\CacheControl\CacheControlField
     */
    public static function create($name, $value) {
        $name = strtolower($name);
        
        if ($name == 'cache-control') {
            return new \webignition\Http\Header\Field\CacheControl\CacheControlField($value);
        }
        
        if (self::isIntegerField($name)) {
            return new \webignition\Http\Header\Field\IntegerField($name, $value);
        }

        if (self::isDateField($name)) {
            return new \webignition\Http\Header\Field\DateField($name, $value);
        }
        
        return new \webignition\Http\Header\Field\Field($name, $value);
    }


    /**
     *
     * @param string $name
     * @return bool
     */
    private static function isIntegerField($name) {
        return \in_array($name, self::$integerFields);
    }


    /**
     *
     * @param string $name
     * @return bool
     */
    private static function isDateField($name) {
        return \in_array($name, self::$dateFields);
    }





}