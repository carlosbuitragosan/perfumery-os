<?php

use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->in('Feature');

beforeEach(function () {
    $this->user = User::Factory()->create();
});

// Default payload for creating/updating a material
function materialPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Lavender',
        'category' => 'EO',
        'botanical' => 'Lavandula Angustifolia',
        'notes' => 'test',
    ], $overrides);
}

// Persist a material quickly
function makeMaterial(array $overrides = []): material
{
    return Material::create(materialPayload($overrides));
}

it('redirect guests to login on /materials', function () {
    $this->get('/materials')->assertRedirect('/login');
});

it('validates and creates a material via POST', function () {
    // Missing name -> validation error
    $this->actingAs($this->user)
        ->post('/materials', materialPayload(['name' => '']))
        ->assertSessionHasErrors(['name']);

    // Valid create ->  redirect + persisted
    $this->actingAs($this->user)
        ->post('/materials', materialPayload([
            'name' => 'Peppermint',
            'category' => 'EO',
            'botanical' => 'Mentha piperita',
            'notes' => 'Fresh',
        ]))
        ->assertRedirect('/materials');

    $this->assertDatabaseHas('materials', ['name' => 'Peppermint']);
});

it('persists botanical when provided', function () {
    $this->actingAs($this->user)
        ->post('/materials', materialPayload())
        ->assertRedirect('/materials');

    $this->assertDatabaseHas('materials', [
        'name' => 'Lavender',
        'botanical' => 'Lavandula Angustifolia',
    ]);
});

it('rejects duplicate material names (case-insensitive)', function () {
    makeMaterial(); // seeds Lavender

    expect(Material::count())->toBe(1);

    $this->actingAs($this->user)
        ->post('/materials', materialPayload([
            'name' => 'Lavender',
        ]))
        ->assertSessionHasErrors(['name']);

    expect(Material::whereRaw('LOWER(name) = ?', ['lavender'])->count())->toBe(1);
});

it('shows the edit form for a material', function () {
    $material = makeMaterial();

    $this->actingAs($this->user)
        ->get(route('materials.edit', $material))
        ->assertOk()
        ->assertSee('Edit Material')
        ->assertSee('Lavender')
        ->assertSee('Lavandula Angustifolia');
});

it('links each material on the index to its edit page', function () {
    $material = makeMaterial();

    $this->actingAs($this->user)
        ->get('/materials')
        ->assertSee(e(route('materials.edit', $material)));
});

it('updates a material and redirects', function () {
    $material = makeMaterial();

    $this->actingAs($this->user)
        ->patch(route('materials.update', $material), materialPayload([
            'name' => 'Lavendola',
            'category' => 'EO',
        ]))
        ->assertRedirect('/materials');

    $this->assertDatabaseHas('materials', [
        'id' => $material->id,
        'name' => 'Lavendola',
        'category' => 'EO',
        'botanical' => 'Lavandula Angustifolia',
        'notes' => 'test',
    ]);
});
