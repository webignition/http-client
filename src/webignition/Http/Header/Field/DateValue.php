<?php

namespace webignition\Http\Header\Field;

/**
 * A date-based header field value
 *
 * @package webignition\Http\Header\Field
 *
 */
class DateValue extends \webignition\Http\Header\Field\Value {


    /**
     *
     * @param string $value
     */
    public function __construct($value = null) {
        $this->set($value);
    }


    /**
     *
     * @return \DateTime
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
            return parent::set();
        }

        return parent::set(new \DateTime($value));
    }


    /**
     *
     * @return string
     */
    public function __toString() {
        return $this->get()->format('D, d M Y H:i:s e');
    }



}