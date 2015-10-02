<?php

namespace Gbrock\Table\Tests;

use Gbrock\Table\Tests\Cases\TestCase;

class RoutingTest extends TestCase {
    public function test_it_accesses_a_route()
    {
        $this->visit('/')
            ->see('awesome')
            ->dontSee('failure');
    }
}
