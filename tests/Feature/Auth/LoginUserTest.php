<?php

use App\Models\User;

it('can log in with valid credentials', function () {
    $user = User::factory()->create();

    $credentials = [
        'email' => $user->email,
        'password' => 'password',
    ];

    $response = $this->postJson('/api/v1/login', $credentials);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'access_token',
            'token_type',
        ])
        ->assertJson([
            'message' => 'Login success',
        ]);
});

it('cannot log in with invalid credentials', function () {
    $credentials = [
        'email' => 'invalid@example.com',
        'password' => 'invalidpassword',
    ];

    $response = $this->postJson('/api/v1/login', $credentials);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'User not found',
            'status' => false,
        ]);
});

it('requires email and password', function () {
    $response = $this->postJson('/api/v1/login', [], ['Accept' => 'application/json']);

    $response->assertStatus(422)
        ->assertJson([
            "message" => "Valiation Failed",
            "data" => [
                "email" => ["The email field is required."],
                "password" => ["The password field is required."],
            ],
            "status" => false
        ]);
});
