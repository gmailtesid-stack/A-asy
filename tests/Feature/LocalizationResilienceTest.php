<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LocalizationResilienceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function test_system_auto_detects_timezone_from_gps_coordinates()
    {
        $company = Company::create(['name' => 'Global Co', 'legal_entity' => 'PT']);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'timezone' => 'UTC' // Default
        ]);

        // Simulating request from Tokyo (Japan)
        // Latitude: 35.6895, Longitude: 139.6917
        $response = $this->actingAs($user)
            ->withHeaders([
                'X-Device-Lat' => '35.6895',
                'X-Device-Lng' => '139.6917'
            ])
            ->get('/api/user'); // Any authenticated endpoint

        // The middleware should have updated the user's timezone to 'Asia/Tokyo'
        $this->assertEquals('Asia/Tokyo', $user->fresh()->timezone);
    }

    /** @test */
    public function test_system_auto_detects_timezone_from_indonesia_gps()
    {
        $company = Company::create(['name' => 'Global Co', 'legal_entity' => 'PT']);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'timezone' => 'UTC'
        ]);

        // Simulating request from Jakarta (WIB)
        // Latitude: -6.2088, Longitude: 106.8456
        $response = $this->actingAs($user)
            ->withHeaders([
                'X-Device-Lat' => '-6.2088',
                'X-Device-Lng' => '106.8456'
            ])
            ->get('/api/user');

        $this->assertEquals('Asia/Jakarta', $user->fresh()->timezone);
    }
}
