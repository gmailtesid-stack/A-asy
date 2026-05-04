<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class ManualMigrationTest extends TestCase
{
    /** @test */
    public function test_manual_migration()
    {
        Artisan::call('migrate:fresh', ['--force' => true]);
        $this->assertTrue(true);
    }
}
