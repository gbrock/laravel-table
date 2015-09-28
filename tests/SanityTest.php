<?php

namespace Gbrock\Table\Tests;

use Gbrock\Table\Facades\Table;
use Gbrock\Table\Tests\Cases\TestCase;

class SanityTest extends TestCase {
    public function test_sanity()
    {
        $table = Table::create(collect([]));
        $this->assertNotEmpty($table);
    }
}
