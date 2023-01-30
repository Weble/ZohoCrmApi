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

    /** @var array<string,mixed>  */
    protected array $data = [];
    protected Module $module;

    /**
     * @param array<string,mixed> $data
     */
    public function __construct(array $data, Module $module)
    {
        $this->data = $data;
        $this->module = $module;
    }

    public function getModule(): Module
    {
        return $this->module;
    }

    /** @phpstan-ignore-next-line */
    public function __get(string $name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
    }

    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    /** @phpstan-ignore-next-line */
    public function __call(string $name, array $arguments)
    {
        // add "id" as a parameter
        array_unshift($arguments, $this->getId());

        if (method_exists($this->module, $name)) {
            /** @var callable $callback */
            $callback = [
                $this->module,
                $name,
            ];
            return call_user_func_array($callback, $arguments);
        }
    }

    /**
     * @return array<string,mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return $this->getData();
    }

    public function toJson($options = 0): string|false
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * @return array<string,mixed>
     */
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
