<?php

namespace webignition\Http\Header\Field;

/**
 * A header field value
 *
 * @package webignition\Http\Header\Field
 *
 */
class Value {

    private $value = null;

    /**
     *
     * @param string $value
     */
    public function __construct($value = null) {
        $this->set($value);
    }


    /**
     *
     * @return string
     */
    public function get() {
        return $this->value;
    }


    /**
     *
     * @param mixed $value
     * @return bool
     */
    public function set($value = null) {
        return $this->value = $value;
    }


    /**
     *
     * @return string
     */
    public function __toString() {
        return $this->get();
    }

}