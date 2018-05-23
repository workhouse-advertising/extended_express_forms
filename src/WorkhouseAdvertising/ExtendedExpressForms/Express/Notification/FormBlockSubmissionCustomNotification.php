<?php
namespace WorkhouseAdvertising\ExtendedExpressForms\Express\Notification;

use Express;
use Core;
use Request;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Entry\Notifier\Notification\FormBlockSubmissionEmailNotification;
use Concrete\Core\User\UserInfoRepository;
use Doctrine\ORM\EntityManager;

class FormBlockSubmissionCustomNotification extends FormBlockSubmissionEmailNotification
{
    protected function getReplyToEmail(Entry $entry)
    {
        return $this->getFromEmail();
    }

    protected function getToEmail(Entry $entry = null)
    {
        $toEmail = parent::getToEmail();
        if ($entry) {
           $toEmail = parent::getReplyToEmail($entry);
        }
        return $toEmail;
    }

    public function notify(Entry $entry, $updateType)
    {
        $entity = $entry->getEntity();
        $entityHandle = $entity->getHandle();
        $formId = Request::getInstance()->post('express_form_id');
        $form = null;
        if($formId) {
            $form = Core::make(EntityManager::class)->getRepository('Concrete\Core\Entity\Express\Form')->findOneById($formId);
        }
        $customNotificationsObject = Express::getObjectByHandle('form_notification');
        if ($form && $customNotificationsObject) {
            $customNotifications = $customNotificationsObject->getEntries();
            foreach ($customNotifications as $customNotification) {
                if($customNotification->getFormNotificationExpressForm() == $form->getID()) {

                    $title = $customNotification->getFormNotificationTitle();
                    $fromAddress = $customNotification->getFormNotificationFromEmail();
                    $fromName = $customNotification->getFormNotificationFromName();
                    $toEmail = $customNotification->getFormNotificationTo();
                    $bcc = $customNotification->getFormNotificationBcc();
                    $replyTo = $customNotification->getFormNotificationReplyTo();
                    $subject = $customNotification->getFormNotificationSubject();
                    $message = $customNotification->getFormNotificationContent();

                    $templateName = "";
                    $pkgHandle = null;

                    $template = explode('/', $customNotification->getFormNotificationMailTemplate());
                    if(count($template) == 1) {
                        $templateName = $template[0];
                    } else {
                        $pkgHandle = $template[0];
                        $templateName = $template[1];
                    }

                    $content = $message;
                    foreach($this->getAttributeValues($entry) as $attributes) {
                        $handle = $attributes->getAttributeKey()->getAttributeKeyHandle();
                        $value = $attributes->getValue();

                        $content = str_replace('{{'.$handle.'}}', $value, $content);
                        $toEmail = str_replace('{{'.$handle.'}}', $value, $toEmail);
                    }

                    $mh = $this->app->make('mail');
                    $mh->to($toEmail);
                    $mh->from($fromAddress, $fromName);
                    $mh->replyto($replyTo);
                    if ($bcc) {
                        $mh->bcc($bcc);
                    }

                    $mh->addParameter('subject', $subject);
                    $mh->addParameter('attributes', $this->getAttributeValues($entry));
                    $mh->addParameter('content', [ $content ]);
                    $mh->load($templateName, $pkgHandle);
                    if (!$mh->getSubject()) {
                        $mh->setSubject($subject);
                    }
                    $mh->sendMail();
                }
            }
        }
    }
}
