<?php

namespace Zhuzhichao\LaravelAdvancedSearch;

class ModelScope
{
    /**
     * Class name.
     *
     * @var
     */
    protected $className;

    /**
     * Scope method name.
     *
     * @var
     */
    private $scopeName;

    /**
     * Args.
     *
     * @var array
     */
    private $args;

    public function __construct($scopeName, ...$args)
    {
        $this->scopeName = $scopeName;
        $this->args = $args;
    }

    /**
     * @return mixed
     */
    public function getScopeName()
    {
        return $this->scopeName;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param mixed $className
     * @return ModelScope
     */
    public function setClassName($className): self
    {
        $this->className = $className;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->className;
    }
}
