<?php
namespace WorkhouseAdvertising\ExtendedExpressForms\Controller;

use Events;
use Request;
use Concrete\Core\Application\Application;
use Concrete\Core\Express\Controller\StandardController;
use Concrete\Core\Express\Entry\Notifier\NotificationProviderInterface;
use Concrete\Core\Express\Entry\Notifier\StandardNotifier;
use Concrete\Core\Express\Form\Context\FrontendFormContext as CoreFrontendFormContext;
use Concrete\Core\Express\Form\Processor\ProcessorInterface;
use Concrete\Core\Form\Context\Registry\ContextRegistry;
use WorkhouseAdvertising\ExtendedExpressForms\Express\Form\Context\FrontendFormContext;
use WorkhouseAdvertising\ExtendedExpressForms\Express\Notification\FormBlockSubmissionCustomNotification;

class FormController extends StandardController
{
    protected $request;

    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * Overrides the default frontend form context with a custom one
     * 
     * @return [type] [description]
     */
    public function getContextRegistry()
    {
        return new ContextRegistry([
            CoreFrontendFormContext::class => FrontendFormContext::class
        ]);
    }

    /**
     * Adds a custom form submission notifier to the list of available notifiers
     * 
     * @param  NotificationProviderInterface|null $provider [description]
     * @return [type]                                       [description]
     */
    public function getNotifier(NotificationProviderInterface $provider = null)
    {
        $notifier = parent::getNotifier($provider);
        if ($provider) {
            $notifier->getNotificationList()->addNotification(new FormBlockSubmissionCustomNotification($this->app, $provider));

            $event = new \Symfony\Component\EventDispatcher\GenericEvent();
            $event->setArgument('notifier', $notifier);
            $event->setArgument('provider', $provider);
            Events::dispatch('on_express_notifier_add', $event);
        }
        return $notifier;
    }

    /**
     * Returns a list of errors for a form
     * 
     * @param  [type] $form [description]
     * @return [type]       [description]
     */
    public function getErrorList($form)
    {
        $errorList = $this->app->make('error');
        if ($form && $this->request->post('express_form_id') == $form->getId()) {
            $validator = $this->getFormProcessor()->getValidator($this->request);
            $validator->validate($form, ProcessorInterface::REQUEST_TYPE_ADD);
            $errorList = $validator->getErrorList();
        }
        return $errorList;
    }
}
