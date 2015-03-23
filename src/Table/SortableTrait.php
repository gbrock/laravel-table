<?php namespace Gbrock\Table;

use Request;

trait TableSortable {
    public function scopeSorted($query, $field, $dir = 'asc')
    {
        // If we tried to sort a Model which can't be sorted, fail loudly.
        if(!isset($this->sortable) || !is_array($this->sortable))
        {
            throw new ModelMissingSortableArrayException;
        }

        // If the field requested isn't known to be sortable by our model, fail silently.
        if(!in_array($field, $this->sortable))
        {
            return $query->paginate(
                min(
                    max(10, (int) Request::input('per') // between 10 rows minimum...
                ), 500) // ...and 500 rows maximum
            );
        }

        // If the direction requested isn't correct, assume ascending
        if($dir !== 'asc' && $dir !== 'desc')
        {
            $dir = 'asc';
        }

        // At this point, all should be well, continue.
        return $query
            ->orderByRaw('ISNULL(' . $field . ')')
            ->orderBy($field, $dir)
            ->paginate(
                min(
                    max(10, (int) Request::input('per') // between 10 rows minimum...
                ), 500) // ...and 500 rows maximum
            );
    }
}

