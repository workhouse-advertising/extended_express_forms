<?php

namespace Concrete\Package\ExtendedExpressForms\Attribute\MultipleEmails;

use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\Error\FieldNotPresentError;
use Concrete\Core\Error\ErrorList\Field\AttributeField;
use Concrete\Core\Entity\Attribute\Value\Value\TextValue;

class Controller extends DefaultController
{
    public $helpers = ['form'];

    public function form()
    {
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->app->make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        $this->set('value', explode(',', $value));
    }

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('envelope');
    }

    public function validateForm($data)
    {
        foreach($data['value'] as $value) {
            if (!$value) {
                return new FieldNotPresentError(new AttributeField($this->getAttributeKey()));
            } else {
                $fh = $this->app->make('helper/validation/strings');
                if (!$fh->email($value)) {
                    return new Error(t('Invalid email address.'), new AttributeField($this->getAttributeKey()));
                } else {
                    return true;
                }
            }
        }
    }

    public function createAttributeValue($value) {
        $output = "";
        if ($value != null) {
            if (is_array($value)) {
                foreach ($value as $email) {
                    if(!empty($email)) {
                        $output .= $email .',';
                    }
                }
                $output = rtrim($output,",");
            } else {
                $output = $value;
            }
        }

        $av = new TextValue();
        $av->setValue($output);

        return $av;
    }
}
