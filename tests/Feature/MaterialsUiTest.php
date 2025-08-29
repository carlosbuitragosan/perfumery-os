<?php

use App\Models\Material;
use App\Models\User;
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

it('persists botanical when provided', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/materials', [
            'name' => 'Lavender',
            'category' => 'EO',
            'botanical' => 'Lavandula Angustifolia',
            'notes' => 'test',
        ])
        ->assertRedirect('/materials');

    $this->assertDatabaseHas('materials', [
        'name' => 'Lavender',
        'botanical' => 'Lavandula Angustifolia',
    ]);
});

it('rejects duplicate material names (case-insensitive)', function () {
    $user = User::factory()->create();

    // save an existing record
    Material::create([
        'name' => 'Lavender',
        'category' => 'EO',
        'botanical' => 'Lavandula Angustifolia',
    ]);

    $this->actingAs($user)
        ->post('/materials', [
            'name' => 'Lavender',
            'category' => 'EO',
        ])
        ->assertSessionHasErrors(['name']);

    expect(Material::whereRaw('LOWER(name) = ?', ['lavender'])->count())->toBe(1);
});
