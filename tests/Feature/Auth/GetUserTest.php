<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;

// uses(RefreshDatabase::class);

// it('can get user record', function () {
//     $user = User::factory()->create();

//     Auth::shouldReceive('id')->once()->andReturn($user->id);

//     $response = $this->get('/api/v1/user');

//     $response->assertStatus(200)
//         ->assertJson([
//             'message' => 'User found',
//             'data' => $user->toArray(),
//             'status' => true,
//             'response_code' => 200,
//         ]);
// });

// it('returns user not found if user does not exist', function () {
//     Auth::shouldReceive('id')->once()->andReturn(null);

//     $response = $this->get('/api/v1/user');

//     $response->assertStatus(404)
//         ->assertJson([
//             'message' => 'User not found',
//             'data' => null,
//             'status' => false,
//             'response_code' => 404,
//         ]);
// });
