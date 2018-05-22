<?php       

namespace Concrete\Package\ExtendedExpressForms;

use Package;
use BlockType;
use CollectionAttributeKey;
use Concrete\Attribute\Select\Option as SelectAttributeTypeOption;

class Controller extends Package
{
    protected $pkgHandle = 'extended_express_forms';
    protected $appVersionRequired = '5.7.1';
    protected $pkgVersion = '1.0';
    protected $pkgAutoloaderRegistries = array(
        'src/WorkhouseAdvertising/ExtendedExpressForms' => '\WorkhouseAdvertising\ExtendedExpressForms',
    );
    
    public function getPackageDescription()
    {
        return t("Additional features for the Express Forms block");
    }

    public function getPackageName()
    {
        return t("Extended Express Forms");
    }
    
    public function install()
    {
        $package = parent::install();

        $factory = $this->app->make('Concrete\Core\Attribute\TypeFactory');

        $types = [
            'optional_value' => 'Optional Value',
            'multiple_emails' => 'Multiple Emails',
            'express_form_select' => 'Express Form Select',
            'mail_template_select' => 'Mail Template Select',
        ];

        foreach($types as $typeHandle => $typeLabel) {
            $type = $factory->getByHandle($typeHandle);

            if (!is_object($type)) {
                $type = $factory->add($typeHandle, $typeLabel, $package);
            }
        }

        $this->installExpressObjects($package);
    }

    public function on_start()
    {
        $this->app->make('Concrete\Core\Express\Controller\Manager')
                  ->setStandardController('\WorkhouseAdvertising\ExtendedExpressForms\Controller\FormController');
    }

    /**
     * Install the express objects for this package
     * 
     * @param  [type] $package [description]
     * @return void
     */
    protected function installExpressObjects($package)
    {
        // Register Express objects
        // Check for and create the required express objects
        $formNotificationObject = \Express::getObjectByHandle('form_notification');

        //// TODO: Add something to handle updates to custom express objects
        if (!$formNotificationObject) {
            $formNotificationObject = \Express::buildObject('form_notification', 'form_notifications', 'Form Notification', $package);
            // Set up a select multiple settings object
            $formNotificationFormsSettings = new \Concrete\Core\Entity\Attribute\Key\Settings\SelectSettings();
            $formNotificationFormsSettings->setAllowMultipleValues(true);
            $formNotificationFormsSettings->setAllowOtherValues(true);

            $formNotificationTemplateSettings = new \Concrete\Core\Entity\Attribute\Key\Settings\SelectSettings();
            $formNotificationTemplateSettings->setAllowMultipleValues(true);
            $formNotificationTemplateSettings->setAllowOtherValues(true);

            // Add attributes
            $formNotificationObject->addAttribute('text', 'Title', 'form_notification_title');
            $formNotificationObject->addAttribute('email', 'From Email', 'form_notification_from_email');
            $formNotificationObject->addAttribute('text', 'From Name', 'form_notification_from_name');
            $formNotificationObject->addAttribute('text', 'Subject', 'form_notification_subject');
            $formNotificationObject->addAttribute('textarea', 'Content', 'form_notification_content');
            $formNotificationObject->addAttribute('email', 'Reply To', 'form_notification_reply_to');
            $formNotificationObject->addAttribute('multiple_emails', 'Send To (Leave empty for autoresponder)', 'form_notification_to');
            $formNotificationObject->addAttribute('multiple_emails', 'BCC', 'form_notification_bcc');
            $formNotificationObject->addAttribute('select', 'Applicable Forms', 'form_notification_forms', $formNotificationFormsSettings);
            $formNotificationObject->addAttribute('select', 'Template', 'form_notification_template', $formNotificationTemplateSettings);
            $formNotificationObject->save();

            // Add the select options
            //// TODO: Create an attribute that just displays the current available forms rather than having the options set here
            $attributeKey = $formNotificationObject->getAttributeKeyCategory()->getByHandle('form_notification_forms');
            $entityManager = \Core::make('database/orm')->entityManager();
            $expressFormRepository = $entityManager->getRepository('Concrete\Core\Entity\Express\Form');
            $expressForms = $expressFormRepository->findAll();
            $selectOptions = [];
            foreach ($expressForms as $expressForm) {
                $selectOptions[] = $expressForm->getEntity()->getHandle();
                SelectAttributeTypeOption::add($attributeKey, $expressForm->getEntity()->getHandle());
            }
            // $attributeKey = $formNotificationObject->getAttributeKeyCategory()->getByHandle('form_notification_template');
            // $values = ['testing value'];
            // foreach ($values as $val) {
            //     SelectAttributeTypeOption::add($attributeKey, $val);
            // }

            //// TODO: See if DB rollback is automatic or if we need to implement it here
            //// TODO: Automatically create an administration form too
            $form = $formNotificationObject->buildForm('Form');
            $form->addFieldset('Details')
                // ->addTextControl('', 'This is just some basic explanatory text.')
                ->addAttributeKeyControl('form_notification_title')
                ->addAttributeKeyControl('form_notification_from_email')
                ->addAttributeKeyControl('form_notification_from_name')
                ->addAttributeKeyControl('form_notification_subject')
                ->addAttributeKeyControl('form_notification_content')
                ->addAttributeKeyControl('form_notification_forms')
                ->addAttributeKeyControl('form_notification_to')
                ->addAttributeKeyControl('form_notification_reply_to')
                ->addAttributeKeyControl('form_notification_bcc')
                ->addAttributeKeyControl('form_notification_template');
            $form = $form->save();
            // Set the default forms for the new object
            $formNotificationObject->setDefaultViewForm($form);
            $formNotificationObject->setDefaultEditForm($form);
            $formNotificationObject->save();
        }
    }
}
