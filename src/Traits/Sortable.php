<?php namespace Gbrock\Table\Traits;

use Illuminate\Support\Facades\Request;

trait Sortable {

    public function scopeSorted($query, $field = false, $direction = false)
    {
        if($field === false)
        {
            $field = $this->getSortingField();
        }

        if($direction === false)
        {
            $direction = $this->getSortingDirection();
        }

        // If we tried to sort a Model which can't be sorted, fail loudly.
        if(!isset($this->sortable) || !is_array($this->sortable))
        {
            throw new ModelMissingSortableArrayException;
        }

        // If the field requested isn't known to be sortable by our model, fail silently.
        if(!in_array($field, (array) $this->sortable) && !in_array($field, (array) $this->joined_sortable))
        {
            return $query;
        }

        // If the direction requested isn't correct, grab from config
        if($direction !== 'asc' && $direction !== 'desc')
        {
            $direction = config('gbrock-tables.default_direction');
        }


        if(in_array($field, $this->sortable))
        {
            // At this point, all should be well, continue.
            return $query
                ->orderByRaw('ISNULL(' . $this->getTable() . '.' . $field . ')') // MySQL hack to always sort NULLs last
                ->orderBy($this->getTable() . '.' . $field, $direction);
        }
        elseif(in_array($field, $this->joined_sortable))
        {
            // At this point, all should be well, continue.
            return $query
                ->orderBy($field, $direction);
        }
    }

    public function getSortable()
    {
        if($this->sortable || $this->joined_sortable)
        {
            return array_merge((array) $this->sortable, (array) $this->joined_sortable);
        }

        return [];
    }

    /**
     * Returns the user-requested sorting field or the default for this model.
     * If none is set, returns the primary key.
     *
     * @return string
     */
    public function getSortingField()
    {
        if(Request::input(config('gbrock-tables.key_field')))
        {
            // User is requesting a specific column
            return Request::input(config('gbrock-tables.key_field'));
        }

        // Otherwise return the primary key
        return $this->getKeyName();
    }

    /**
     * Returns the default sorting field for this model.
     * If none is set, returns the primary key.
     *
     * @return string
     */
    protected function getSortingDirection()
    {
        if(Request::input(config('gbrock-tables.key_direction')))
        {
            // User is requesting a specific column
            return Request::input(config('gbrock-tables.key_direction'));
        }

        // Otherwise return the primary key
        return config('gbrock-tables.default_direction');
    }

    public function getIsSortableAttribute()
    {
        return true;
    }
}

