<?php

use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\DomCrawler\Crawler;

uses(RefreshDatabase::class)->in('Feature');

// Create user
beforeEach(function () {
    $this->user = User::factory()->create();
});

// Default payload for creating/updating a material
function materialPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Lavender',
        'botanical' => 'Lavandula Angustifolia',
        'notes' => 'test',
        'pyramid' => ['top', 'heart'],
    ], $overrides);
}

// Persist a material quickly
function makeMaterial(array $overrides = []): material
{
    return Material::create(materialPayload($overrides));
}

// Act as user and GET/POST/PATCH
function getAs(User $user, string $uri)
{
    return test()->actingAs($user)->get($uri);
}

function postAs(User $user, string $uri, array $data = [])
{
    return test()->actingAs($user)->post($uri, $data);
}

function patchAs(User $user, string $uri, array $data = [])
{
    return test()->actingAs($user)->patch($uri, $data);
}

// Build a DomCrawler from the response
function crawl($response): Crawler
{
    return new Crawler($response->getContent());
}

// Assert a set of checkbox inputs exists by name/value
function assertInputs(Crawler $crawler, string $name, array $expected): void
{
    expect($crawler->filter("input[name=\"{$name}\"]")->count())->toBe(count($expected));

    foreach ($expected as $value) {
        expect($crawler->filter("input[name=\"{$name}\"][value=\"{$value}\"]")->count())->toBe(1, "Missing input[name=\"{$name}\"][value=\"{$value}\"]");
    }
}

// Assert specific checkbox values are checked / not checked
function assertChecked(Crawler $crawler, string $name, array $values): void
{
    foreach ($values as $value) {
        expect($crawler->filter("input[name=\"{$name}\"][value=\"{$value}\"]")->attr('checked'))->not->toBeNull("expected '{$name}' '{$value}' to be checked");
    }
}

function assertNotChecked(Crawler $crawler, string $name, array $values): void
{
    foreach ($values as $value) {
        expect($crawler->filter("input[name=\"{$name}\"][value=\"{$value}\"]")->attr('checked'))->toBeNull("Expected '{$name}' '{$value}' to be NOT checked");
    }
}

it('redirect guests to login on /materials', function () {
    $this->get('/materials')->assertRedirect('/login');
});

it('validates and creates a material via POST', function () {
    // Missing name -> validation error
    postAs($this->user, '/materials', materialPayload(['name' => '']))
        ->assertSessionHasErrors(['name']);

    // Valid create ->  redirect + persisted
    postAs($this->user, '/materials', materialPayload([
        'name' => 'Peppermint',
        'botanical' => 'Mentha piperita',
        'notes' => 'Fresh',
    ]))
        ->assertRedirect('/materials');

    $this->assertDatabaseHas('materials', ['name' => 'Peppermint']);
});

it('persists botanical when provided', function () {
    postAs($this->user, '/materials', materialPayload())
        ->assertRedirect('/materials');

    $this->assertDatabaseHas('materials', [
        'name' => 'Lavender',
        'botanical' => 'Lavandula Angustifolia',
    ]);
});

it('rejects duplicate material names (case-insensitive)', function () {
    makeMaterial(); // seeds Lavender

    expect(Material::count())->toBe(1);

    postAs($this->user, '/materials', materialPayload([
        'name' => 'Lavender',
    ]))
        ->assertSessionHasErrors(['name']);

    expect(Material::whereRaw('LOWER(name) = ?', ['lavender'])->count())->toBe(1);
});

it('shows the edit form for a material', function () {
    $material = makeMaterial();

    getAs($this->user, route('materials.edit', $material))
        ->assertOk()
        ->assertSee('Edit Material')
        ->assertSee('Lavender')
        ->assertSee('Lavandula Angustifolia');
});

it('links each material on the index to its edit page', function () {
    $material = makeMaterial();

    getAs($this->user, '/materials')
        ->assertSee(e(route('materials.edit', $material)));
});

