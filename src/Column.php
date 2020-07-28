<?php namespace Gbrock\Table;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;

class Column
{
    /** @var Model The base model from which we can gather certain options */
    protected $model;
    /** @var string Applicable database field used in sorting */
    protected $field;
    /** @var string The default sorting direction */
    protected $direction;
    /** @var string The visible portion of the column header */
    protected $label;
    /** @var bool Whether this column can be sorted by the user */
    protected $sortable = false;
    /** @var array The CSS classes applied to the column */
    protected $classes = [];
    /**
     * @var closure
     * A rendering closure used when generating cell data, accepts the model:
     * $column->setRenderer(function($model){ return '<strong>' . $model->id . '</strong>'; })
     */
    protected $renderer;

    public static function create()
    {
        $args = func_get_args();

        $class = new static;

        // Detect instantiation scheme
        switch (count($args)) {
            case 1: // one argument passed
                if (is_string($args[0])) {
                    // Only the field was passed
                    $class->setField($args[0]);
                    $class->setLabel(ucwords(str_replace('_', ' ', $args[0])));
                } elseif (is_array($args[0])) {
                    // Just an array was sent; set the parameters.
                    $class->setParameters($args[0]);
                }
                break;
            case 2: // two arguments
                if (is_string($args[0]) && is_string($args[1])) {
                    // Both are strings, this is a Field => Label affair.
                    $class->setField($args[0]);
                    $class->setLabel($args[1]);
                } elseif (is_string($args[0]) && is_array($args[1])) {
                    // Normal complex initialization: field and quick parameters
                    $class->setField($args[0]);
                    $class->setParameters($args[1]);
                    if (!isset($args[1]['label'])) {
                        $class->setLabel(ucwords(str_replace('_', ' ', $args[0])));
                    }
                }
                break;
            case 3: // three arguments
                if (is_string($args[0]) && is_string($args[1]) && is_callable($args[2])) {
                    // Field, Label, and [rendering] Closure.  Standard View addition.
                    $class->setField($args[0]);
                    $class->setLabel($args[1]);
                    $class->setRenderer($args[2]);
                }
                break;
        }

        return $class;
    }

    /**
     * Sets some common-sense options based on the underlying data model.
     *
     * @param Model $model
     * @return $this
     */
    public function setOptionsFromModel($model)
    {
        if (!$model) {
            return $this;
        }

        if ($model->is_sortable && in_array($this->getField(), $model->getSortable())) {
            // The model dictates that this column should be sortable
            $this->setSortable(true);
        }

        $this->model = $model;

        return $this;
    }

    /**
     * Checks if this column is currently being sorted.
     */
    public function isSorted()
    {
        if (Request::input(config('gbrock-tables.key_field')) == $this->getField()) {
            return true;
        }

        if (!Request::input(config('gbrock-tables.key_field')) && $this->model && $this->model->getSortingField() == $this->getField()) {
            // No sorting was requested, but this is the default field.
            return true;
        }

        return false;
    }

    /**
     * Generates a URL to toggle sorting by this column.
     */
    public function getSortURL($direction = false)
    {
        if (!$direction) {
            // No direction indicated, determine automatically from defaults.
            $direction = $this->getDirection();

            if ($this->isSorted()) {
                // If we are already sorting by this column, swap the direction
                $direction = $direction == 'asc' ? 'desc' : 'asc';
            }
        }

        // Generate and return a URL which may be used to sort this column
        return $this->generateUrl(array_filter([
            config('gbrock-tables.key_field')     => $this->getField(),
            config('gbrock-tables.key_direction') => $direction,
        ]));
    }

    /**
     * Returns the default sorting
     * @return string
     */
    public function getDirection()
    {
        if ($this->isSorted()) {
            // If the column is currently being sorted, grab the direction from the query string
            $this->direction = Request::input(config('gbrock-tables.key_direction'));
        }

        if (!$this->direction) {
            $this->direction = config('gbrock-tables.default_direction');
        }

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
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSortable()
    {
        return $this->sortable;
    }

    /**
     * @param boolean $sortable
     * @return $this
     */
    public function setSortable($sortable)
    {
        $this->sortable = (bool) $sortable;

        return $this;
    }

    public function generateUrl($parameters = [])
    {
        // Generate our needed parameters
        $parameters = array_merge($this->getCurrentInput(), $parameters);

        // Grab the current URL
        $path = URL::getRequest()->path();

        return url($path . '/?' . http_build_query($parameters));
    }

    protected function getCurrentInput()
    {
        return Request::only([
            config('gbrock-tables.key_field')     => Request::input(config('gbrock-tables.key_field')),
            config('gbrock-tables.key_direction') => Request::input(config('gbrock-tables.key_direction')),
        ]);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function setParameters($arguments)
    {
        foreach ($arguments as $k => $v) {
            $this->{'set' . ucfirst($k)}($v);
        }

        return $this;
    }

    /**
     * @param string $direction
     * @return $this
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    public function render($data)
    {
        if ($this->hasRenderer()) {
            $renderer = $this->renderer;

            return $renderer($data);
        }
    }

    public function hasRenderer()
    {
        return ($this->renderer != null);
    }

    public function setRenderer($function)
    {
        if (!is_callable($function)) {
            throw new CallableFunctionNotProvidedException;
        }

        $this->renderer = $function;

        return $this;
    }

    public function addClass($class)
    {
        $this->classes[] = $class;

        return $this;
    }
    
    public function setClasses($class)
    {
        $this->classes = explode(" ", $class);

        return $this;
    }

    /**
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @return array
     */
    public function getClassString()
    {
        return implode(' ', array_filter($this->classes));
    }
}
