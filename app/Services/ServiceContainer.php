<?php

namespace App\Services;

use Exception;

class ServiceContainer
{
    private $instances = [];
    private $factories = [];

    public function set(string $name, callable $factory)
    {
        $this->factories[$name] = $factory;
    }

    public function get(string $name)
    {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        if (isset($this->factories[$name])) {
            $this->instances[$name] = call_user_func($this->factories[$name], $this);
            return $this->instances[$name];
        }

        throw new Exception("Service '{$name}' not found in container");
    }
}