it('updates a material and redirects', function () {
    $material = makeMaterial();

    patchAs($this->user, route('materials.update', $material), materialPayload([
        'name' => 'Lavendola',
    ]))->assertRedirect('/materials');

    $this->assertDatabaseHas('materials', [
        'id' => $material->id,
        'name' => 'Lavendola',
        'botanical' => 'Lavandula Angustifolia',
        'notes' => 'test',
    ]);
});

it('saves pyramid tiers for a material', function () {
    postAs($this->user, '/materials', materialPayload([
        'name' => 'Bergamot',
        'pyramid' => ['top'],
    ]))
        ->assertRedirect('/materials');

    $this->assertDatabaseHas('materials', [
        'name' => 'Bergamot',
    ]);

    $material = Material::where('name', 'Bergamot')->first();
    expect($material->pyramid)->toBe(['top']);
});

it('shows pyramid tier inputs on the create form', function () {
    $reponse = getAs($this->user, '/materials/create')->assertOk();
    $crawler = crawl($reponse);

    assertInputs($crawler, 'pyramid[]', ['top', 'heart', 'base']);
});

it('updates pyramid values for a material', function () {
    $material = makeMaterial();

    patchAs($this->user, route('materials.update', $material), materialPayload([
        'name' => 'Lavender',
        'pyramid' => ['top', 'heart'],
    ]))
        ->assertRedirect('/materials');

    $this->assertDatabaseHas('materials', [
        'id' => $material->id,
        'pyramid' => json_encode(['top', 'heart']),
    ]);
});

it('shows and pre-checks pyramid values on the edit form', function () {
    $material = makeMaterial();
    $response = getAs($this->user, route('materials.edit', $material))->assertOk();
    $crawler = crawl($response);

    assertChecked($crawler, 'pyramid[]', ['top', 'heart']);
    assertNotChecked($crawler, 'pyramid[]', ['base']);
});

it('creates a material with allowed taxonomy tags and IFRA percent', function () {
    postAs($this->user, '/materials', materialPayload([
        'families' => ['citrus'],
        'functions' => ['modifier'],
        'safety' => ['phototoxic', 'irritant'],
        'effects' => ['uplifting'],
        'ifra_max_pct' => 1.0,
    ]))
        ->assertRedirect('/materials');

    // Checks the database
    $material = Material::where('name', 'Lavender')->first();
    expect($material)->not->toBeNull();
    expect($material->families)->toContain('citrus');
    expect($material->functions)->toContain('modifier');
    expect($material->safety)->toContain('phototoxic', 'irritant');
    expect($material->effects)->toContain('uplifting');
    expect($material->ifra_max_pct)->toBeFloat()->toBe(1.0);
});

it('renders all taxonomy options on the create form', function () {
    $response = getAs($this->user, route('materials.create'))->assertOk();
    $crawler = crawl($response);

    assertInputs($crawler, 'families[]', config('materials.families'));
    assertInputs($crawler, 'functions[]', config('materials.functions'));
    assertInputs($crawler, 'safety[]', config('materials.safety'));
    assertInputs($crawler, 'effects[]', config('materials.effects'));

    expect($crawler->filter('input[name="ifra_max_pct"][type="number"]')->count())->toBe(1);
});

it('shows taxonomy fields on the create material form', function () {
    $response = getAs($this->user, '/materials/create')->assertOk();
    $crawler = crawl($response);

    assertInputs($crawler, 'families[]', config('materials.families'));
    assertInputs($crawler, 'functions[]', config('materials.functions'));
    assertInputs($crawler, 'safety[]', config('materials.safety'));
    assertInputs($crawler, 'effects[]', config('materials.effects'));

    // IFRA max %
    expect($crawler->filter('input[name="ifra_max_pct"][type="number"]')->count())->toBe(1, 'Missing IFRA max % input');
});

