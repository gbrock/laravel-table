<?php namespace Gbrock\Table;

use Illuminate\Support\Facades\Input;

class Table {

    protected $models;
    protected $columns;

    /**
     * @param array $models
     * @param array $columns
     */
    public function __construct($models = [], $columns = [])
    {
        if($models)
        {
            $this->setModels($models);

            if(!$columns)
            {
                // Columns were not passed; generate them
                $columns = $this->getFieldsFromModels($models);
            }

            $this->setColumns($columns);
        }
    }

    /**
     * Static way to generate a new instance of this class.
     *
     * @param $rows
     * @param bool $columns
     * @return static
     */
    public function create($rows, $columns = false)
    {
        $table = new static($rows, $columns);

        return $table;
    }

    /**
     * Add columns based on field names
     * @param $columns
     * @throws ColumnKeyNotProvidedException
     */
    protected function addColumns($columns)
    {
        $model = $this->models->first();

        foreach($columns as $key => $field)
        {
            // For each of our table's basic keys, add a column
            if(is_string($field))
            {
                // This is a basic string column
                $new_column = Column::create($field);
            }
            else
            {
                if(!is_string($key))
                {
                    // This column has extra associations, and must be keyed
                    throw new ColumnKeyNotProvidedException;
                }

                // Set the variables back up properly
                $field_parameters = $field;
                $field = $key;

                // Create the complex column
                $new_column = Column::create($field, $field_parameters);

            }

            $new_column->setOptionsFromModel($model);

            $this->columns[] = $new_column;
        }
    }

    /**
     * Get fields based on a collection of models
     * @param $models
     * @return array
     */
    protected function getFieldsFromModels($models)
    {
        if(!$models->first())
        {
            // No models, we can't add any columns.
            return [];
        }

        $fields = array_keys($models->first()->toArray());
        $timestamp_fields = ['created_at', 'updated_at', 'deleted_at'];

        // only those non-timestamp fields
        return array_diff($fields, $timestamp_fields);
    }

    /**
     * Render the table view file.
     * @return array
     */
    public function render()
    {
        $this->appendPaginationLinks();
        return view('gbrock.tables::table', $this->getViewData())->render();
    }

    /**
     * Generate the data needed to render the view.
     * @return array
     */
    protected function getViewData()
    {
        return [
            'rows' => $this->getRows(),
            'columns' => $this->getColumns(),
        ];
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
        if(class_basename($this->models) == 'LengthAwarePaginator')
        {
            // This set of models was paginated.  Make it append our current view variables.
            $this->models->appends(Input::only('sort', 'direction'));
        }
        else
        {
        }
    }

}
