<?php namespace Gbrock\Table;

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
            return $query;
        }

        // If the direction requested isn't correct, assume ascending
        if($dir !== 'asc' && $dir !== 'desc')
        {
            $dir = 'asc';
        }

        // At this point, all should be well, continue.
        return $query
            ->orderByRaw('ISNULL(' . $field . ')') // MySQL hack to always sort NULLs last
            ->orderBy($field, $dir);
    }
}

