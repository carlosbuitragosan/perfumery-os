<?php

use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\DomCrawler\Crawler;

uses(RefreshDatabase::class)->in('Feature');

beforeEach(function () {
    $this->user = User::Factory()->create();
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
        ]))
        ->assertRedirect('/materials');

    $this->assertDatabaseHas('materials', [
        'id' => $material->id,
        'name' => 'Lavendola',
        'botanical' => 'Lavandula Angustifolia',
        'notes' => 'test',
    ]);
});

it('saves pyramid tiers for a material', function () {
    $this->actingAs($this->user)
        ->post('/materials', materialPayload([
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
    $this->actingAs($this->user)
        ->get('/materials/create')
        ->assertOk()
        ->assertSee('Pyramid')
        ->assertSee('name="pyramid[]"', false)
        ->assertSee('value="top"', false)
        ->assertSee('value="heart"', false)
        ->assertSee('value="base"', false);
});

it('updates pyramid values for a material', function () {
    $material = makeMaterial();

    $this->actingAs($this->user)
        ->patch(route('materials.update', $material), materialPayload([
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

    $this->actingAs($this->user)
        ->get(route('materials.edit', $material))
        ->assertOk()
        ->assertSee('Pyramid')
        ->assertSee('name="pyramid[]"', false)
        ->assertSee('value="top"', false)
        ->assertSee('value="heart"', false)
        ->assertSee('value="base"', false)
        ->assertSeeInOrder(['value="top"', 'checked'], false)
        ->assertSeeInOrder(['value="heart"', 'checked'], false)
        ->assertDontSee('value="base" checked', false);
});

it('creates a material with allowed taxonomy tags and IFRA percent', function () {
    $this->actingAs($this->user)
        ->post('/materials', materialPayload([
            'families' => ['citrus'],
            'functions' => ['modifier'],
            'safety' => ['phototoxic', 'irritant'],
            'effects' => ['uplifting'],
            'ifra_max_pct' => 1.0,
        ]))
        ->assertRedirect('/materials');

    $material = Material::where('name', 'Lavender')->first();
    expect($material)->not->toBeNull();
    expect($material->families)->toContain('citrus');
    expect($material->functions)->toContain('modifier');
    expect($material->safety)->toContain('phototoxic', 'irritant');
    expect($material->effects)->toContain('uplifting');
    expect($material->ifra_max_pct)->toBeFloat()->toBe(1.0);
});

it('renders all taxonomy options on the create form', function () {
    $material = Material::create(materialPayload());
    $response = $this->actingAs($this->user)->get(route('materials.create', $material));
    $response->assertOk();
    $crawler = new Crawler($response->getContent());

    $families = config('materials.families');
    $functions = config('materials.functions');
    $safety = config('materials.safety');
    $effects = config('materials.effects');

    $assertInputs = function (array $expected, string $name) use ($crawler) {
        expect($crawler->filter("input[name=\"{$name}\"]")->count())->toBe(count($expected));

        foreach ($expected as $value) {
            expect($crawler->filter("input[name=\"{$name}\"][value=\"{$value}\"]")->count())->toBe(1, "Missing input[name=\"{$name}\"][value=\"{$value}\"]");
        }
    };

    $assertInputs($families, 'families[]');
    $assertInputs($functions, 'functions[]');
    $assertInputs($safety, 'safety[]');
    $assertInputs($effects, 'effects[]');

    expect($crawler->filter('input[name="ifra_max_pct"][type="number"]')->count())->toBe(1);
});

it('shows taxonomy fields on the create material form', function () {
    $this->actingAs($this->user)
        ->get('/materials/create')
        ->assertOk()

    // Families"
        ->assertSee('name="families[]"', false)
        ->assertSee('value="citrus"', false)
        ->assertSee('value="floral"', false)
        ->assertSee('value="herbal"', false)
        ->assertSee('value="woody"', false)
        ->assertSee('value="resinous"', false)

    // Functions
        ->assertSee('name="functions[]"', false)
        ->assertSee('value="fixative"', false)
        ->assertSee('value="modifier"', false)

    // Safety
        ->assertSee('name="safety[]"', false)
        ->assertSee('value="phototoxic"', false)
        ->assertSee('value="irritant"', false)
        ->assertSee('value="allergenic"', false)
        ->assertSee('value="sensitizer"', false)

    // Effects
        ->assertSee('name="effects[]"', false)
        ->assertSee('value="calming"', false)
        ->assertSee('value="uplifting"', false)
        ->assertSee('value="grounding"', false)
        ->assertSee('value="sedative"', false)
        ->assertSee('value="aphrodisiac"', false)
        ->assertSee('value="stimulant"', false)

    // IFRA max %
        ->assertSee('name="ifra_max_pct"', false)
        ->assertSee('type="number"', false);
});

it('shows and pre-checks taxonomy fields on the edit form', function () {
    $material = Material::create(materialPayload([
        'families' => ['citrus'],
        'functions' => ['fixative'],
        'safety' => ['sensitizer'],
        'effects' => ['uplifting'],
        'ifra_max_pct' => 1.0,
    ]));

    $this->actingAs($this->user)
        ->get(route('materials.edit', $material))
        ->assertOk()

        // Fields exist
        ->assertSee('name="families[]"', false)
        ->assertSee('name="functions[]"', false)
        ->assertSee('name="safety[]"', false)
        ->assertSee('name="effects[]"', false)
        ->assertSee('name="ifra_max_pct"', false);

    $response = $this->actingAs($this->user)->get(route('materials.edit', $material));
    $response->assertOk();
    $html = $response->getContent();
    $crawler = new Crawler($html);

    // checked
    expect($crawler->filter('input[name="families[]"][value="citrus"]')->attr('checked'))->not->toBeNull();
    expect($crawler->filter('input[name="functions[]"][value="fixative"]')->attr('checked'))->not->toBeNull();
    expect($crawler->filter('input[name="safety[]"][value="sensitizer"]')->attr('checked'))->not->toBeNull();
    expect($crawler->filter('input[name="effects[]"][value="uplifting"]')->attr('checked'))->not->toBeNull();

    // unchecked
    expect($crawler->filter('input[name="families[]"][value="floral"]')->attr('checked'))->toBeNull();
    expect($crawler->filter('input[name="safety[]"][value="irritant"]')->attr('checked'))->toBeNull();

    // IFRA pre-filled value
    $ifra = $crawler->filter('input[name="ifra_max_pct"]')->attr('value');
    expect((float) $ifra)->toBe(1.0);
});

it('renders all taxonomy options on the edit form', function () {
    $material = Material::create(materialPayload());
    $response = $this->actingAs($this->user)->get(route('materials.edit', $material));
    $response->assertOk();
    $crawler = new Crawler($response->getContent());

    $families = config('materials.families');
    $functions = config('materials.functions');
    $safety = config('materials.safety');
    $effects = config('materials.effects');

    $assertInputs = function (array $expected, string $name) use ($crawler) {
        expect($crawler->filter("input[name=\"{$name}\"]")->count())->toBe(count($expected));

        foreach ($expected as $value) {
            expect($crawler->filter("input[name=\"{$name}\"][value=\"{$value}\"]")->count())->toBe(1, "Missing input[name=\"{$name}\"][value=\"{$value}\"]");
        }
    };

    $assertInputs($families, 'families[]');
    $assertInputs($functions, 'functions[]');
    $assertInputs($safety, 'safety[]');
    $assertInputs($effects, 'effects[]');

    expect($crawler->filter('input[name="ifra_max_pct"][type="number"]')->count())->toBe(1);
});

it('shows taxonomy tags for each material on the index', function () {
    $material = Material::create(materialPayload([
        'families' => ['citrus'],
        'functions' => ['fixative'],
        'safety' => ['sensitizer'],
        'effects' => ['uplifting'],
        'ifra_max_pct' => 1.0,
    ]));

    $response = $this->actingAs($this->user)->get('/materials');
    $response->assertOk();

    $response
        ->assertSee('Lavender')
        ->assertSee('Lavandula Angustifolia')
        ->assertSee('Top')
        ->assertSee('Heart')
        ->assertSee('Citrus')
        ->assertSee('Fixative')
        ->assertSee('Sensitizer')
        ->assertSee('Uplifting')
        ->assertSee('IFRA4 1%');
});
