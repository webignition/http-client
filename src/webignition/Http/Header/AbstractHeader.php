<?php

namespace webignition\Http\Header;

/**
 * Easy-access to the fields of an HTTP header
 *
 * @package webignition\Http\Header
 *
 */
abstract class AbstractHeader {

    const CACHE_CONTROL_FIELD_NAME = 'cache-control';

    /**
     * Internal set of \webignition\Http\Header\Field\Field objects
     *
     * @var array
     */
    private $fields = array();


    /**
     * "Hop-by-hop headers, which are meaningful only for a single
     *  transport-level connection, and are not stored by caches
     *  or forwarded by proxies."
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html#sec13.5.1
     *
     * @var array
     */
    private $hopByHopFields = array(
        'connection',
        'keep-alive',
        'proxy-authenticate',
        'proxy-authorization',
        'te',
        'trailers',
        'transfer-encoding',
        'upgrade'

    );



    /**
     * Populate this header object from an array of raw header fields
     *
     * @param array $rawHeaders
     * @return bool
     */
    public function populate(array $rawHeaders) {
        $this->fields = array();
        foreach ($rawHeaders as $fieldName => $rawFieldValue) {
            $this->parseRawHeader($fieldName, $rawFieldValue);
        }

        return true;
    }


    /**
     *
     * @param string $name
     * @return bool
     */
    public function hasField($name = null) {
        if (!\is_string($name)) {
            return false;
        }

        $name = \strtolower(\trim($name));
        return isset($this->fields[$name]);
    }


    /**
     *
     * @param string $name
     * @return \webignition\Http\Header\Field\Field
     */
    public function getField($name) {
        if (!$this->hasField($name)) {
            return null;
        }

        return $this->fields[$name];
    }


    /**
     *
     * @return \webignition\Http\Header\Field\CacheControl\CacheControlField 
     */
    public function getCacheControlField() {
        if ($this->hasField('cache-control')) {
            return $this->getField('cache-control');
        }

        return new \webignition\Http\Header\Field\CacheControl\CacheControlField();
    }


    /**
     *
     * @param string $name
     * @return \webignition\Http\Header\Field\DateField
     */
    public function getDateField($name) {
        if (!$this->hasField($name)) {
            return new \webignition\Http\Header\Field\DateField();
        }

        if (!$this->getField($name) instanceof \webignition\Http\Header\Field\DateField) {
            $this->setField(new \webignition\Http\Header\Field\DateField($name, $this->getField($name)->getValue()->get()));
        }

        return $this->getField($name);
    }


    /**
     *
     * @param \webignition\Http\Header\Field\Field $field
     * @return bool
     */
    public function setField(\webignition\Http\Header\Field\Field $field) {
        return $this->fields[strtolower($field->getName())] = $field;
    }
    
    
    public function toArray() {
        $return = array();

        foreach ($this->fields as $currentField) {
            /* @var $currentField \webignition\Http\Header\Field\Field  */
            $return[$currentField->getName()] = $currentField->getValue().'';
        }

        return $return;
    }


    public function __toString() {
        $return = '';

        foreach ($this->fields as $currentField) {
            /* @var $currentField \webignition\Http\Header\Field\Field  */
            $return .= $currentField->getName().':'.$currentField->getValue()."\n";
        }

        return trim($return);
    }


    /**
     * List of field names that are relevant only per-hop
     * These should not be stored in a cache or passed on by a proxy
     *
     * @return array
     */
    public function getHopByHopFields() {
        return $this->hopByHopFields;
    }


    /**
     *
     * @param string $fieldName
     * @param string $rawFieldValue
     * @return bool
     */
    private function parseRawHeader($fieldName, $rawFieldValue) {
        return $this->fields[\strtolower($fieldName)] = \webignition\Http\Header\Field\Factory::create($fieldName, $rawFieldValue);
    }






}