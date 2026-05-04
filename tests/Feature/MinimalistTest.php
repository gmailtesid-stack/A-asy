<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MinimalistTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_basic()
    {
        $this->assertTrue(true);
    }
}
