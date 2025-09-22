<?php

namespace Tests\Feature\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use App\Http\Requests\DynamicKeyStoreRequest;

class DynamicKeyStoreRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup a dummy route for testing the form request.
     */
    public function setUp(): void
    {
        parent::setUp();

        Route::post('/_test-dynamic-key-request', function (DynamicKeyStoreRequest $request) {
            return $request->getDynamicKeyAndValue();
        });
    }

    /**
     * @test
     * @dataProvider validationProvider
     */
    public function it_validates_the_request_body($data, $shouldPass)
    {
        // Act
        $response = $this->postJson('/_test-dynamic-key-request', $data);

        // Assert
        if ($shouldPass) {
            $response->assertStatus(200);
            $this->assertEquals(key($data), $response->json('key'));
            $this->assertEquals(current($data), $response->json('value'));
        } else {
            $response->assertStatus(422)
                     ->assertJsonValidationErrors('body');
        }
    }

    public static function validationProvider(): array
    {
        return [
            'valid: single key-value pair' => [
                'data' => ['test_key' => 'test_value'],
                'shouldPass' => true,
            ],
            'valid: single key with JSON object value' => [
                'data' => ['config' => json_encode(['setting' => 'enabled'])],
                'shouldPass' => true,
            ],
            'invalid: empty JSON object' => [
                'data' => [],
                'shouldPass' => false,
            ],
            'invalid: multiple key-value pairs' => [
                'data' => ['key1' => 'value1', 'key2' => 'value2'],
                'shouldPass' => false,
            ],
        ];
    }
}