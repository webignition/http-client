<?php

namespace webignition\Http\Header\Field;

/**
 * A generic date-storing header of the name:<date> form, such as Expires,
 * Last-Modified or Date
 *
 * The value of this header is assumed to be a date and will be parsed as such
 * and returned as a DateTime object.
 *
 * @package webignition\Http\Header\Field
 *
 */
class DateField extends \webignition\Http\Header\Field\Field {


    /**
     *
     * @return DateTime
     */
    public function getValue() {
        return parent::getValue();
    }


    /**
     *
     * @param mixed $value
     * @return bool
     */
    public function setValue($value = null) {
        if (!$value instanceof \webignition\Http\Header\Field\DateValue) {
            $value = new \webignition\Http\Header\Field\DateValue($value);
        }

        return parent::setValue($value);
    }


    /**
     *
     * @return string
     */
    public function __toString() {
        return $this->getName().':'.$this->getValue();
    }


}