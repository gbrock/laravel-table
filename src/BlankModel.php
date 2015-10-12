<?php

namespace Gbrock\Table;

class BlankModel {
    private $attributes;

    public function __construct($attributes)
    {
        $this->attributes = (array) $attributes;
    }

    public function __get($name)
    {
        return array_get($this->attributes, $name, null);
    }

    public function __call($name, $arguments = [])
    {
        return;
    }
}
