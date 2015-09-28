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

    public function test_custom_sorting_function()
    {
        Game::create(['name' => 'Final Fantasy 3', 'country' => 'us']);
        Game::create(['name' => 'Final Fantasy VI', 'country' => 'jp']);

        // "country" isn't sortable, but corresponds to a custom sorter function
        $rows = Game::sorted('country')->get();

        $this->assertEquals('jp', $rows->first()->getAttribute('country'));
    }
}
