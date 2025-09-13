<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Create user
beforeEach(function () {
    $this->user = User::factory()->create();
});

it('shows a "add" button on the material show page linking to the create form', function () {
    $material = makeMaterial();
    $response = getAs($this->user, route('materials.show', $material))->assertOk();

    // Expect a link to the create form
    $createUrl = route('materials.bottles.create', $material);
    $response->assertSee($createUrl, false);
    $response->assertSee('ADD');
});
it('shows the create bottle form for a material', function () {
    $material = makeMaterial();
    $response = getAs($this->user, route('materials.bottles.create', $material))->assertOk();
    $response->assertSee('Create Bottle');
});
