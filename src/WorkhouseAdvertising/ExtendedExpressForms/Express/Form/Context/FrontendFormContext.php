<?php
namespace WorkhouseAdvertising\ExtendedExpressForms\Express\Form\Context;

use Concrete\Core\Express\Form\Context\FrontendFormContext as CoreFrontendFormContext;

class FrontendFormContext extends CoreFrontendFormContext
{
    public $errors;

    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    public function getAttributeContext()
    {
        return new \WorkhouseAdvertising\ExtendedExpressForms\Attribute\Context\FrontendFormContext();
    }
}