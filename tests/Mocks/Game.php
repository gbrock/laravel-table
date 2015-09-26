<?php

namespace Gbrock\Table\Tests\Mocks;

use Illuminate\Database\Eloquent\Model;

class Game extends Model {

    protected $fillable = ['name', 'country'];
}
