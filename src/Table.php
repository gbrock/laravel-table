<?php namespace Gbrock\Table;

use Illuminate\Support\Facades\Input;
use Gbrock\Table\Column;

class Table
{
    protected $models;
    protected $columns;
    protected $view = 'gbrock::table';
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
                $columns = $this->getFieldsFromModels($this->models);
            }

            $this->setColumns($columns);
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
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
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
     * Add one column to the table.
     *
     */
    public function addColumn()
    {
        $model = $this->models->first();

        $new_column = forward_static_call_array([new Column(), 'create'], func_get_args());

        $new_column->setOptionsFromModel($model);

        $this->columns[] =& $new_column;

        return $new_column;
    }

    /**
     * Add multiple columns at a time
     * @param $columns
     * @throws ColumnKeyNotProvidedException
     */
    protected function addColumns($columns)
    {
        $model = $this->models->first();

        if ($columns) {
            foreach ($columns as $key => $field) {
                if (is_numeric($key)) {
                    // Simple non-keyed array passed.
                    $new_column = Column::create($field);
                } else {
                    // Key also matters, apparently
                    $new_column = Column::create($key, $field);
                }

                $new_column->setOptionsFromModel($model);

                $this->columns[] = $new_column;
            }
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
     * Render the table view file.
     * @return array
     */
    public function render()
    {
        $this->appendPaginationLinks();

        return view($this->view, $this->getData())->render();
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
     * @return mixed
     */
    public function getRows()
    {
        return $this->models;
    }

    /**
     * Return current columns.
     * @return mixed
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
     * Overwrite all current rows with the ones passed.
     * @param $models
     */
    public function setModels($models)
    {
        if (!is_object($models)) {
            if (is_array($models)) {
                $models = collect($models);
            }
        }

        if(class_basename($models->first()) == 'stdClass')
        {
            $models = $models->map(function ($array) {
                return new BlankModel($array);
            });
        }

        $this->models = $models;
    }

    /**
     * Remove all currently-set columns.
     */
    private function clearColumns()
    {
        $this->columns = [];
    }

    /**
     * If rows were paginated, add our variables to the pagination query string
     */
    private function appendPaginationLinks()
    {
        if (class_basename($this->models) == 'LengthAwarePaginator') {
            // This set of models was paginated.  Make it append our current view variables.
            $this->models->appends(Input::only(config('gbrock-tables.key_field'),
                config('gbrock-tables.key_direction')));
        }
    }
}
