<?php


namespace Webleit\ZohoCrmApi\Mixins;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;

trait HasInflector
{

    protected ?Inflector $inflector = null;

    public function inflector(): Inflector
    {
        if (! $this->inflector) {
            $this->inflector = InflectorFactory::create()->build();
        }

        return $this->inflector;
    }
}
