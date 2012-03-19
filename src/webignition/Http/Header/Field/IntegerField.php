<?php

namespace webignition\Http\Header\Field;

/**
 * A generic header for fields having integer values
 *
 * @package webignition\Http\Header\Field
 *
 */
class IntegerField extends \webignition\Http\Header\Field\Field {

    /**
     *
     * @return \webignition\Http\Header\Field\IntegerValue
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
        if (!$value instanceof \webignition\Http\Header\Field\IntegerValue) {
            $value = new \webignition\Http\Header\Field\IntegerValue($value);
        }

        return parent::setValue($value);
    }

}