<?php
namespace WorkhouseAdvertising\ExtendedExpressForms\Controller;

use Concrete\Core\Express\Controller\StandardController;
use Concrete\Core\Express\Entry\Notifier\NotificationProviderInterface;
use Concrete\Core\Express\Entry\Notifier\StandardNotifier;
use Concrete\Core\Express\Form\Context\FrontendFormContext as CoreFrontendFormContext;
use Concrete\Core\Form\Context\Registry\ContextRegistry;
use WorkhouseAdvertising\ExtendedExpressForms\Express\Form\Context\FrontendFormContext;
use WorkhouseAdvertising\ExtendedExpressForms\Express\Notification\FormBlockSubmissionCustomNotification;

class FormController extends StandardController
{
    public function getContextRegistry()
    {
        return new ContextRegistry([
            CoreFrontendFormContext::class => new FrontendFormContext()
        ]);
    }

    public function getNotifier(NotificationProviderInterface $provider = null)
    {
        $notifier = parent::getNotifier($provider);
        if ($provider) {
            $notifier->getNotificationList()->addNotification(new FormBlockSubmissionCustomNotification($this->app, $provider));
        }
        return $notifier;
    }

}
