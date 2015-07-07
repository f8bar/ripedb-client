<?php
// MatchedAttribute.php

namespace Dormilich\WebService\RIPE;

class MatchedAttribute extends Attribute
{
    private $regexp;

    /**
     * Object constructor. Attributes that must conform to a pattern are set 
     * to be single-valued.
     * 
     * @param string $name Attribute name.
     * @param boolean $mandatory If the attribute is mandatory/required.
     * @param string $regexp A PCRE regular expression pattern.
     * @return self
     * @throws LogicException RegExp failed the validation test.
     */
    public function __construct($name, $mandatory, $regexp)
    {
        parent::__construct($name, $mandatory, Attribute::SINGLE);
        $this->setRegexp($regexp);
    }

    /**
     * Return the regular expression used to validate the attribute value.
     * 
     * @return string Regular expression pattern.
     */
    public function getRegexp()
    {
        return $this->regexp;
    }

    /**
     * Set and validate the regular expression.
     * 
     * @param string $regexp 
     * @return void
     * @throws LogicException RegExp failed the validation test.
     */
    protected function setRegexp($regexp)
    {
        $this->regexp = (string) $regexp;

        if (false === preg_match($this->regexp, uniqid())) {
            throw new \LogicException('Invalid regular expression', preg_last_error());
        }
    }

    /**
     * Add a regular expression check before saving the value.
     * 
     * @param mixed $value Attribute value.
     * @return string Validated string value.
     * @throws UnexpectedValueException Value does not match pattern.
     */
    protected function getStringValue($value)
    {
        $value = parent::getStringValue($value);

        if (!preg_match($this->regexp, $value)) {
            $msg = sprintf('Invalid value for the [%s] attribute.', $this->name);
            throw new \UnexpectedValueException($msg);
        }
        return $value;
    }
}