<?php

namespace Gbrock\Table\Tests;

use Gbrock\Table\Facades\Table;
use Gbrock\Table\Tests\Cases\DatabaseTestCase;
use Gbrock\Table\Tests\Mocks\Game;

class CreationTest extends DatabaseTestCase
{
    public function test_it_can_add_a_column()
    {
        Game::create(['name' => 'Terraria']);

        $rows = Game::all();
        $table = Table::create($rows, false);

        $table->addColumn('id');

        $this->assertCount(1, $table->getColumns());
    }
}
