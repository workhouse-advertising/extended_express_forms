<?php       

namespace Concrete\Package\ExtendedExpressForms;
use Package;
use BlockType;

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
        $pkg = parent::install();
        $factory = $this->app->make('Concrete\Core\Attribute\TypeFactory');
        $type = $factory->getByHandle('optional_value');
        if (!is_object($type)) {
            $type = $factory->add('optional_value', 'Optional Value', $pkg);
        }
    }
}