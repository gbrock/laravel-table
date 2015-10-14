<?php

namespace Gbrock\Table;

use Illuminate\Contracts\Support\Arrayable;

class BlankModel implements Arrayable {
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

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }
}
