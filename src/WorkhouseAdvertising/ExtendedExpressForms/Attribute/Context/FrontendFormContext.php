<?php
namespace WorkhouseAdvertising\ExtendedExpressForms\Attribute\Context;

use Concrete\Core\Attribute\Context\FrontendFormContext as BaseFrontendFormContext;
use Concrete\Core\Attribute\View;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Filesystem\TemplateLocation;

class FrontendFormContext extends BaseFrontendFormContext
{
    /**
     * [$errorList description]
     * @var [type]
     */
    protected $errorList;

    /**
     * [$form description]
     * @var [type]
     */
    protected $form;

    /**
     * Add the package template location and set the template
     * //// TODO: Consider making this optional and dynamically setting the template based on the theme's selected grid layout
     * 
     * @param TemplateLocator $locator [description]
     */
    public function setLocation(TemplateLocator $locator)
    {
        $locator->addLocation((new TemplateLocation('elements/form', 'extended_express_forms')));
        $locator->setTemplate('bootstrap3_extended');
        return $locator;
    }

    /**
     * [render description]
     * @param  Key    $key   [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function render(Key $key, $value = null)
    {
        if (is_object($value)) {
            $v = new View($value);
        } else {
            $v = new View($key);
        }
        // Add the required status to the scope items
        //// TODO: Add other scope items that we might require
        $v->addScopeItems([
            'required' => $this->getIsRequired($key),
            'errors' => $this->getFieldErrors($key),
        ]);
        $v->render($this);
    }

    /**
     * Set the current form
     * //// TODO: Test this context to make sure that it's compatible with nultiple forms.
     *            If a single instance is used on each page it will cause issues as the error list and form may be incorrect.
     * @param [type] $form [description]
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * [setErrorList description]
     * @param [type] $errorList [description]
     */
    public function setErrorList($errorList)
    {
        return $this->errorList = $errorList;
    }

    /**
     * Returns an array af errors for a given attribute key
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function getFieldErrors($key)
    {
        $fieldErrors = [];
        $keyName = $key->getAttributeKeyName();
        $keyHandle = $key->getAttributeKeyHandle();
        if ($this->errorList) {
            foreach ($this->errorList->getList() as $error) {
                if ($error->getField()) {
                    $fieldKey = $error->getField()->getFieldElementName();
                    if ($fieldKey == $keyName || $fieldKey == $keyHandle) {
                        $fieldErrors[] = $error->getMessage();
                    }
                }
            }
        }
        return $fieldErrors;
    }

    /**
     * [getIsRequired description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function getIsRequired($key)
    {
        //// TODO: Correctly fetch if a field is required
        return false;
    }
}