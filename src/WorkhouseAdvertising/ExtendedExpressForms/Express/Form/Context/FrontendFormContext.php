<?php
namespace WorkhouseAdvertising\ExtendedExpressForms\Express\Form\Context;

use Concrete\Core\Express\Form\Context\FrontendFormContext as CoreFrontendFormContext;

class FrontendFormContext extends CoreFrontendFormContext
{
    protected $errorList = null;

    /**
     * Overrides the default frontend attribute form context
     * 
     * @return [type] [description]
     */
    public function getAttributeContext()
    {
        $attributContext = new \WorkhouseAdvertising\ExtendedExpressForms\Attribute\Context\FrontendFormContext();
        $attributContext->setForm($this->form);
        $attributContext->setErrorList($this->getErrorList());
        return $attributContext;
    }

    /**
     * Returns the current form controller
     * 
     * @return [type] [description]
     */
    public function getFormController()
    {
        $express = \Core::make('express');
        return ($this->form) ? $express->getEntityController($this->form->getEntity()) : null;
    }

    /**
     * Returns a list of errors for the current form
     * 
     * @return [type] [description]
     */
    public function getErrorList()
    {
        if ($this->errorList === null) {
            $formController = $this->getFormController();
            $this->errorList = ($formController) ? $formController->getErrorList($this->getForm()) : false;
        }
        return $this->errorList;
    }
}