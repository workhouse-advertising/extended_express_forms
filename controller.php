<?php       

namespace Concrete\Package\ExtendedExpressForms;

use Package;
use BlockType;
use CollectionAttributeKey;
use Concrete\Attribute\Select\Option as SelectAttributeTypeOption;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Doctrine\ORM\Id\UuidGenerator;
use WorkhouseAdvertising\ExtendedExpressForms\Concrete\ObjectBuilder as ObjectBuilderExtended;

class Controller extends Package
{
    protected $pkgHandle = 'extended_express_forms';
    protected $appVersionRequired = '5.7.1';
    protected $pkgVersion = '1.1.0';
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
        $this->app->make('Concrete\Core\Express\Controller\Manager')->setStandardController('\WorkhouseAdvertising\ExtendedExpressForms\Controller\FormController');
        $this->app->bind('helper/validation/strings', '\WorkhouseAdvertising\ExtendedExpressForms\Utility\Service\Validation\Strings');
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

            // Add attributes
            $formNotificationObject->addAttribute('text', 'Title', 'form_notification_title');
            $formNotificationObject->addAttribute('email', 'From Email', 'form_notification_from_email');
            $formNotificationObject->addAttribute('text', 'From Name', 'form_notification_from_name');
            $formNotificationObject->addAttribute('text', 'Subject', 'form_notification_subject');
            $formNotificationObject->addAttribute('textarea', 'Content', 'form_notification_content');
            $formNotificationObject->addAttribute('email', 'Reply To', 'form_notification_reply_to');
            $formNotificationObject->addAttribute('multiple_emails', 'Send To (Leave empty for autoresponder)', 'form_notification_to');
            $formNotificationObject->addAttribute('multiple_emails', 'BCC', 'form_notification_bcc');
            $formNotificationObject->addAttribute('express_form_select', 'Form', 'form_notification_express_form');
            $formNotificationObject->addAttribute('mail_template_select', 'Mail Template', 'form_notification_mail_template');
            $formNotificationObject->addAttribute('image_file', 'Attachment', 'form_notification_attachment');
            $formNotificationObject->save();

            //// TODO: See if DB rollback is automatic or if we need to implement it here
            //// TODO: Automatically create an administration form too
            $form = $formNotificationObject->buildForm('Form');
            $form->addFieldset('Details')
                // ->addTextControl('', 'This is just some basic explanatory text.')
                ->addAttributeKeyControl('form_notification_title')
                ->addAttributeKeyControl('form_notification_from_email')
                ->addAttributeKeyControl('form_notification_from_name')
                ->addAttributeKeyControl('form_notification_reply_to')
                ->addAttributeKeyControl('form_notification_subject')
                ->addAttributeKeyControl('form_notification_content')
                ->addAttributeKeyControl('form_notification_to')
                ->addAttributeKeyControl('form_notification_bcc')
                ->addAttributeKeyControl('form_notification_express_form')
                ->addAttributeKeyControl('form_notification_mail_template')
                ->addAttributeKeyControl('form_notification_attachment');
            $form = $form->save();
            // Set the default forms for the new object
            $formNotificationObject->setDefaultViewForm($form);
            $formNotificationObject->setDefaultEditForm($form);
            $formNotificationObject->save();
        }
    }

    /**
     * Upgrade the add-on
     *
     * @return void
     */
    public function upgrade()
    {
        $formNotificationObject = \Express::getObjectByHandle('form_notification');
        $category = $formNotificationObject->getAttributeKeyCategory();
        $existing = $category->getAttributeKeyByHandle('form_notification_attachment');
        if (!is_object($existing)) {
            $builder = $this->app->make(ObjectBuilderExtended::class);
            $builder->setEntity($formNotificationObject);
            $builder->addAttribute('image_file', 'Attachment', 'form_notification_attachment');
            $builder->save();
            // TODO: Figure out how we can automatcially add the field to the correct fieldset.
            //       Perhaps consider switching this to XML data for import.
            // foreach ($formNotificationObject->getDefaultEditForm()->getFieldSets() as $fieldset) {
            //     $control = new AttributeKeyControl();
            //     $key = new ExpressKey();
            //     $key->setAttributeKeyHandle('form_notification_attachment');
            //     $control->setAttributeKey($key);
            //     $control->setId((new UuidGenerator())->generate($builder->getEntityManager(), $control));
            //     $fieldset->getControls()->add($control);
            //     $entityManager = $builder->getEntityManager();
            //     $entityManager->persist($fieldset);
            //     // $formNotificationObject->getDefaultEditForm()->save();
            //     // Only add the field to the first fieldset
            //     break;
            // }
        }
        parent::upgrade();
    }
}
