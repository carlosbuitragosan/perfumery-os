<?php

use App\Livewire\MaterialsIndex;
use App\Models\Material;
use App\Models\User;
use Livewire\Livewire;

// Create user
beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    // $this->material = makeMaterial();
});

describe('authentication', function () {
    it('redirects guests to login on /materials', function () {
        auth()->logout();

        $this->get('/materials')->assertRedirect('/login');
    });
});

describe('creating materials', function () {
    it('validates name when creating a material', function () {
        $postUrl = route('materials.store');

        postAs($this->user, $postUrl, materialPayload(['name' => '']))
            ->assertSessionHasErrors(['name']);
    });

    it('createa a material and redirects to index', function () {
        $postUrl = route('materials.store');
        $redirectUrl = route('materials.index');

        postAs($this->user, $postUrl, materialPayload())
            ->assertRedirect($redirectUrl);

        $this->assertDatabaseHas('materials', ['name' => 'Lavender']);
    });

    it('persists botanical when provided', function () {
        $postUrl = route('materials.store');
        $redirectUrl = route('materials.index');

        postAs($this->user, $postUrl, materialPayload())
            ->assertRedirect($redirectUrl);

        $this->assertDatabaseHas('materials', [
            'user_id' => $this->user->id,
            'name' => 'Lavender',
            'botanical' => 'Lavandula Angustifolia',
        ]);
    });

    it('rejects duplicate material names (case-insensitive)', function () {
        $postUrl = route('materials.store');
        makeMaterial(); // seeds Lavender

        expect(Material::count())->toBe(1);

        // tries to post same material (Lavender)
        postAs($this->user, $postUrl, materialPayload(['name' => 'lavender']))
            ->assertSessionHasErrors(['name']);

        expect(Material::whereRaw('LOWER(name) = ?', ['lavender'])->count())->toBe(1);
    });

    it('shows a cancel link on the create material page that returns to index', function () {
        $createUrl = route('materials.create');
        $indexUrl = route('materials.index');
        $formAction = route('materials.store');
        [$response, $crawler] = getPageCrawler($this->user, $createUrl);

        $cancelLink = $crawler->filter("form[action=\"$formAction\"] a[href=\"$indexUrl\"]");

        expect($cancelLink->count())->toBe(1);
        expect($cancelLink->text())->toContain('CANCEL');
    });

    it('saves pyramid tiers for a material', function () {
        $postUrl = route('materials.store');
        $indexUrl = route('materials.index');
        $payload = materialPayload([
            'name' => 'Bergamot',
            'pyramid' => ['top'],
        ]);

        postAs($this->user, $postUrl, $payload)
            ->assertRedirect($indexUrl);

        $this->assertDatabaseHas('materials', [
            'name' => 'Bergamot',
        ]);

        $material = Material::where('name', 'Bergamot')->first();
        expect($material->pyramid)->toBe(['top']);
    });

    it('shows pyramid tier inputs on the create form', function () {
        $createUrl = route('materials.create');
        [, $crawler] = getPageCrawler($this->user, $createUrl);

        assertInputs($crawler, 'pyramid[]', ['top', 'heart', 'base']);
    });

    it('creates a material with allowed taxonomy tags and IFRA percent', function () {
        $postUrl = route('materials.store');
        $indexUrl = route('materials.index');
        $payload = materialPayload([
            'families' => ['citrus'],
            'functions' => ['modifier'],
            'safety' => ['phototoxic', 'irritant'],
            'effects' => ['uplifting'],
            'ifra_max_pct' => 1.0,
        ]);

        postAs($this->user, $postUrl, $payload)
            ->assertRedirect($indexUrl);

        // Checks the database
        $material = Material::where('name', 'Lavender')->first();
        expect($material)->not->toBeNull();
        expect($material->families)->toContain('citrus');
        expect($material->functions)->toContain('modifier');
        expect($material->safety)->toContain('phototoxic');
        expect($material->safety)->toContain('irritant');
        expect($material->effects)->toContain('uplifting');
        expect($material->ifra_max_pct)->toBeFloat()->toBe(1.0);
    });

    it('shows taxonomy fields on the create material form', function () {
        $createUrl = route('materials.create');
        [, $crawler] = getPageCrawler($this->user, $createUrl);

        assertInputs($crawler, 'families[]', config('materials.families'));
        assertInputs($crawler, 'functions[]', config('materials.functions'));
        assertInputs($crawler, 'safety[]', config('materials.safety'));
        assertInputs($crawler, 'effects[]', config('materials.effects'));

        expect($crawler->filter('input[name="ifra_max_pct"][type="number"]')->count())->toBe(1, 'Missing IFRA max % input');
    });

    it('shows the newly added family descriptors in the material create form', function () {
        $createUrl = route('materials.create');
        [$response, $crawler] = getPageCrawler($this->user, $createUrl);

        // helper to check each value
        $assertFamilyCheckbox = function (string $value) use ($crawler) {
            $selector = "input[type='checkbox'][name='families[]'][value='{$value}']";

            expect($crawler->filter($selector)->count())
                ->toBe(1, "Missing checkbox for family '{$value}'");
        };

        $assertFamilyCheckbox('creamy');
        $assertFamilyCheckbox('earthy');
        $assertFamilyCheckbox('powdery');
        $assertFamilyCheckbox('musky');
        $assertFamilyCheckbox('camphorous');
    });

    it('allows saving new family descriptors on material create', function () {
        $storeUrl = route('materials.store');
        $payload = materialPayload([
            'name' => 'my oil',
            'families' => ['creamy', 'camphorous', 'musky', 'earthy'],
        ]);

        $response = postAs($this->user, $storeUrl, $payload)
            ->assertRedirect();

        $material = Material::where('name', $payload['name'])->first();

        expect($material->families)->toEqualCanonicalizing(['creamy', 'camphorous', 'musky', 'earthy']);
    });
});

