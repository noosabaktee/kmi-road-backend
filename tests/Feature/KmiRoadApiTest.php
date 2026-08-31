<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\mDriver;
use App\Models\mVehicle;
use App\Models\trDutyTrip;

class KmiRoadApiTest extends TestCase
{
    public function test_public_vehicle_seat_check(): void
    {
        $response = $this->getJson('/api/check-vehicles?date=' . now()->toDateString());
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'date',
            'vehicles' => [
                '*' => ['id', 'name', 'plate', 'brand_model', 'max_seat', 'remaining_seats', 'is_full']
            ]
        ]);
    }

    public function test_driver_login_and_fetch_trips(): void
    {
        $loginRes = $this->postJson('/api/driver/login', [
            'txtEmail' => 'joko.santoso@kmi.kalbe.co.id',
            'password' => 'driver123',
        ]);

        $loginRes->assertStatus(200);
        $token = $loginRes->json('token');
        $this->assertNotEmpty($token);

        $tripRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/driver/trips');

        $tripRes->assertStatus(200);
        $tripRes->assertJsonStructure([
            'status',
            'active_trip',
            'upcoming_trips',
            'completed_trips',
        ]);
    }

    public function test_driver_gps_location_telemetry(): void
    {
        $driver = mDriver::where('txtEmail', 'joko.santoso@kmi.kalbe.co.id')->first();
        $token = $driver->createToken('test_token')->plainTextToken;

        $trip = trDutyTrip::where('intDriver_ID', $driver->intDriver_ID)->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/driver/location-update', [
                'trip_id' => $trip->intDutyTrip_ID,
                'latitude' => -6.331200,
                'longitude' => 107.158900,
                'speed' => 45.5,
                'heading' => 90,
                'accuracy' => 4.0,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }
}
