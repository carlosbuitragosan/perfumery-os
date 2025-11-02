<?php

use App\Models\Bottle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Create user
beforeEach(function () {
    $this->user = User::factory()->create();
    $this->material = makeMaterial();
});

describe('Bottle creation', function () {

    it('shows a "add" button on the material show page linking to the create form', function () {
        $createUrl = route('materials.bottles.create', $this->material);
        [$response, $crawler] = getPageCrawler($this->user, route('materials.show', $this->material));

        $addButton = $crawler->filter('a[href="'.$createUrl.'"]');
        expect($addButton->count())->toBe(1, 'Missing add link to create bottle');
    });

    it('shows the bottle create form with all fields', function () {
        $createUrl = route('materials.bottles.create', $this->material);
        [$response, $crawler] = getPageCrawler($this->user, $createUrl);

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
        $assertInput('input[name="expiry_date"]');
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
        $createUrl = route('materials.bottles.create', $this->material);
        [$response, $crawler] = getPageCrawler($this->user, $createUrl);

        $cancelLink = $crawler->filter('a:contains("CANCEL")');
        expect($cancelLink->count())->toBe(1, 'Missing cancel button/link');
        expect($cancelLink->attr('href'))->toBe(route('materials.show', $this->material));
    });

    it('creates a bottle for the material on form submit', function () {
        $payload = bottlePayload(['batch_code' => 'AB123']);
        $storeUrl = route('materials.bottles.store', $this->material);
        $redirectRoute = route('materials.show', $this->material);

        postAs($this->user, $storeUrl, $payload)
            ->assertSessionHasNoErrors()
            ->assertRedirect($redirectRoute);

        expect(Bottle::whereMaterialId($this->material->id)
            ->whereBatchCode('AB123')
            ->whereSupplierName('Eden Botanicals')
            ->whereIsActive(true)
            ->exists())
            ->toBeTrue('Bottle not created in DB');
    });

    it('shows expiry date field instead of distillation date in the create form', function () {
        $createUrl = route('materials.bottles.create', $this->material);

        [$response, $crawler] = getPageCrawler($this->user, $createUrl);

        expect($crawler->text())->not->toContain('Distillation date');
        expect($crawler->filter('input[type="date"][name="distillation_date"]')->count())->toBe(0, 'An input with name distillation_date has been found');

        expect($crawler->text())->toContain('Expiry date');
        expect($crawler->filter('input[type="date"][name="expiry_date"]')->count())->toBe(1, 'Missing expiry date in form');
    });
});

describe('Bottle display', function () {
    it('shows the material bottles on the material show page', function () {
        $bottle = makeBottle($this->material);
        $showUrl = route('materials.show', $this->material);

        [$response, $crawler] = getPageCrawler($this->user, $showUrl);
        $bottleDiv = $crawler->filter("div#bottle-{$bottle->id}");

        expect($bottleDiv->text())->toContain('Eden Botanicals');
        expect($bottleDiv->text())->toContain('http://www.edenbotanicals.com');
        expect($bottleDiv->text())->toContain('AB1234');
        expect($bottleDiv->text())->toContain('Steam distilled');
        expect($bottleDiv->text())->toContain('leaves');
        expect($bottleDiv->text())->toContain('Morocco');
        expect($bottleDiv->text())->toContain('01/03/2025');
        expect($bottleDiv->text())->toContain('10');
        expect($bottleDiv->text())->toContain('0.912');
        expect($bottleDiv->text())->toContain('4.99');
        expect($bottleDiv->text())->toContain('test notes');
        expect($bottleDiv->text())->toContain('In use');
    });

    it('shows newest bottles first on the material show page', function () {
        $material = makeMaterial(['name' => 'bergamot']);
        $showUrl = route('materials.show', $material);

        // make the older bottle a day old
        $older = makeBottle($material, ['batch_code' => 'old']);
        $older->created_at = now()->subDay();
        $older->updated_at = now()->subDay();
        $older->save();

        $newer = makeBottle($material, ['batch_code' => 'new']);

        [$response, $crawler] = getPageCrawler($this->user, $showUrl);

        $bottles = $crawler->filter("#bottle-{$newer->id}, #bottle-{$older->id}");
        $firstBottle = $bottles->first()->attr('id');

        expect($firstBottle)->toBe("bottle-{$newer->id}", 'Newest bottle should appear first');
    });

    it('shows expiry date for a bottle on the material show page (and no distillation date', function () {
        $payload = bottlePayload();
        $createUrl = route('materials.bottles.store', $this->material);
        $showUrl = route('materials.show', $this->material);

        postAs($this->user, $createUrl, $payload);
        $newBottle = $this->material->bottles()->latest()->first();

        [$response, $crawler] = getPageCrawler($this->user, $showUrl);
        $newBottleDiv = $crawler->filter("div#bottle-{$newBottle->id}");

        expect($newBottleDiv->text())->not->toContain('Distillation date');
        expect($newBottleDiv->text())->toContain('Expiry date');
        expect($newBottleDiv->text())->toContain('30/01/2029');
    });
});

