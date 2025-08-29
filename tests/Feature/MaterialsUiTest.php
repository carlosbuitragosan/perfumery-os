<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirect guests to login on /materials', function () {
    $this->get('/materials')->assertRedirect('/login');
});

it('validates and creates a material via POST', function () {
    $user = \App\Models\User::factory()->create();

    // Missing name -> validation error
    $this->actingAs($user)
        ->post('/materials', [
            'name' => '',
            'category' => 'EO',
            'botanical' => 'Lavandula Angustifolia',
            'notes' => 'Test',
        ])
        ->assertSessionHasErrors(['name']);

    // Valid create ->  redirect + persisted
    $this->actingAs($user)
        ->post('/materials', [
            'name' => 'Peppermint',
            'category' => 'EO',
            'botanical' => 'Mentha piperita',
            'notes' => 'Fresh',
        ])
        ->assertRedirect('/materials');

    $this->assertDatabaseHas('materials', ['name' => 'Peppermint']);
});
