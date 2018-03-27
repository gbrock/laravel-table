<?php namespace Gbrock\Table\Traits;

use Illuminate\Support\Facades\Request;

trait Sortable {

    public function scopeSorted($query, $field = false, $direction = false)
    {
        $field = $this->getSortingField($field);
        $direction = $this->getSortingDirection(strtolower($direction));

        if(
            !isset($this->sortable) || // are sortables present?
            !is_array($this->sortable) // are the sortables an array?
        )
        {
            // If we tried to sort a Model which can't be sorted, fail loudly.
            throw new ModelMissingSortableArrayException;
        }

        // The name of the custom function (which may or may not exist) which sorts this field
        $sortFunctionName = 'sort' . studly_case($field);

        // does $field appear as a VALUE in list of known sortables?
        $isValueOfSortable = in_array($field, (array) $this->sortable);
        // does $field appear as a KEY in list of known sortables?
        $isKeyOfSortable = isset($this->sortable[$field]);
        // is there a custom function for sorting this column?
        $isCallableFunction = method_exists($this, $sortFunctionName);

        if(!$isValueOfSortable && !$isKeyOfSortable && !$isCallableFunction)
        {
            // If the field requested isn't known to be sortable by our model, fail silently.
            return $query;
        }

        // If the direction requested isn't correct, grab from config
        if($direction !== 'asc' && $direction !== 'desc')
        {
            $direction = config('gbrock-tables.default_direction');
        }

        if($isCallableFunction)
        {
            // Call custom function and return immediately
            return call_user_func([$this, $sortFunctionName], $query, $direction);
        }

        // By default assume the $field is a member of the $sortable array
        $sortField = strpos($field, '.') === FALSE ? $this->getTable() . '.' . $field : $field;

        if($isKeyOfSortable)
        {
            // Set via key
            $sortField = $this->sortable[$field];
        }

        // At this point, all should be well, continue.
        return $query
            ->orderBy($sortField, $direction);
    }

    public function getSortable()
    {
        if($this->sortable)
        {
            return (array) $this->sortable;
        }

        return [];
    }

    /**
     * Returns the user-requested sorting field or the default for this model.
     * If none is set, returns the primary key.
     *
     * @return string
     */
    public function getSortingField($field=false)
    {
        if(Request::input(config('gbrock-tables.key_field')))
        {
            // User is requesting a specific column
            return Request::input(config('gbrock-tables.key_field'));
        }
        elseif($field !== false)
        {
            // Merge manually set sort field into request, so view column indicators work
            Request::merge([config('gbrock-tables.key_field') => $field]);

            // Specific field passed to sortable() method
            return $field;
        }
        else
        {
            // Otherwise return the primary key
            return $this->getKeyName();
        }
    }

    /**
     * Returns the default sorting field for this model.
     * If none is set, returns the primary key.
     *
     * @return string
     */
    protected function getSortingDirection($direction=false)
    {
        if(Request::input(config('gbrock-tables.key_direction')))
        {
            // User is requesting a specific column
            return Request::input(config('gbrock-tables.key_direction'));
        }
        elseif($direction !== false)
        {
            // Merge manually set sort direction into request, so view column indicators work
            Request::merge([config('gbrock-tables.key_direction') => $direction]);

            // Specific direction passed to sortable() method
            return $direction;
        }
        else
        {
            // Otherwise return the primary key
            return config('gbrock-tables.default_direction');
        }

    }

    public function getIsSortableAttribute()
    {
        return true;
    }
}
