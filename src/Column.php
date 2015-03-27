<?php namespace Gbrock\Table;

class Column {
    /** @var string Applicable database field used in sorting */
    private $field;

    /** @var string The default sorting direction */
    private $direction;

    /**
     * @var mixed
     * The rendering method used when generating cell data
     * Can be either a string (the function or view file to be rendered) or a closure accepting the model $row:
     * $column->setRender(function($row){ return '<strong>' . $row->id . '</strong>'; })
     */
    private $render;

    /**
     * Checks if this column is currently being sorted.
     */
    public function isSorted()
    {
        return false;
    }

    /**
     * Generates a URL to toggle sorting by this column.
     */
    public function getSortURL($direction = false)
    {
        if(!$direction)
        {
            // No direction indicated, determine automatically from defaults.
            $direction = $this->getDirection();

            if($this->isSorted())
            {
                // If we are already sorting by this column, swap the direction
                $direction = $direction == 'asc' ? 'desc' : 'asc';
            }
        }

        // Generate and return a URL which may be used to sort this column
        return $this->generateUrl([
            'direction' => $direction,
            'field' => $this->getField(),
        ]);
    }

    /**
     * Returns the default sorting
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }
}
