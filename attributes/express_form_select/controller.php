<?php

namespace Concrete\Package\ExtendedExpressForms\Attribute\ExpressFormSelect;

use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Attribute\Value\Value\TextValue;
use Doctrine\ORM\Query\Expr;

class Controller extends DefaultController
{
    public $helpers = ['form'];

    protected $searchIndexFieldDefinition = array('type' => 'integer', 'options' => array('notnull' => false));

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('list-alt');
    }

    public function form()
    {
        $selectedFormId = "";
        if (is_object($this->getAttributeValue())) {
            $form = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Form')->findOneById($this->getAttributeValue()->getValue());
            if($form) {
                $selectedFormId = $form->getID();
            }
        }

        $expressFormRepository = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Form');
        $expressForms = $expressFormRepository->findAll();
        $selectOptions = [];
        $formFields = [];
        $selectOptions[] = '---';
        foreach ($expressForms as $expressForm) {
            $selectOptions[$expressForm->getID()] = $expressForm->getEntity()->getName() . ' > ' . $expressForm->getName();
            foreach($expressForm->getControls() as $control) {
                if($control instanceof AttributeKeyControl) {
                    $formFields[$expressForm->getID()][] = ($control->getAttributeKey()->getAttributeKeyHandle());
                }
            }
        }

        $this->set('forms', $selectOptions);
        $this->set('form_fields', $formFields);
        $this->set('selected_form', $selectedFormId);
    }

    public function createAttributeValue($formID)
    {
        $av = null;
        $form = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Form')->findOneById($formID);

        if($form) {
            $av = new TextValue();
            $av->setValue($form->getID());
        }

        return $av;
    }

    public function getDisplayValue()
    {
        $value = "";
        if(is_object($this->attributeValue)) {
            $expressFormRepository = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Form');
            $expressForm = $expressFormRepository->findOneById($this->attributeValue->getValue());
            if($expressForm) {
                $value = $expressForm->getEntity()->getName() . ' > ' . $expressForm->getName();
            }
        }
        return $value;
    }

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        $av = null;
        if (isset($data['expressFormSelect'])) {
            $form = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Form')->findOneById($data['expressFormSelect']);
            if($form) {
                $av = new TextValue();
                $av->setValue($form->getID());
            }
        }

        return $av;
    }

}
