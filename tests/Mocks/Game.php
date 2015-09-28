<?php

namespace Gbrock\Table\Tests\Mocks;

use Gbrock\Table\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class Game extends Model {

    use Sortable;

    protected $fillable = ['name', 'country'];
    protected $sortable = ['id', 'name'];
}
