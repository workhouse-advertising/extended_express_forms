<?php
namespace WorkhouseAdvertising\ExtendedExpressForms\Concrete;

use Concrete\Core\Express\ObjectBuilder as CoreObjectBuilder;

class ObjectBuilder extends CoreObjectBuilder
{
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }
}