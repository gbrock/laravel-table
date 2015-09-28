<?php

namespace Gbrock\Table\Tests;

use Gbrock\Table\Tests\Cases\DatabaseTestCase;
use Gbrock\Table\Tests\Mocks\Game;

class SortingTest extends DatabaseTestCase {
    public function test_it_can_sort_keys()
    {
        Game::create(['name' => 'Super Mario Bros.']);
        Game::create(['name' => 'Super Mario Bros. 2']);

        $rows = Game::get();

        $this->assertEquals('Super Mario Bros.', array_get($rows->get(0), 'name'));

        $rows = Game::sorted('id', 'desc')->get();

        $this->assertEquals('Super Mario Bros. 2', array_get($rows->get(0), 'name'));
    }
}
