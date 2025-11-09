<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('has the materials table with the expected columns', function () {
    expect(Schema::hasTable('materials'))->toBeTrue();

    expect(Schema::hasColumns('materials', [
        'id', 'name', 'botanical', 'notes', 'pyramid', 'families', 'functions', 'safety', 'effects', 'ifra_max_pct', 'created_at', 'updated_at', 'user_id',
    ]))->toBeTrue();

    expect(Schema::hasColumn('materials', 'category'))->toBeFalse();
});

it('can create a material with the minimal fields', function () {
    $material = makeMaterial();

    expect($material->id)->not->toBeNull();
    expect($material->pyramid)->not->toBeNull();

    $this->assertDatabaseHas('materials', [
        'name' => 'Lavender',
        'user_id' => $this->user->id,
    ]);
});

it('casts pyramid to array when set', function () {
    $material = makeMaterial();

    $material->refresh();
    expect($material->pyramid)->toEqual(['top', 'heart']);
});
