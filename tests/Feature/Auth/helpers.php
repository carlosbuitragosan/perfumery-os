<?php

use App\Models\User;
use Illuminate\Testing\TestResponse;

function postWithCsrf(string $uri, array $data = []): TestResponse
{
    $token = 'test-token';

    return test()
        ->withSession(['_token' => $token])
        ->post($uri, array_merge($data, ['_token' => $token]));
}

function postAsWithCsrf(User $user, string $uri, array $data = []): TestResponse
{
    $token = 'test-token';

    return test()
        ->actingAs($user)
        ->withSession(['_token' => $token])
        ->post($uri, array_merge($data, ['_token' => $token]));
}
