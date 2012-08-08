<?php

namespace ComponentHub;

use ComponentHub\IAcceptsHub,
    ComponentHub\IAcceptsArgs,
    ComponentHub\Exception,
    Closure;

class Hub
{
    /**
     * @var array
     */
    protected $componentLoaders = array();

    /**
     * @var array
     */
    protected $components = array();

    /**
     * @var callable
     */
    protected $componentValidator = null;

    /**
     * @param string $name
     * @param bool $forceLoad
     * @return bool
     */
    public function hasComponent($name, $forceLoad = false)
    {
        if (array_key_exists($name, $this->componentLoaders)) {
            if ($forceLoad) {
                $this->loadComponent($name);
            }
        }
        return array_key_exists($name, $this->components);
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function getComponent($name, array $args = array())
    {
        if (! array_key_exists($name, $this->components)) {
            $this->loadComponent($name);
        }
        if ($args && ($this->components[$name] instanceof IAcceptsArgs)) {
            $this->components[$name]->setComponentArgs($args);
        }
        return $this->components[$name];
    }

    /**
     * @param bool $forceLoading
     * @return array
     */
    public function getAllComponents($forceLoading = false)
    {
        if ($forceLoading) {
            foreach (array_keys($this->componentLoaders) as $name) {
                $this->loadComponent($name);
            }
        }
        return $this->components;
    }

    /**
     * @param string $name
     * @param Callable $loader
     */
    public function registerComponentLoader($name, $loader)
    {
        $this->componentLoaders[$name] = $loader;
    }

    /**
     * @param Callable $func
     */
    public function setComponentValidator($func)
    {
        $this->componentValidator = $func;
    }

    /**
     * @param mixed $component
     * @return bool
     */
    protected function isValidComponent($component)
    {
        if (is_callable($this->componentValidator)) {
            return (bool) call_user_func($this->componentValidator, $component, $this);
        }
        return true;
    }

    /**
     * @param string $name
     * @throws Exception
     */
    protected function loadComponent($name)
    {
        if (! array_key_exists($name, $this->componentLoaders)) {
            throw new Exception("Missing registered loader for component \"$name\".");
        }
        $loader = $this->componentLoaders[$name];
        unset($this->componentLoaders[$name]);

        if (is_callable($loader)) {
            $component = call_user_func($loader);
        } else {
            throw new Exception("Component loader for \"$name\" was not callable.");
        }

        if (! $component) {
            throw new Exception("Component \"$name\" failed to load.");
        }

        $this->setComponent($name, $component);
    }

    /**
     * @param string $name
     * @param mixed $component
     * @throws Exception
     */
    public function setComponent($name, $component)
    {
        if ($this->isValidComponent($component)) {
            if ($component instanceof IAcceptsHub) {
                /* @var IAcceptsHub $component */
                $component->setComponentHub($this);
            }
            $this->components[$name] = $component;
        } else {
            throw new Exception("The component \"$name\" was invalid.");
        }
    }

    /**
     * @param string $name
     */
    public function removeComponent($name)
    {
        unset($this->components[$name], $this->componentLoaders[$name]);
    }
}
