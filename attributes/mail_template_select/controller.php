<?php

namespace Concrete\Package\ExtendedExpressForms\Attribute\MailTemplateSelect;

use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\Controller as AttributeTypeController;
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
        $value = "";
        if (is_object($this->attributeValue)) {
            $value = $this->app->make('helper/text')->entities($this->getAttributeValue()->getValue());
        }

        $this->set('templates', $this->listMailTemplates());
        $this->set('selected_template', $value);
    }

    public function createAttributeValue($value)
    {
        $av = null;
        if ($value) {
            $av = new TextValue();
            $av->setValue($value);
        }

        return $av;
    }

    //public function getDisplayValue()
    //{
    //}

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        $av = null;
        if (isset($data['mailTemplateSelect'])) {
            $av = new TextValue();
            $av->setValue($data['mailTemplateSelect']);
        }

        return $av;
    }

    public function listMailTemplates() {

        $getTemplateNames = function($item) {
            return $item['template'];
        };

        $noPackageHandle = function($item) { 
            return is_null($item['pkgHandle']);
        };

        $templateList[] = '---';

        // application directory
        if(is_dir(DIR_FILES_EMAIL_TEMPLATES)) {
            foreach(glob(DIR_FILES_EMAIL_TEMPLATES .'/*.php') as $templatePath) {
                $template = pathinfo($templatePath, PATHINFO_FILENAME);
                $templateList['Application'][$template] = $template;
            }
        }

        $installedPkgs = [];
        // packages directories
        foreach(glob(DIR_PACKAGES .'/*', GLOB_ONLYDIR) as $pkgPath) {
            $pkgHandle = basename($pkgPath);
            $installedPkgs[] = $pkgHandle;
            $pkgMailPath = $pkgPath . '/' . DIRNAME_MAIL_TEMPLATES;
            if(is_dir($pkgMailPath)) {
                foreach(glob($pkgMailPath .'/*.php') as $templatePath) {
                    $template = pathinfo($templatePath, PATHINFO_FILENAME);
                    if(!in_array($template, $templateList['Application'])) {
                        $templateList['Package'][$pkgHandle .'/'. $template] = $pkgHandle .'/'. $template;
                    }
                }
            }
        }

        // core packages directories
        foreach(glob(DIR_PACKAGES_CORE .'/*', GLOB_ONLYDIR) as $pkgPath) {
            $pkgHandle = basename($pkgPath);
            if(!in_array($pkgHandle, $installedPkgs)) {
                $pkgMailPath = $pkgPath . '/' . DIRNAME_MAIL_TEMPLATES;
                if(is_dir($pkgMailPath)) {
                    foreach(glob($pkgMailPath .'/*.php') as $templatePath) {
                        $template = pathinfo($templatePath, PATHINFO_FILENAME);
                        if(!in_array($template, $templateList['Application']) && !in_array([ 'template' => $template, 'pkgHandle' => $pkgHandle ], $templateList['Package'])) {
                            $templateList['Package'][$pkgHandle .'/'. $template] = $pkgHandle .'/'. $template;
                        }
                    }
                }
            }
        }

        // core packages directories
        foreach(glob(DIR_FILES_EMAIL_TEMPLATES_CORE .'/*.php') as $templatePath) {
            $template = pathinfo($templatePath, PATHINFO_FILENAME);
            if(!in_array($template, $templateList['Application'])) {
                $templateList['Core'][$template] = $template;
            }
        }

        return $templateList;
    }

}
