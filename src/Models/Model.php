<?php

namespace Webleit\ZohoCrmApi\Models;

use Webleit\ZohoCrmApi\Contracts\Module;
use Webleit\ZohoCrmApi\Mixins\HasInflector;

/**
 * Class Model
 * @package Webleit\ZohoSignApi\Models
 */
abstract class Model implements \Webleit\ZohoCrmApi\Contracts\Model
{
    use HasInflector;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var Module
     */
    protected $module;

    public function __construct(array $data, Module $module)
    {
        $this->data = $data;
        $this->module = $module;
    }

    public function getModule(): Module
    {
        return $this->module;
    }

    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
    
    function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __call($name, $arguments)
    {
        // add "id" as a parameter
        array_unshift($arguments, $this->getId());

        if (method_exists($this->module, $name)) {
            return call_user_func_array([
                $this->module,
                $name,
            ], $arguments);
        }
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function toArray()
    {
        return $this->getData();
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function isNew(): bool
    {
        return ! $this->getId();
    }

    public function getId(): ?string
    {
        $key = $this->getKeyName();

        return $this->$key ?? null;
    }

    public function getKeyName(): string
    {
        return 'id';
    }

    public function getName(): string
    {
        return $this->inflector()->singularize(strtolower((new \ReflectionClass($this))->getShortName()));
    }
}
