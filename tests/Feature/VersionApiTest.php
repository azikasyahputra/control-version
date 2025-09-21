<?php

namespace Tests\Feature;

use App\Models\Version;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VersionApiTest extends TestCase
{
    use RefreshDatabase; // This trait resets the database for each test.

    /** @test */
    public function it_can_store_a_new_version_with_a_string_value(): void
    {
        $payload = ['app_version' => '1.0.0'];

        $response = $this->postJson('/api/version', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'key' => 'app_version',
                     'value' => '1.0.0'
                 ]);

        $this->assertDatabaseHas('version', [
            'key' => 'app_version',
            'value' => '"1.0.0"' // Eloquent auto-encodes even strings
        ]);
    }

    /** @test */
    public function it_can_store_a_new_version_with_a_json_value(): void
    {
        $payload = ['user_settings' => ['theme' => 'dark', 'notifications' => true]];

        $response = $this->postJson('/api/version', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'key' => 'user_settings',
                     'value' => [
                         'theme' => 'dark',
                         'notifications' => true
                     ]
                 ]);

        $this->assertDatabaseHas('version', [
            'key' => 'user_settings',
            'value' => json_encode(['theme' => 'dark', 'notifications' => true])
        ]);
    }

    /** @test */
    public function it_fails_to_store_if_body_has_more_than_one_key(): void
    {
        $payload = [
            'key_one' => 'value_one',
            'key_two' => 'value_two'
        ];

        $response = $this->postJson('/api/version', $payload);

        $response->assertStatus(422) // Unprocessable Content
                 ->assertJsonValidationErrors('body');
    }

    /** @test */
    public function it_can_get_the_latest_version_for_a_key(): void
    {
        // Create an older version first
        Version::create([
            'key' => 'feature_flag', 
            'value' => 'false', 
            'created_at' => now()->subDay()->getTimestamp()
        ]);

        // Create the newest version
        $newestVersion = Version::create([
            'key' => 'feature_flag', 
            'value' => 'true',
            'created_at' => now()->getTimestamp()
        ]);

        $response = $this->getJson('/api/version/feature_flag');

        $response->assertStatus(200)
                 ->assertJson([
                     'key' => 'feature_flag',
                     'value' => 'true',
                     'id' => $newestVersion->id
                 ]);
    }

    /** @test */
    public function it_can_get_a_version_at_a_specific_point_in_time(): void
    {
        // Create an older version
        $olderVersion = Version::create([
            'key' => 'api_endpoint', 
            'value' => 'v1', 
            'created_at' => now()->subDays(2)->getTimestamp()
        ]);

        // Create a newer version
        Version::create([
            'key' => 'api_endpoint', 
            'value' => 'v2',
            'created_at' => now()->getTimestamp()
        ]);
        
        // Ask for the version from yesterday
        $timestamp = now()->subDay()->getTimestamp();

        $response = $this->getJson("/api/version/api_endpoint?timestamp={$timestamp}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $olderVersion->id,
                     'key' => 'api_endpoint',
                     'value' => 'v1'
                 ]);
    }

    /** @test */
    public function it_returns_404_if_no_version_is_found_for_a_key(): void
    {
        $response = $this->getJson('/api/version/non_existent_key');
        $response->assertStatus(404);
    }
    
    /** @test */
    public function it_can_get_all_version_records(): void
    {
        Version::create(['key' => 'key1', 'value' => 'value1']);
        Version::create(['key' => 'key2', 'value' => 'value2']);

        $response = $this->getJson('/api/version/get_all_records');

        $response->assertStatus(200)
                 ->assertJsonCount(2)
                 ->assertJsonStructure([
                     '*' => ['id', 'key', 'value', 'created_at', 'updated_at']
                 ]);
    }
}