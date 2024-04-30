<?php

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function PHPUnit\Framework\assertTrue;

uses(RefreshDatabase::class);

test('require name, email, and password', function () {
    $response = $this->postJson('/api/v1/register', [], ['Accept' => 'application/json']);

    $response->assertStatus(422)
        ->assertJson([
            "message" => "Valiation Failed",
            "data" => [
                "name" => ["The name field is required."],
                "email" => ["The email field is required."],
                "password" => ["The password field is required."],
            ],
            "status" => false
        ]);
});

test('register successfully', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'waseemkamboh424@example.com',
        'password' => 'password',
    ];

    $response = $this->postJson('/api/v1/register', $userData);

    assertTrue($response->getStatusCode() !== 422, 'Validation failed: ' . $response->getContent());

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'name',
                'email',
                'verification_code',
                'created_at',
                'updated_at',
                'id'
            ],
            'access_token',
            'token_type',
            'status',
            'response_code'
        ]);
});