describe('Bottle editing', function () {
    it('shows an edit button that links to edit bottle page', function () {
        $bottle = makeBottle($this->material);
        $showUrl = route('materials.show', $this->material);
        $editUrl = route('bottles.edit', $bottle);

        [$response, $crawler] = getPageCrawler($this->user, $showUrl);

        $link = $crawler->filter('a:contains("EDIT")')
            ->reduce(function ($node) use ($editUrl) {
                return $node->attr('href') === $editUrl;
            });

        expect($link->count())->toBe(1, "Missing edit link for bottle {$bottle->id}");
    });

    it('shows the edit form for a bottle', function () {
        $bottle = makeBottle($this->material);
        $editUrl = route('bottles.edit', $bottle);
        [$response, $crawler] = getPageCrawler($this->user, $editUrl);

        $header = $crawler->filter('header');
        expect($header->text())->toContain("{$this->material->name}");
        expect($header->text())->toContain('Edit Bottle');

        $form = $crawler->filter('form#bottle-edit-form');

        $priceInput = $form->filter('input[name="price"]');
        expect($priceInput->attr('value'))->toEqual($bottle->price);

        $methodInput = $form->filter('select[name="method"] option[selected]');
        expect($methodInput->attr('value'))->toBe($bottle->method);

        $originInput = $form->filter('input[name="origin_country"]');
        expect($originInput->attr('value'))->toBe($bottle->origin_country);
    });

    it('updates a bottle and redirects to the correct view', function (int $padding) {
        // create materials to add noise
        for ($i = 0; $i < $padding; $i++) {
            makeMaterial(['name' => "Pad-$i"]);
        }

        // create target for updating and redirecting
        $targetMaterial = makeMaterial(['name' => "Target-$padding"]);
        $bottle = makeBottle($targetMaterial);

        // add more materials for more noise
        for ($i = 0; $i < 3; $i++) {
            makeMaterial(['name' => "Tail-$i"]);
        }

        // minimal change for update
        $payload = bottlePayload(['price' => 123]);
        $updateUrl = route('bottles.update', $bottle);
        $showUrl = route('materials.show', $targetMaterial).'#bottle-'.$bottle->id;

        patchAs($this->user, $updateUrl, $payload)
            ->assertRedirect($showUrl)
            ->assertSessionHasNoErrors();
    })->with(range(0, 20));

    it('shows updated bottle data on the show page', function () {
        $bottle = makeBottle($this->material);
        $payload = bottlePayload([
            'origin_country' => 'Colombia',
            'plant_part' => 'flowers',
            'price' => 200,
        ]);
        $updateUrl = route('bottles.update', $bottle);
        $showUrl = route('materials.show', $this->material).'#bottle-'.$bottle->id;

        patchAs($this->user, $updateUrl, $payload)
            ->assertRedirect($showUrl)
            ->assertSessionHasNoErrors();

        [$response, $crawler] = getPageCrawler($this->user, $showUrl);

        $bottleDiv = $crawler->filter("div#bottle-{$bottle->id}");
        expect($bottleDiv->text())->toContain('Colombia');
        expect($bottleDiv->text())->toContain('flowers');
        expect($bottleDiv->text())->toContain('200');
        expect($bottleDiv->text())->toContain('AB1234');
    });

    it('shows a cancel link in the edit form that returns to the material show page anchored to that bottle', function () {
        $bottle = makeBottle($this->material);
        $cancelUrl = route('materials.show', $this->material).'#bottle-'.$bottle->id;

        [$response, $crawler] = getPageCrawler($this->user, route('bottles.edit', $bottle));

        $cancelLink = $crawler->filter('a:contains("CANCEL")');
        expect($cancelLink->attr('href'))->toBe($cancelUrl);
    });

    test('the bottle edit form posts to the bottle update route', function () {
        $bottle = makeBottle($this->material);
        $updateUrl = route('bottles.update', $bottle);

        [$response, $crawler] = getPageCrawler($this->user, route('bottles.edit', $bottle));

        $form = $crawler->filter('form#bottle-edit-form');

        $submitButton = $form->filter('button[type="submit"]');
        expect($submitButton->count())->toBe(1, 'Missing submit button');

        $csrfInput = $form->filter('input[type="hidden"][name="_token"]');
        expect($csrfInput->count())->toBe(1, 'Missing CSRF for the form');

        $patchOverride = $form->filter('input[name="_method"][value="PATCH"]');
        expect($patchOverride->count())->toBe(1, 'Missing patch method override');

        expect($form->attr('action'))->toBe($updateUrl, 'Edit form action does not match update route');
        expect($form->attr('method'))->toBe('POST', 'form\'s HTTP method should be POST');
    });
});

it('marks a bottle as finished from the material show page', function () {
    $bottle = makeBottle($this->material, [
        'supplier_name' => 'test supplier',
        'method' => 'steam_distilled',
    ]);
    $finishUrl = route('bottles.finish', $bottle);
    $showUrl = route('materials.show', $this->material);

    $finishResponse = postAs($this->user, $finishUrl);
    $finishResponse->assertRedirect(route('materials.show', $this->material));

    $bottle->refresh();
    expect($bottle->is_active)->toBeFalse();

    [$response, $crawler] = getPageCrawler($this->user, $showUrl);
    $bottleDiv = $crawler->filter("div#bottle-{$bottle->id}");
    expect($bottleDiv->text())->toContain('Finished');
    expect($bottleDiv->text())->not->toContain('In use');
});
