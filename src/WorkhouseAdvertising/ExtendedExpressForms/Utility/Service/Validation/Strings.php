<?php
namespace WorkhouseAdvertising\ExtendedExpressForms\Utility\Service\Validation;

use Concrete\Core\Utility\Service\Validation\Strings as CoreStringsValidation;

/**
 * Functions useful for validating strings.
 */
class Strings extends CoreStringsValidation
{
    /**
     * Returns true if the passed string is a valid attribute reference.
     *
     * @param string $em The string to be tested
     * @param bool $testMXRecord Set to true if you want to perform dns record validation for the domain, defaults to false
     * @param bool $strict Strict email validation
     *
     * @return bool
     */
    public function attributeReference($value)
    {
        return preg_match('/^\{\{.*\}\}$/', $value);
    }
}
