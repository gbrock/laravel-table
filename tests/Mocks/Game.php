<?php

namespace Gbrock\Table\Tests\Mocks;

use Illuminate\Database\Eloquent\Model;
use Jedrzej\Sortable\SortableTrait;

class Game extends Model {

    use SortableTrait;

    protected $fillable = ['name', 'country'];
    protected $sortable = ['id', 'name', 'country'];

    public function sortCountry($query, $direction = false)
    {
        if(!$direction) {
            $direction = 'asc';
        }

        return $query->orderBy('country', $direction);
    }
}
