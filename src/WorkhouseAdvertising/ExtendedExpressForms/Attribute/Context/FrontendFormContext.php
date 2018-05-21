<?php
namespace WorkhouseAdvertising\ExtendedExpressForms\Attribute\Context;

use Concrete\Core\Attribute\Context\FrontendFormContext as BaseFrontendFormContext;
use Concrete\Core\Attribute\View;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Filesystem\TemplateLocator;

class FrontendFormContext extends BaseFrontendFormContext
{

    protected $isRequired;

    // public function setLocation(TemplateLocator $locator)
    // {
    //     $locator->setTemplate('frontend');
    //     return $locator;
    // }

    public function setIsRequired($isRequired)
    {
        $this->isRequired = (bool)$isRequired;
    }

    public function render(Key $key, $value = null)
    {
        if (is_object($value)) {
            $v = new View($value);
        } else {
            $v = new View($key);
        }
        //// Add the required status to the scope items
        $v->addScopeItems([
            'isRequired' => $this->isRequired,
        ]);
        $v->render($this);
    }
}