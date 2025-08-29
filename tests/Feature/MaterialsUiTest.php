<?php

use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirect guests to login on /materials', function () {
    $this->get('/materials')->assertRedirect('/login');
});

it('validates and creates a material via POST', function () {
    $user = User::factory()->create();

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

it('shows the edit form for a material', function () {
    $user = User::factory()->create();
    $material = Material::create([
        'name' => 'Lavender',
        'category' => 'EO',
        'botanical' => 'Lavandula Angustifolia',
    ]);

    $this->actingAs($user)
        ->get(route('materials.edit', $material))
        ->assertOk()
        ->assertSee('Edit Material')
        ->assertSee('Lavender')
        ->assertSee('Lavandula Angustifolia');
});

it('links each material on the index to its edit page', function () {
    $user = User::factory()->create();

    $material = Material::create([
        'name' => 'Lavender',
        'category' => 'EO',
        'botanical' => 'Lavandula Angustifolia',
    ]);

    $this->actingAs($user)
        ->get('/materials')
        ->assertSee(e(route('materials.edit', $material)));
});

it('updates a material and redirects', function () {
    $user = User::factory()->create();

    $material = Material::create([
        'name' => 'Lavender',
        'category' => 'EO',
        'botanical' => 'Lavandula Angustifolia',
    ]);

    $this->actingAs($user)
        ->patch(route('materials.update', $material), [
            'name' => 'Lavendola',
            'category' => 'EO',
            'botanical' => 'Lavandula Angustifolia',
            'notes' => 'updated',
        ])
        ->assertRedirect('/materials');

    $this->assertDatabaseHas('materials', [
        'id' => $material->id,
        'name' => 'Lavendola',
        'category' => 'EO',
        'botanical' => 'Lavandula Angustifolia',
        'notes' => 'updated',
    ]);
});
