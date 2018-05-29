<?php

namespace Application\Attribute\Email;

use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\Error\FieldNotPresentError;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\ErrorList\Field\AttributeField;

class Controller extends DefaultController
{
    protected $akTextPlaceholder;
    public $helpers = ['form'];

    public function saveKey($data)
    {
        $type = $this->getAttributeKeySettings();
        $data += array(
            'akTextPlaceholder' => null,
        );
        $akTextPlaceholder = $data['akTextPlaceholder'];

        $type->setPlaceholder($akTextPlaceholder);

        return $type;
    }

    public function form()
    {
        $this->load();
        $value = null;
        
        if (is_object($this->attributeValue)) {
            $value = $this->app->make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        $this->set('value', $value);

        $akTextPlaceholder = '';
        if (isset($this->akTextPlaceholder)) {
            $akTextPlaceholder = $this->akTextPlaceholder;
        }
        $this->set('akTextPlaceholder', $akTextPlaceholder);
    }

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('envelope');
    }

    public function validateForm($data)
    {
        if (!$data['value']) {
            return new FieldNotPresentError(new AttributeField($this->getAttributeKey()));
        } else {
            $fh = $this->app->make('helper/validation/strings');
            if (!$fh->email($data['value'])) {
                return new Error(t('Invalid email address.'), new AttributeField($this->getAttributeKey()));
            } else {
                return true;
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\SimpleTextExportableAttributeInterface::updateAttributeValueFromTextRepresentation()
     */
    public function updateAttributeValueFromTextRepresentation($textRepresentation, ErrorList $warnings)
    {
        $good = true;
        if ($textRepresentation !== '') {
            $vs = $this->app->make('helper/validation/strings');
            /* @var \Concrete\Core\Utility\Service\Validation\Strings $vs */
            if (!$vs->email($textRepresentation)) {
                $good = false;
                $warnings->add(t('"%1$s" is not a valid email address for the attribute with handle %2$s', $textRepresentation, $this->attributeKey->getAttributeKeyHandle()));
            }
        }
        if ($good) {
            $value = parent::updateAttributeValueFromTextRepresentation($textRepresentation, $warnings);
        } else {
            $value = $this->getAttributeValueObject();
        }

        return $value;
    }

    public function type_form()
    {
        $this->load();
    }

    protected function load()
    {
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        $type = $ak->getAttributeKeySettings();
        /**
         * @var $type TextSettings
         */
        $this->akTextPlaceholder = $type->getPlaceholder();
        $this->set('akTextPlaceholder', $type->getPlaceholder());
    }

    public function exportKey($akey)
    {
        $this->load();
        $akey->addChild('type')->addAttribute('placeholder', $this->akTextPlaceholder);

        return $akey;
    }
}
