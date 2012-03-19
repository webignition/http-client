<?php

namespace webignition\Http\Header\Field;

/**
 * A generic HTTP header field i.e. a single fieldname:fieldvalue pair, commonly
 * a single header line
 *
 * @package webignition\Http\Header\Field
 *
 */
class Field {


    /**
     *
     * @var string
     */
    private $name = null;

    /**
     *
     * @var mixed
     */
    private $value = null;


    /**
     *
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value = null) {
        $this->setName($name);
        $this->setValue($value);
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
     * @return \webignition\Http\Header\Field\Value
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
     * @param mixed $value
     * @return bool
     */
    public function setValue($value = null) {
        if (!$value instanceof \webignition\Http\Header\Field\Value) {
            $value = new \webignition\Http\Header\Field\Value($value);
        }
        
        return $this->value = $value;
    }


    /**
     *
     * @return string
     */
    public function __toString() {
        return $this->getName().':'.$this->getValue();
    }


}