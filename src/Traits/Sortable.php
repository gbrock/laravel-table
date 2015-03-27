<?php namespace Gbrock\Table\Traits;

trait Sortable {
    public function scopeSorted($query, $field = false, $dir = 'asc')
    {
        if($field === false)
        {
            $field = $this->getDefaultField();
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
        if($dir !== 'asc' && $dir !== 'desc')
        {
            $dir = 'asc';
        }

        // At this point, all should be well, continue.
        return $query
            ->orderByRaw('ISNULL(' . $field . ')') // MySQL hack to always sort NULLs last
            ->orderBy($field, $dir);
    }

    /**
     * Returns the default sorting field for this model.
     * If none is set, returns the primary key.
     *
     * @return string
     */
    protected function getDefaultField()
    {
        return $this->getKeyName();
    }
}

