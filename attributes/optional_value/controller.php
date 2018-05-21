<?php

namespace Concrete\Package\ExtendedExpressForms\Attribute\OptionalValue;

use Concrete\Attribute\Text\Controller as BaseController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;

class Controller extends BaseController
{
    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('question');
    }

    public function form()
    {
        parent::form();
        // $this->getView()->setSupportsLabel(false);
    }

    // public function getValidator()
    // {
    //     $validator = \Core::make('Concrete\Core\Attribute\StandardValidator');
    //     return $validator;
    // }

    public function validateForm($value)
    {
        $error = $this->app->make('error');
        $error->add( t('Too many bananas'));
        $error->add( t('And not enough cheese'));
        return $error;
    }

}