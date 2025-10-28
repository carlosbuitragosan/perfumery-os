<?php

use App\Models\Bottle;
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

it('shows a cancel button in the bottle create form to go back to the specific material', function () {
    $material = makeMaterial();
    $response = getAs($this->user, route('materials.bottles.create', $material))->assertOk();
    $crawler = crawl($response);

    $cancelLink = $crawler->filter('a:contains("CANCEL")');
    expect($cancelLink->count())->toBe(1, 'Missing cancel button/link');
    expect($cancelLink->attr('href'))->toBe(route('materials.show', $material));

});

it('creates a bottle for the material on form submit', function () {
    $material = makeMaterial();

    $payload = bottlePayload(['batch_code' => 'AB123']);

    $response = postAs($this->user, route('materials.bottles.store', $material), $payload);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('materials.show', $material));

    expect(Bottle::query()->where([
        'material_id' => $material->id,
        'batch_code' => 'AB123',
    ])->exists())->toBeTrue();
});

it('shows the material bottles on the material show page', function () {
    $material = makeMaterial();

    $bottle = makeBottle($material, bottlePayload());

    $response = getAs($this->user, route('materials.show', $material))
        ->assertOk();
    $crawler = crawl($response);

    expect($response->getContent())->toContain('Eden Botanicals');
    expect($response->getContent())->toContain('http://www.edenbotanicals.com');
    expect($response->getContent())->toContain('AB1234');
    expect($response->getContent())->toContain('Steam distilled');
    expect($response->getContent())->toContain('leaves');
    expect($response->getContent())->toContain('Morocco');
    expect($response->getContent())->toContain('30/01/2021');
    expect($response->getContent())->toContain('01/03/2025');
    expect($response->getContent())->toContain('10');
    expect($response->getContent())->toContain('0.912');
    expect($response->getContent())->toContain('4.99');
    expect($response->getContent())->toContain('test notes');
    expect($response->getContent())->toContain('In use');
});

it('marks a bottle as finished from the material show page', function () {
    $material = makeMaterial();
    $bottle = makeBottle($material, [
        'supplier_name' => 'test supplier',
        'method' => 'steam_distilled',
    ]);

    $response = postAs($this->user, route('bottles.finish', $bottle));
    $response->assertRedirect(route('materials.show', $material));

    $bottle->refresh();
    expect($bottle->is_active)->toBeFalse();

    $page = getAs($this->user, route('materials.show', $material))->assertOk();
    expect($page->getContent())->toContain('Finished');
    expect($page->getContent())->not->toContain('In use');
});

it('shows newest bottles first on the material show page', function () {
    $material = makeMaterial(['name' => 'bergamot']);

    $older = makeBottle($material, ['batch_code' => 'old']);
    // manually force timestamps to yesterday
    $older->created_at = now()->subDay();
    $older->updated_at = now()->subDay();
    $older->save();

    $newer = makeBottle($material, ['batch_code' => 'new']);

    $response = getAs($this->user, route('materials.show', $material))
        ->assertOk();
    $html = $response->getContent();

    $posNewer = strpos($html, 'new');
    $posOlder = strpos($html, 'old');

    expect($posNewer)->not->toBeFalse();
    expect($posOlder)->not->toBeFalse();
    expect($posNewer)->toBeLessThan($posOlder, 'Newest bottle should appear before the older bottle');
});

it('shows an edit button that links to edit bottle page', function () {
    $material = makeMaterial();
    $bottle = makeBottle($material);

    $response = getAs($this->user, route('materials.show', $material))
        ->assertOk();

    $crawler = crawl($response);

    $editUrl = route('bottles.edit', $bottle);

    $link = $crawler->filter('a:contains("EDIT")')
        ->reduce(function ($node) use ($editUrl) {
            return $node->attr('href') === $editUrl;
        });

    expect($link->count())->toBeGreaterThan(0, "Missing edit link for bottle {$bottle->id}");
});

it('shows the edit form for a bottle', function () {
    $material = makeMaterial();
    $bottle = makeBottle($material);

    $response = getAs($this->user, route('bottles.edit', $bottle))
        ->assertOk()
        ->assertSee($material->name)
        ->assertSee('Edit Bottle')
        ->assertSee('Eden Botanicals')
        ->assertSee('Morocco')
        // false = do not escape html
        ->assertSee('value="'.$bottle->price.'"', false);
});

it('updates a bottle and redirects', function () {
    $material = makeMaterial();
    $bottle = makeBottle($material);
    $payload = bottlePayload([
        'origin_country' => 'Colombia',
        'plant_part' => 'flowers',
        'price' => 200,
    ]);

    $response = patchAs($this->user, route('bottles.update', $bottle), $payload);
    $expectedUrl = route('materials.show', $material).'#bottle-'.$bottle->id;

    $response->assertRedirect($expectedUrl);

    $this->assertDatabaseHas('bottles', [
        'id' => $bottle->id,
        'material_id' => $material->id,
        'batch_code' => 'AB1234',
        'origin_country' => 'Colombia',
        'plant_part' => 'flowers',
        'price' => 200,
    ]);
});
