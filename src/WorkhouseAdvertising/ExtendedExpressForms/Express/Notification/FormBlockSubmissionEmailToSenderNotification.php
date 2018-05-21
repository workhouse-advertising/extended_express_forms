<?php
namespace WorkhouseAdvertising\ExtendedExpressForms\Express\Notification;

use Express;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Entry\Notifier\Notification\FormBlockSubmissionEmailNotification;
use Concrete\Core\User\UserInfoRepository;
use Doctrine\ORM\EntityManager;

class FormBlockSubmissionEmailToSenderNotification extends FormBlockSubmissionEmailNotification
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
        $notificationTriggered = false;
        $form = $entry->getEntity();
        $formHandle = $form->getHandle();
        $customNotificationsObject = Express::getObjectByHandle('form_notification');
        if ($customNotificationsObject) {
            $customNotifications = $customNotificationsObject->getEntries();
            foreach ($customNotifications as $customNotification) {
                foreach ($customNotification->getFormNotificationForms()->getSelectedOptions() as $selectedOption) {
                    if (trim($selectedOption->getSelectAttributeOptionValue()) == trim($formHandle)) {
                        $notificationTriggered = true;
                        $message = (trim($customNotification->getFormNotificationContent()))? $customNotification->getFormNotificationContent() : $this->blockController->thankyouMsg;
                        $subject = (trim($customNotification->getFormNotificationSubject()))? $customNotification->getFormNotificationSubject() : t('Thank you for your enquiry');
                        $fromAddress = (trim($customNotification->getFormNotificationFromEmail()))? trim($customNotification->getFormNotificationFromEmail()) : $this->getFromEmail();
                        $fromName = (trim($customNotification->getFormNotificationFromName()))? trim($customNotification->getFormNotificationFromName()) : null;
                        $toEmail = (trim($customNotification->getFormNotificationTo()))? trim($customNotification->getFormNotificationTo()) : $this->getToEmail($entry);
                        $replyTo = (trim($customNotification->getFormNotificationReplyTo()))? trim($customNotification->getFormNotificationReplyTo()) : $this->getReplyToEmail($entry);
                        $template = (trim($customNotification->getFormNotificationTemplate()))? trim($customNotification->getFormNotificationTemplate()) : 'block_express_form_submission_to_sender';
                        $bcc = (trim($customNotification->getFormNotificationBcc()))? trim($customNotification->getFormNotificationBcc()) : false;
                        $mh = $this->app->make('mail');
                        $mh->to($toEmail);
                        $mh->from($fromAddress, $fromName);
                        $mh->replyto($replyTo);
                        if ($bcc) {
                            $mh->bcc($bcc);
                        }
                        $mh->addParameter('entity', $entry->getEntity());
                        $mh->addParameter('formName', $this->getFormName($entry));
                        $mh->addParameter('attributes', $this->getAttributeValues($entry));
                        $mh->addParameter('message', $message);
                        $mh->load($template);
                        if (!$mh->getSubject()) {
                            $mh->setSubject($subject);
                        }
                        $mh->sendMail();
                    }
                }
            }
        }
        // if ($this->blockController->notifyMeOnSubmission && $this->blockController->replyToEmailControlID) {
        // Only send the default one if a notification hasn't already been triggered
        if ($this->blockController->replyToEmailControlID && !$notificationTriggered) {
            $mh = $this->app->make('mail');
            $mh->to($this->getToEmail($entry));
            $mh->from($this->getFromEmail());
            $mh->replyto($this->getReplyToEmail($entry));
            $mh->addParameter('entity', $entry->getEntity());
            $mh->addParameter('formName', $this->getFormName($entry));
            $mh->addParameter('attributes', $this->getAttributeValues($entry));
            $mh->addParameter('message', $this->blockController->thankyouMsg);
            $mh->load('block_express_form_submission_to_sender');
            if (!$mh->getSubject()) {
                $mh->setSubject(t('Thank you for your enquiry'));
            }
            $mh->sendMail();
        }
    }
}
