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

it('shows the bottle create form with all fields', function () {
    $material = makeMaterial();
    $response = getAs($this->user, route('materials.bottles.create', $material))->assertOk();
    $crawler = crawl($response);

    $assertInput = function (string $selector) use ($crawler) {
        expect($crawler->filter($selector)->count())
            ->toBe(1, "Missing input {$selector}");
    };

    $assertInput('input[name="supplier_name"]');
    $assertInput('input[name="supplier_url"]');
    $assertInput('input[name="batch_code"]');
    $assertInput('select[name="method"]');
    $assertInput('input[name="plant_part"]');
    $assertInput('input[name="origin_country"]');
    $assertInput('input[name="distillation_date"]');
    $assertInput('input[name="purchase_date"]');
    $assertInput('input[name="density"]');
    $assertInput('input[name="volume_ml"]');
    $assertInput('input[name="price"]');
    $assertInput('textarea[name="notes"]');

    $files = $crawler->filter('input[type="file"][name="files[]"]');
    expect($files->count())->toBe(1, 'Missing input[type="file"][name="files[]"]');
    expect($files->attr('multiple'))->not->toBeNull('files[] input should have the "multiple" attribute');
});
