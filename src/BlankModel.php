<?php

namespace Gbrock\Table;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class BlankModel implements Arrayable {
    private $attributes;

    public function __construct($attributes)
    {
        $this->attributes = (array) $attributes;
    }

    public function __get($name)
    {
        return Arr::get($this->attributes, $name);
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
