<?php namespace Gbrock\Table;

use Illuminate\Support\Facades\Input;
use Gbrock\Table\Column;

class Table
{
    protected $models;
    protected $columns;
    protected $view = 'table::default';
    protected $viewVars = [];

    /**
     * @param array $models
     * @param array $columns
     */
    public function __construct($models = [], $columns = [])
    {
        if ($models) {
            $this->setModels($models);

            if (!$columns && $columns !== false) {
                // Columns were not passed and were not prevented from auto-generation; generate them
                $columns = $this->getFieldsFromModels($models);
            }

            if ($columns) {
                $this->setColumns($columns);
            }
        }
    }

    /**
     * Static way to generate a new instance of this class.
     *
     * @param $rows
     * @param mixed $columns
     * @return static
     */
    public function create($rows, $columns = [])
    {
        $table = new static($rows, $columns);

        return $table;
    }

    /**
     * Render the table view file.
     * @return string
     */
    public function render()
    {
        $this->appendPaginationLinks();

        return trim(view($this->view, $this->getData())->render());
    }

    /**
     * Generate the data needed to render the view.
     * @return array
     */
    public function getData()
    {
        return array_merge($this->viewVars, [
            'rows'    => $this->getRows(),
            'columns' => $this->getColumns(),
        ]);
    }

    /**
     * Return current rows.
     * @return Collection
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * Overwrite all current rows with the ones passed.
     * @param $models
     */
    public function setModels($models)
    {
        if (!is_object($models)) {
            if (is_array($models)) {
                foreach ($models as $k => $v) {
                    $models[$k] = new BlankModel($v);
                }
            }

            $models = collect($models);
        }

        $this->models = $models;
    }

    /**
     * Alias for models.
     * @return Collection
     */
    public function getRows()
    {
        return $this->getModels();
    }

    /**
     * Return current columns.
     * @return Collection
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Overwrite all current columns with the ones passed.
     * @param mixed $columns
     */
    public function setColumns($columns)
    {
        $this->clearColumns();
        $this->addColumns($columns);
    }

    /**
     * Add one column to the table.
     *
     * @return Column
     */
    public function addColumn()
    {
        $new_column = forward_static_call_array([new Column, 'create'], func_get_args());

        $new_column->setOptionsFromModel($this->models->first());

        $this->columns[] =& $new_column;

        return $new_column;
    }

    /**
     * Add multiple columns at a time
     * @param $columns
     * @throws ColumnKeyNotProvidedException
     */
    public function addColumns($columns)
    {
        foreach ($columns as $key => $field) {
            if (is_numeric($key)) {
                // Simple non-keyed array passed.
                $new_column = Column::create($field);
            } else {
                // Key passed explicitly
                $new_column = Column::create($key, $field);
            }

            $new_column->setOptionsFromModel($this->models->first());

            $this->columns[] = $new_column;
        }
    }

    /**
     * Returns the name of the set view file.
     *
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Sets the view file used for rendering.
     *
     * @param string $view
     */
    public function setView($view, $vars = true)
    {
        $this->view = $view;
        if (is_array($vars) || !$vars) {
            $this->viewVars = $vars;
        }
    }

    /**
     * Get fields based on a collection of models
     * @param $models
     * @return array
     */
    protected function getFieldsFromModels($models)
    {
        if (!$models->first()) {
            // No models, we can't add any columns.
            return [];
        }

        $model = $models->first();

        // These are the Laravel basic timestamp fields which we don't want to display, by default
        $timestamp_fields = ['created_at', 'updated_at', 'deleted_at'];
        // Grab the basic fields from the first model
        $fields = array_keys($model->toArray());
        // Remove the timestamp fields
        $fields = array_diff($fields, $timestamp_fields);

        if ($model->isSortable) {
            // Add the fields from the model's sortable array
            $fields = array_unique(array_merge($fields, $model->getSortable()));
        }

        return $fields;
    }

    /**
     * Remove all currently-set columns.
     */
    protected function clearColumns()
    {
        $this->columns = [];
    }

    /**
     * If rows were paginated, add our variables to the pagination query string
     */
    protected function appendPaginationLinks()
    {
        if (class_basename($this->models) == 'LengthAwarePaginator') {
            // This set of models was paginated.  Make it append our current view variables.
            $this->models->appends(Input::only(config('gbrock-tables.keys.field'),
                config('gbrock-tables.keys.direction')));
        }
    }
}
