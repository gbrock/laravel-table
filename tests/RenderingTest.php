<?php

namespace Gbrock\Table\Tests;

use Gbrock\Table\Facades\Table;
use Gbrock\Table\Tests\Cases\DatabaseTestCase;
use Gbrock\Table\Tests\Mocks\Game;

class RenderingTest extends DatabaseTestCase
{
    public function test_it_renders_an_html_table()
    {
        Game::create(['name' => 'Rocket League']);

        $rows = Game::all();
        $table = Table::create($rows);

        $rendered = $table->render();

        $this->assertStringStartsWith('<table', $rendered);
        $this->assertStringEndsWith('</table>', $rendered);
    }

    public function test_it_can_add_classes_to_a_column()
    {
        Game::create(['name' => 'Chibi Robo']);

        $rows = Game::all();
        $table = Table::create($rows, false);

        $table->addColumn(['field' => 'id'])
            ->addClass('minimum-width-column')
            ->addClass('id-column');

        $rendered = $table->render();

        $this->assertContains('<th class="minimum-width-column id-column">', $rendered);
        $this->assertContains('<td class="minimum-width-column id-column">', $rendered);
    }
}