describe('editing materials', function () {
    it('shows the edit form for a material', function () {
        $material = makeMaterial();
        $editUrl = route('materials.edit', $material);

        getAs($this->user, $editUrl)
            ->assertOk()
            ->assertSee('Edit Material')
            ->assertSee('Lavender')
            ->assertSee('Lavandula Angustifolia');
    });

    it('updates a material and redirects', function () {
        $material = makeMaterial();
        $updateUrl = route('materials.update', $material);
        $expectedUrl = route('materials.index').'#material-'.$material->id;

        patchAs($this->user, $updateUrl, materialPayload([
            'name' => 'Lavendola',
        ]))->assertRedirect($expectedUrl);

        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'name' => 'Lavendola',
            'botanical' => 'Lavandula Angustifolia',
            'notes' => 'test',
        ]);
    });

    it('shows a cancel link on the edit page that returns to the index', function () {
        $material = makeMaterial();
        $editUrl = route('materials.edit', $material);
        $updateUrl = route('materials.update', $material);
        $indexUrl = route('materials.index').'#material-'.$material->id;

        [$response, $crawler] = getPageCrawler($this->user, $editUrl);

        $cancelLink = $crawler->filter("form[action=\"$updateUrl\"] a[href=\"$indexUrl\"]");

        expect($cancelLink->count())->toBe(1);
        expect($cancelLink->text())->toContain('CANCEL');
    });

    it('shows and pre-checks taxonomy fields on the edit form', function () {
        $material = makeMaterial([
            'families' => ['citrus'],
            'functions' => ['fixative'],
            'safety' => ['sensitizer'],
            'effects' => ['uplifting'],
            'ifra_max_pct' => 1.0,
        ]);
        $editUrl = route('materials.edit', $material);
        [, $crawler] = getPageCrawler($this->user, $editUrl);

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
        $ifraValue = $crawler->filter('input[name="ifra_max_pct"]')->attr('value');
        expect((float) $ifraValue)->toBe(1.0);
    });

    it('updates pyramid values for a material', function () {
        $material = makeMaterial();
        $updateUrl = route('materials.update', $material);
        $expectedUrl = route('materials.index').'#material-'.$material->id;
        $payload = materialPayload([
            'name' => 'Lavender',
            'pyramid' => ['top', 'heart'],
        ]);

        patchAs($this->user, $updateUrl, $payload)
            ->assertRedirect($expectedUrl);

        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'pyramid' => json_encode(['top', 'heart']),
        ]);
    });

    it('shows and pre-checks pyramid values on the edit form', function () {
        $material = makeMaterial();
        $editUrl = route('materials.edit', $material);
        [, $crawler] = getPageCrawler($this->user, $editUrl);

        assertChecked($crawler, 'pyramid[]', ['top', 'heart']);
        assertNotChecked($crawler, 'pyramid[]', ['base']);
    });

    it('editing a material redirects back to the material list anchored to the updated material', function () {
        $material = makeMaterial(['name' => 'Jasmine']);
        $payload = ['name' => 'Lavender'];
        $updateUrl = route('materials.update', $material);
        $indexUrl = route('materials.index').'#material-'.$material->id;

        patchAs($this->user, $updateUrl, $payload)
            ->assertRedirect($indexUrl);

        [, $crawler] = getPageCrawler($this->user, $indexUrl);

        $materialDiv = $crawler->filter("#material-{$material->id}");
        expect($materialDiv->count())->toBe(1);
        expect($materialDiv->text())->toContain('Lavender');
    });

    it('the cancel link on edit material form returns to the anchored material position', function () {
        $material = makeMaterial(['name' => 'Bergamot']);
        $editUrl = route('materials.edit', $material);
        $indexUrl = route('materials.index').'#material-'.$material->id;

        [$response, $crawler] = getPageCrawler($this->user, $editUrl);

        $cancelLink = $crawler->filter("a[href=\"$indexUrl\"]");

        expect($cancelLink->count())->toBe(1);
        expect($cancelLink->text())->toBe('CANCEL');
    });
});

