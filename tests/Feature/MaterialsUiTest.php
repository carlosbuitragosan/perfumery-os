<?php

use App\Livewire\MaterialsIndex;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class)->in('Feature');

// Create user
beforeEach(function () {
    $this->user = User::factory()->create();
});

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

it('shows a cancel link on the create material page that returns to index', function () {
    $response = getAs($this->user, '/materials/create')->assertOk();
    $indexUrl = route('materials.index');
    $formAction = route('materials.store');
    $crawler = crawl($response);
    $cancelLink = $crawler->filter('form[action="'.$formAction.'"] a[href="'.$indexUrl.'"]');
    $label = trim($cancelLink->text());

    expect(strtolower($label))->toContain('cancel');
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

    $response = patchAs($this->user, route('materials.update', $material), materialPayload([
        'name' => 'Lavendola',
    ]));

    $expectedUrl = route('materials.index').'#material-'.$material->id;

    $response->assertRedirect($expectedUrl);

    $this->assertDatabaseHas('materials', [
        'id' => $material->id,
        'name' => 'Lavendola',
        'botanical' => 'Lavandula Angustifolia',
        'notes' => 'test',
    ]);
});

it('shows a cancel link on the edit page that returns to the index', function () {
    $material = makeMaterial();
    // visit the edit page
    $response = getAs($this->user, route('materials.edit', $material))->assertOk();
    // build url for the edit form
    $formAction = route('materials.update', $material);
    // build url for the materials index page
    $indexUrl = route('materials.index');
    // turn html response into a DomCrawler object
    $crawler = crawl($response);
    // find the cancel link (inside the form)
    $cancelLink = $crawler->filter('form[action="'.$formAction.'"] a[href="'.$indexUrl.'#material-'.$material->id.'"]');
    // grag the visible text of that link
    $label = trim($cancelLink->text());

    expect(strtolower($label))->toContain('cancel');
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

    $response = patchAs($this->user, route('materials.update', $material), materialPayload([
        'name' => 'Lavender',
        'pyramid' => ['top', 'heart'],
    ]));

    $expectedUrl = route('materials.index').'#material-'.$material->id;

    $response->assertRedirect($expectedUrl);

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

it('shows a clear button only when a query is present', function () {
    getAs($this->user, '/materials')
        ->assertOk()
        ->assertDontsee('aria-label="Clear search"', false);

    getAs($this->user, '/materials?query=lav')
        ->assertOk()
        ->assertSee('aria-label="Clear search"', false);
});

it('clears the query and input when clicking the clear button', function () {
    Livewire::test(MaterialsIndex::class, ['query' => 'lav'])
        ->assertSee('query', 'lav')
        ->assertSee('aria-label="Clear search"', false)
        ->set('query', '')
        ->assertSet('query', '')
        ->assertDontSee('aria-label="Clear search"', false);
});

it('finds materials that have an IFRA tag when searching "ifra"', function () {
    $hasIfra = makeMaterial(materialPayload([
        'ifra_max_pct' => 1.0,
    ]));

    $noIfra = makeMaterial(materialPayload([
        'name' => 'Cedar',
        'ifra_max_pct' => null,
    ]));

    getAs($this->user, '/materials?query=ifra')
        ->assertOk()
        ->assertSee('Lavender')
        ->assertDontSee('Cedar');
});

it('links each material on the index to its show page', function () {
    $material = (makeMaterial());

    getAs($this->user, '/materials')
        ->assertOk()
        ->assertSee(e(route('materials.show', $material)), false);
});

it('shows all materials without pagination', function () {
    for ($i = 1; $i <= 30; $i++) {
        makeMaterial(['name' => "Material {$i}"]);
    }

    $response = Livewire::test(MaterialsIndex::class)
        ->assertStatus(200);

    $html = $response->html();

    expect($html)->toContain('Material 20');
    expect($html)->toContain('Material 29');
    expect($html)->not->toContain('Next');
});

it('editing a material redirects back to the material list anchored to the updated material', function () {
    $material = makeMaterial(['name' => 'Jasmine']);
    $payload = ['name' => 'Lavender'];

    $response = patchAs($this->user, route('materials.update', $material), $payload);

    $expectedUrl = route('materials.index').'#material-'.$material->id;

    $response->assertRedirect($expectedUrl);

    $html = Livewire::test(MaterialsIndex::class)
        ->assertStatus(200)
        ->html();

    expect($html)->toContain('id="material-'.$material->id.'"');
});

it('the cancel link on edit material form returns to the anchored material position', function () {
    $material = makeMaterial(['name' => 'Bergamot']);

    $response = getAs($this->user, route('materials.edit', $material))
        ->assertOk();

    $html = $response->getContent();

    $expectedHref = route('materials.index').'#material-'.$material->id;

    expect($html)->toContain('href="'.$expectedHref.'"');
});

it('shows the newly added family descriptors in the material create form', function () {
    $response = getAs($this->user, route('materials.create'))
        ->assertOk();
    $crawler = crawl($response);

    // helper to check each value
    $assertFamilyCheckbox = function (string $value) use ($crawler) {
        $selector = 'input[type="checkbox"][name="families[]"][value="'.$value.'"]';

        expect($crawler->filter($selector)->count())
            ->toBeGreaterThan(0, "Missing checkbox for family '{$value}'");
    };

    $assertFamilyCheckbox('creamy');
    $assertFamilyCheckbox('earthy');
    $assertFamilyCheckbox('powdery');
    $assertFamilyCheckbox('musky');
    $assertFamilyCheckbox('camphorous');
});

it('allows saving new family descriptors on material create', function () {
    $payload = materialPayload([
        'name' => 'my oil',
        'families' => ['creamy', 'camphorous', 'musky', 'earthy'],
    ]);

    $response = postAs($this->user, route('materials.store'), $payload)
        ->assertRedirect();

    $material = Material::where('name', $payload['name'])->first();

    expect($material->families)->toEqualCanonicalizing(['creamy', 'camphorous', 'musky', 'earthy']);
});
