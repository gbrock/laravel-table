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
        if(!in_array($field, $this->sortable))
        {
            return $query;
        }

        // If the direction requested isn't correct, assume ascending
        if($direction !== 'asc' && $direction !== 'desc')
        {
            $direction = 'asc';
        }

        // At this point, all should be well, continue.
        return $query
            ->orderByRaw('ISNULL(' . $field . ')') // MySQL hack to always sort NULLs last
            ->orderBy($field, $direction);
    }

    public function getSortable()
    {
        if(isset($this->sortable))
        {
            return $this->sortable;
        }
    }

    /**
     * Returns the user-requested sorting field or the default for this model.
     * If none is set, returns the primary key.
     *
     * @return string
     */
    protected function getSortingField()
    {
        if(Request::input('sort'))
        {
            // User is requesting a specific column
            return Request::input('sort');
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
        if(Request::input('direction'))
        {
            // User is requesting a specific column
            return Request::input('direction');
        }

        // Otherwise return the primary key
        return 'asc';
    }
}

