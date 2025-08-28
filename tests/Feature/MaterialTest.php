<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('has the materials table with the expected columns', function () {
    expect(Schema::hasTable('materials'))->toBeTrue();

    expect(Schema::hasColumns('materials', [
        'id', 'name', 'category', 'botanical', 'notes', 'created_at', 'updated_at',
    ]))->toBeTrue();
});

it('can create a material with the minimal fields', function () {
    $material = \App\Models\Material::create([
        'name' => 'Lavender',
        'category' => 'EO',
        'botanical' => 'Lavandula Angustifolia',
        'notes' => 'Fresh, clean lavender',
    ]);

    expect($material->id)->not->toBeNull();

    $this->assertDatabaseHas('materials', [
        'name' => 'Lavender',
    ]);
});
