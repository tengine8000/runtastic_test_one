<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TraceTest extends TestCase
{
    /**
     * Test route exists.
     *
     * @return void
     */
    public function test_trace_route_exists()
    {
        $response = $this->get('/api/traces');

        $response->assertStatus(200);
    }

    public function test_trace_create_route_with_no_payload()
    {
        $response = $this->postJson('/api/traces', []);

        $response
            ->assertStatus(400)
            ->assertJson([
                'error' => true,
            ]);
    }

    public function test_trace_create_route_with_invalid_payload_wrong_input_field_names()
    {
        $response = $this->postJson('/api/traces', 
            [['latty' => 32.9377784729004, 'longy' => -117.2303924560]]
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'error' => true,
                'message' => true,
            ]);
    }

    public function test_trace_create_route_with_invalid_payload_wrong_latitude_and_wrong_longitude()
    {
        $response = $this->postJson('/api/traces', 
            [
                ['latitude' => 2032.9377784729004, 'longitude' => 1170.2303924560],
                ['latitude' => 2032.9377784729004, 'longitude' => 1170.2303924560],
                ['latitude' => 2032.9377784729004, 'longitude' => 1170.2303924560],
                ['latitude' => 2032.9377784729004, 'longitude' => 1170.2303924560],
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'error' => true,
                'message' => true,
            ]);
    }

    public function test_trace_create_route_with_valid_payload_returns_trace_data()
    {
        $response = $this->postJson('/api/traces', 
                        [
                ['latitude' => 32.9377784729004, 'longitude' => -117.2303924560],
                ['latitude' => -32.9377784729004, 'longitude' => 16.2303924560],
                ['latitude' => 32.9377784729004, 'longitude' => -10.2303924560],
                ['latitude' => -88.9377784729004, 'longitude' => 117.2303924560],
            ]
        );

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'trace_id' => true
            ]);;
    }

    /**
     * GET Specific Trace
     */
    public function test_can_get_existing_trace_data_given_id()
    {
        $response = $this->postJson('/api/traces', 
                        [
                ['latitude' => 32.9377784729004, 'longitude' => -117.2303924560],
                ['latitude' => -32.9377784729004, 'longitude' => 16.2303924560],
                ['latitude' => 32.9377784729004, 'longitude' => -10.2303924560],
                ['latitude' => -88.9377784729004, 'longitude' => 117.2303924560],
            ]
        );

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'trace_id' => true
            ]);

        $trace_id = $response['trace_id'];

        // GET trace with given id
        $response = $this->get('/api/traces/'.$trace_id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => true
            ]);

    }

    public function test_cannot_get_trace_data_given_invalid_id()
    {
        $trace_id = 78; // invalid id

        // GET trace with given id
        $response = $this->get('/api/traces/'.$trace_id);

        $response->assertNotFound();
    }
    
}
