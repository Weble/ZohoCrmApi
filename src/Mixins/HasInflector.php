<?php


namespace Webleit\ZohoCrmApi\Mixins;


use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;

trait HasInflector
{
    /**
     * @var \Doctrine\Inflector\Inflector
     */
    protected $inflector = null;

    public function inflector(): Inflector
    {
        if (!$this->inflector) {
            $this->inflector = InflectorFactory::create()->build();
        }

        return $this->inflector;
    }
}
