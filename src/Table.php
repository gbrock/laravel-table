<?php namespace Gbrock\Table;

use Illuminate\Support\Facades\DB;

class Table {

    protected $models;
    protected $columns;

    public function __construct($models = false, $columns = false)
    {
        if($models)
        {
            $this->setModels($models);

            if(!$columns)
            {
                // Columns were not passed; generate them
                $columns = $this->getColumnsFromModels($models);
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
     * Add a column
     * @param $columns
     */
    protected function addColumns($columns)
    {
        $this->columns =+ $columns;
    }

    /**
     * Get columns based on a collection of models
     * @return array
     */
    protected function getColumnsFromModels($models)
    {
        if(!$models->first())
        {
            // No models, we can't add any columns.
            return [];
        }

        return array_keys($models->first()->toArray());
    }

    /**
     * Render the table view file.
     * @return array
     */
    public function render()
    {
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
        $this->columns = $columns;
    }

    /**
     * Overwrite all current rows with the ones passed.
     * @param $models
     */
    public function setModels($models)
    {
        $this->models = $models;
    }

}