it('shows and pre-checks taxonomy fields on the edit form', function () {
    $material = Material::create(materialPayload([
        'families' => ['citrus'],
        'functions' => ['fixative'],
        'safety' => ['sensitizer'],
        'effects' => ['uplifting'],
        'ifra_max_pct' => 1.0,
    ]));
    $response = getAs($this->user, route('materials.edit', $material))->assertOk();
    $crawler = crawl($response);

    // All fields present
    assertInputs($crawler, 'families[]', config('materials.families'));
    assertInputs($crawler, 'functions[]', config('materials.functions'));
    assertInputs($crawler, 'safety[]', config('materials.safety'));
    assertInputs($crawler, 'effects[]', config('materials.effects'));

    // checked
    assertChecked($crawler, 'families[]', ['citrus']);
    assertChecked($crawler, 'functions[]', ['fixative']);
    assertChecked($crawler, 'safety[]', ['sensitizer']);
    assertChecked($crawler, 'effects[]', ['uplifting']);

    // unchecked sample
    assertNotChecked($crawler, 'families[]', ['floral']);
    assertNotChecked($crawler, 'safety[]', ['irritant']);

    // IFRA pre-filled value
    $ifra = $crawler->filter('input[name="ifra_max_pct"]')->attr('value');
    expect((float) $ifra)->toBe(1.0);
});

it('renders all taxonomy options on the edit form', function () {
    $material = Material::create(materialPayload());
    $response = getAs($this->user, route('materials.edit', $material))->assertOk();
    $crawler = crawl($response);

    assertInputs($crawler, 'families[]', config('materials.families'));
    assertInputs($crawler, 'functions[]', config('materials.functions'));
    assertInputs($crawler, 'safety[]', config('materials.safety'));
    assertInputs($crawler, 'effects[]', config('materials.effects'));

    expect($crawler->filter('input[name="ifra_max_pct"][type="number"]')->count())->toBe(1);
});

it('shows taxonomy tags for each material on the index', function () {
    Material::create(materialPayload([
        'families' => ['citrus'],
        'functions' => ['fixative'],
        'safety' => ['sensitizer'],
        'effects' => ['uplifting'],
        'ifra_max_pct' => 1.0,
    ]));

    $response = getAs($this->user, '/materials')->assertOk();

    $response
        ->assertSee('Lavender')
        ->assertSee('Lavandula Angustifolia')

        // Pyramid
        ->assertSee('Top')
        ->assertSee('Heart')

        // Functions / Safety / Effects
        ->assertSee('Citrus')
        ->assertSee('Fixative')
        ->assertSee('Sensitizer')
        ->assertSee('Uplifting')

        // IFRA formatted label
        ->assertSee('IFRA4 1%');
});

it('shows a search input on the materials index', function () {
    getAs($this->user, 'materials')
        ->assertOk()
        ->assertSee('name="query"', false)
        ->assertSee('type="search"', false);
});

it('filters materials by keywords, based on the material information', function () {
    $lavender = makeMaterial(materialPayload([
        'families' => ['herbal', 'floral'],
        'effects' => ['calming'],
    ]));

    $bergamot = makeMaterial(materialPayload([
        'name' => 'Bergamot',
        'botanical' => 'Citrus Bergamia',
        'families' => ['citrus', 'herbal'],
        'pyramid' => ['heart'],
        'effects' => ['uplifting'],
        'notes' => 'beautiful',
    ]));

    // Name search
    getAs($this->user, '/materials?query=lave')
        ->assertOk()
        ->assertSee('Lavender')
        ->assertDontSee('Bergamot');

    // Family search
    getAs($this->user, '/materials?query=citr')
        ->assertOK()
        ->assertSee('Bergamot')
        ->assertDontSee('Lavender');

    // pyramid search
    getAs($this->user, '/materials?query=heart')
        ->assertOK()
        ->assertSee('Bergamot')
        ->assertSee('Lavender');

    // Effects search
    getAs($this->user, '/materials?query=uplift')
        ->assertOK()
        ->assertSee('Bergamot')
        ->assertDontSee('Lavender');

    // botanical name search
    getAs($this->user, '/materials?query=angust')
        ->assertOK()
        ->assertSee('Lavender')
        ->assertDontSee('Bergamot');

    // Notes search
    getAs($this->user, '/materials?query=beauti')
        ->assertOK()
        ->assertSee('Bergamot')
        ->assertDontSee('Lavender');
});
