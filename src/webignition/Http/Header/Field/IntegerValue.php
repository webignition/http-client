<?php

namespace webignition\Http\Header\Field;

/**
 * A integer-based header field value
 *
 * @package webignition\Http\Header\Field
 *
 */
class IntegerValue extends \webignition\Http\Header\Field\Value {

    /**
     *
     * @param string $value
     */
    public function __construct($value = null) {
        $this->set($value);
    }


    /**
     *
     * @return int
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
        if (!is_int($value) && !ctype_digit($value)) {
            return parent::set();
        }

        return parent::set((int)$value);
    }


    /**
     *
     * @return string
     */
    public function __toString() {
        return (string)$this->get();
    }

}