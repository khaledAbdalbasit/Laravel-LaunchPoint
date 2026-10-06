<?php

namespace Tests\Unit;

use Tests\TestCase;

class HealthCommandTest extends TestCase
{
    public function test_health_command_executes()
    {
        // sqlite in-memory or default env
        $this->artisan('launchpoint:health')
            ->expectsOutputToContain('LaunchPoint Project Health Diagnostic');
    }
}
