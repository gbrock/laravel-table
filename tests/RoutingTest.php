<?php

namespace Gbrock\Table\Tests;

use Gbrock\Table\Tests\Cases\DatabaseTestCase;
use Gbrock\Table\Tests\Mocks\Game;

class RoutingTest extends DatabaseTestCase {
    public function test_it_accesses_a_table()
    {
        $this->visit('/')
            ->see('<table')
                ->see('<tbody>')
                ->see('</tbody>')
            ->see('</table>');
    }

    public function test_it_sorts_a_table()
    {
        // Seed the database
        Game::create(['name' => 'Super Mario Kart']);
        Game::create(['name' => 'Final Fantasy Tactics']);

        $this->visit('/');

        $this->seeInOrder('Super Mario', 'Final Fantasy');

        $this->visit('/?' . http_build_query([
            'sort'=>'id,desc',
        ]));

        $this->seeInOrder('Final Fantasy', 'Super Mario');
    }
}
