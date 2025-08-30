<?php

use App\Models\Material;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('has the materials table with the expected columns', function () {
    expect(Schema::hasTable('materials'))->toBeTrue();

    expect(Schema::hasColumns('materials', [
        'id', 'name', 'category', 'botanical', 'notes', 'pyramid', 'families', 'functions', 'safety', 'effects', 'ifra_max_pct', 'created_at', 'updated_at',
    ]))->toBeTrue();
});

it('can create a material with the minimal fields', function () {
    $material = Material::create([
        'name' => 'Lavender',
        'category' => 'EO',
        'botanical' => 'Lavandula Angustifolia',
        'notes' => 'Fresh, clean lavender',
    ]);

    expect($material->id)->not->toBeNull();
    expect($material->pyramid)->toBeNull();

    $this->assertDatabaseHas('materials', [
        'name' => 'Lavender',
    ]);
});

it('casts pyramid to array when set', function () {
    $material = Material::create([
        'name' => 'Lavender',
        'pyramid' => ['top', 'heart'],
    ]);

    $material->refresh();
    expect($material->pyramid)->toEqual(['top', 'heart']);
});
