<?php

namespace Tests\Feature;

use App\Models\Version;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VersionApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_store_a_new_version_with_a_string_value(): void
    {
        $payload = ['app_version' => '1.0.0'];

        $response = $this->postJson('/api/version', $payload);

        $response->assertStatus(201);

        $response->assertJsonStructure(['Time']);

        $this->assertDatabaseHas('version', [
            'key' => 'app_version',
            'value' => '"1.0.0"'
        ]);
    }

    /** @test */
    public function it_can_store_a_new_version_with_a_json_value(): void
    {
        $payload = ['user_settings' => '{"theme":"dark","notifications":true}'];

        $response = $this->postJson('/api/version', $payload);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'Time'
        ]);

        $this->assertDatabaseHas('version', [
            'key' => 'user_settings',
            'value' => json_encode('{"theme":"dark","notifications":true}')
        ]);
    }

     /** @test */
    public function it_can_store_a_new_version_with_a_array_value(): void
    {
        $jsonData = json_encode([
            'theme' => 'dark', 
            'version' => 2
        ]);
        $payload = ['user_prefs' => $jsonData];

        $response = $this->postJson('/api/version', $payload);

        $response->assertStatus(201);

        $response->assertJsonStructure(['Time']);

        $this->assertDatabaseHas('version', [
            'key' => 'user_prefs',
            'value' => json_encode($jsonData)
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

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('body');
    }

     /** @test */
    public function it_fails_to_store_if_key_already_exist(): void
    {
        Version::create([
            'key' => 'key_one',
            'value' => 'value_one',
        ]);

        $payload = [
            'key' => 'key_one',
            'value' => 'value_two',
        ];

        $response = $this->postJson('/api/version', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('body');
    }

    /**
     * @test
     */
    public function it_returns_a_validation_error_for_an_invalid_payload(): void
    {
        $payloads = [
            'empty_object' => [],
            'multiple_keys' => ['key1' => 'value1', 'key2' => 'value2'],
            'not_an_object' => ['just_a_value']
        ];

        foreach ($payloads as $payload) {
            $response = $this->postJson('/api/version', $payload);
            $response->assertStatus(422);
        }
    }

    /** @test */
    public function it_can_get_the_version_for_a_key(): void
    {
        Version::create([
            'key' => 'feature_flag', 
            'value' => 'true',
            'created_at' => now()->getTimestamp()
        ]);

        $response = $this->getJson('/api/version/feature_flag');

        $response->assertStatus(200)
                ->assertJson([
                    'value' => 'true',
                ]);
    }

    /** @test */
    public function it_can_get_the_version_for_a_key_and_timestamp(): void
    {
        $data = Version::create([
            'key' => 'api_endpoint', 
            'value' => 'v2',
            'created_at' => now()->getTimestamp()
        ]);
        
        $timestamp = $data->created_at;

        $response = $this->getJson("/api/version/api_endpoint?timestamp={$timestamp}");

        $response->assertStatus(200)
                 ->assertJson(['value' => 'v2']);
    }

     /** @test */
    public function it_can_get_the_version_for_a_key_and_invalid_timestamp(): void
    {
        $data = Version::create([
            'key' => 'api_endpoint', 
            'value' => 'v2'
        ]);
        
        $timestamp = 'ASDKJKABC23';

        $response = $this->getJson("/api/version/api_endpoint?timestamp={$timestamp}");

        $response->assertStatus(422);
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

    /** @test */
    public function it_can_not_found_if_there_are_no_records(): void
    {
        $response = $this->getJson('/api/version/get_all_records');
        $response->assertStatus(404);
    }
}