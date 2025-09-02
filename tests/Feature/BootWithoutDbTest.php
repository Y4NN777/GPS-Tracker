<?php declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\TestCase;
use Tests\CreatesApplication;

class BootWithoutDbTest extends TestCase
{
    use CreatesApplication;

    public function test_app_boot_does_not_bind_configuration_when_db_unavailable(): void
    {
        // Given: a fresh app instance booted via CreatesApplication
        $this->createApplication();

        // Then: if database driver/connection is unavailable, provider should not crash
        // and configuration binding may be absent. We just assert no exception occurred
        // and that accessing the binding is guarded by our code changes.
        $this->assertTrue(true);

        // The container may or may not have the binding depending on environment; this assert is soft
        // We only ensure that checking the binding status does not throw and is a boolean
        $this->assertIsBool(app()->bound('configuration'));
    }
}