describe('materials index listing and navigation', function () {
    it('links each material on the index to its edit page', function () {
        $material = makeMaterial();
        $indexUrl = route('materials.index');
        $editUrl = route('materials.edit', $material);

        [$response, $crawler] = getPageCrawler($this->user, $indexUrl);

        $materialDiv = $crawler->filter("div#material-{$material->id}");
        $href = $materialDiv->filter('a')->attr('href');

        expect($materialDiv->count())->toBe(1);
        expect($href)->toBe($editUrl);
    });

    it('links each material on the index to its show page', function () {
        $material = makeMaterial();
        $indexUrl = route('materials.index');
        $showUrl = route('materials.show', $material);

        [, $crawler] = getPageCrawler($this->user, $indexUrl);

        expect($crawler->filter("#material-{$material->id}")->count())->toBe(1);
        expect($crawler->html())->toContain($showUrl);
    });

    it('shows taxonomy tags for each material on the index', function () {
        $indexUrl = route('materials.index');
        $material = makeMaterial([
            'families' => ['citrus'],
            'functions' => ['fixative'],
            'safety' => ['sensitizer'],
            'effects' => ['uplifting'],
            'ifra_max_pct' => 1.0,
        ]);

        [, $crawler] = getPageCrawler($this->user, $indexUrl);

        $materialDiv = $crawler->filter("div#material-{$material->id}");

        expect($materialDiv->text())->toContain('Lavender');
        expect($materialDiv->text())->toContain('Lavandula Angustifolia');
        expect($materialDiv->text())->toContain('Top');
        expect($materialDiv->text())->toContain('Heart');
        expect($materialDiv->text())->toContain('Citrus');
        expect($materialDiv->text())->toContain('Fixative');
        expect($materialDiv->text())->toContain('Sensitizer');
        expect($materialDiv->text())->toContain('Uplifting');
        expect($materialDiv->text())->toContain('IFRA4 1%');
    });

    it('shows all materials without pagination', function () {
        $indexUrl = route('materials.index');

        for ($i = 1; $i <= 30; $i++) {
            makeMaterial(['name' => "Material {$i}"]);
        }

        [, $crawler] = getPageCrawler($this->user, $indexUrl);

        expect($crawler->text())->toContain('Material 20');
        expect($crawler->text())->toContain('Material 29');
        expect($crawler->text())->not->toContain('Next');
    });
});

describe('search and filtering', function () {
    it('shows a search input on the materials index', function () {
        getAs($this->user, 'materials')
            ->assertOk()
            ->assertSee('name="query"', false)
            ->assertSee('type="search"', false);
    });

    it('filters materials by keywords, based on the material information', function () {
        $lavender = makeMaterial([
            'families' => ['herbal', 'floral'],
            'effects' => ['calming'],
        ]);

        $bergamot = makeMaterial([
            'name' => 'Bergamot',
            'botanical' => 'Citrus Bergamia',
            'families' => ['citrus', 'herbal'],
            'pyramid' => ['heart'],
            'effects' => ['uplifting'],
            'notes' => 'beautiful',
        ]);

        // Name search
        getAs($this->user, '/materials?query=lave')
            ->assertOk()
            ->assertSee('Lavender')
            ->assertDontSee('Bergamot');

        // Family search
        getAs($this->user, '/materials?query=citr')
            ->assertOk()
            ->assertSee('Bergamot')
            ->assertDontSee('Lavender');

        // pyramid search
        getAs($this->user, '/materials?query=heart')
            ->assertOk()
            ->assertSee('Bergamot')
            ->assertSee('Lavender');

        // Effects search
        getAs($this->user, '/materials?query=uplift')
            ->assertOk()
            ->assertSee('Bergamot')
            ->assertDontSee('Lavender');

        // botanical name search
        getAs($this->user, '/materials?query=angust')
            ->assertOk()
            ->assertSee('Lavender')
            ->assertDontSee('Bergamot');

        // Notes search
        getAs($this->user, '/materials?query=beauti')
            ->assertOk()
            ->assertSee('Bergamot')
            ->assertDontSee('Lavender');
    });

    it('shows a clear button only when a query is present', function () {
        $clearButton = 'aria-label="Clear search"';

        getAs($this->user, '/materials')
            ->assertOk()
            ->assertDontsee($clearButton, false);

        getAs($this->user, '/materials?query=lav')
            ->assertOk()
            ->assertSee($clearButton, false);
    });

    it('clears the query and input when clicking the clear button', function () {
        $clearButton = 'aria-label="Clear search"';

        Livewire::test(MaterialsIndex::class, ['query' => 'lav'])
        // assertSet checks livewire component state
            ->assertSet('query', 'lav')
        // assertSee checks HTML output
            ->assertSee($clearButton, false)
            ->set('query', '')
            ->assertSet('query', '')
            ->assertDontSee($clearButton, false);
    });

    it('finds materials that have an IFRA tag when searching "ifra"', function () {
        $hasIfra = makeMaterial([
            'ifra_max_pct' => 1.0,
        ]);

        $noIfra = makeMaterial([
            'name' => 'Cedar',
            'ifra_max_pct' => null,
        ]);

        getAs($this->user, '/materials?query=ifra')
            ->assertOk()
            ->assertSee('Lavender')
            ->assertDontSee('Cedar');
    });
});
