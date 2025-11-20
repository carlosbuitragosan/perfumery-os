<?php

use App\Models\Bottle;
use App\Models\BottleFile;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\DomCrawler\Crawler;

// Bind Laravel’s base TestCase to all Feature tests
uses(Tests\TestCase::class)->in('Feature');

// Run migrations + refresh DB for every Feature test
uses(RefreshDatabase::class)->in('Feature');

// Default payload for creating/updating a material
function materialPayload(array $overrides = []): array
{
    $base = [
        'name' => 'Lavender',
        'botanical' => 'Lavandula Angustifolia',
        'notes' => 'test',
        'pyramid' => ['top', 'heart'],
        'user_id' => auth()->id(),
    ];

    return array_merge($base, $overrides);
}

// create a new material
function makeMaterial(array $overrides = []): Material
{

    return Material::create(materialPayload($overrides));
}

// default payload for creating/updating a bottle
function bottlePayload(array $overrides = []): array
{
    $base = [
        'supplier_name' => 'Eden Botanicals',
        'supplier_url' => 'http://www.edenbotanicals.com',
        'batch_code' => 'AB1234',
        'method' => 'steam_distilled',
        'plant_part' => 'leaves',
        'origin_country' => 'Morocco',
        'purchase_date' => '2025-03-01',
        'expiry_date' => '2029-01-30',
        'volume_ml' => 10,
        'density' => 0.912,
        'price' => 4.99,
        'notes' => 'test notes',
        'is_active' => true,
    ];

    return array_merge($base, $overrides);
}

// create a new bottle
function makeBottle(Material $material, array $overrides = []): Bottle
{
    $withOwnership = array_merge(['user_id' => $material->user_id], $overrides);

    return $material->bottles()->create(bottlePayload($withOwnership));
}

// create a file for a bottle
function makeBottleFile(Bottle $bottle, array $overrides = []): BottleFile
{
    $base = [
        'user_id' => $bottle->user_id ?? auth()->id(),
        'path' => "bottles/{$bottle->id}/coa.pdf",
        'original_name' => 'coa.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 12345,
        'note' => null,
    ];

    return $bottle->files()->create(array_merge($base, $overrides));
}

// get a test instance test()
// login as the given user actingAs()
// send HTTP request to the URI
// return a TestResponse (HTML, headers, cookies, status, etc.)
function getAs(User $user, string $uri)
{
    return test()->actingAs($user)->get($uri);
}

// creates a post
function postAs(User $user, string $uri, array $data = [])
{
    $token = 'test-token';

    return test()
        ->actingAs($user)
        ->withSession(['_token' => $token])
        ->post($uri, array_merge($data, ['_token' => $token]));
}

// creates an update
function patchAs(User $user, string $uri, array $data = [])
{
    $token = 'test-token';

    return test()
        ->actingAs($user)
        ->withSession(['_token' => $token])
        ->patch($uri, array_merge($data, ['_token' => $token]));
}

// delete
function deleteAs(User $user, string $uri, array $data = [])
{
    $token = 'test-token';

    return test()
        ->actingAs($user)
        ->withSession(['_token' => $token])
        ->delete($uri, array_merge($data, ['_token' => $token]));
}

// Build a DomCrawler from the response and extract the HTML into an object
function crawl($response): Crawler
{
    return new Crawler($response->getContent());
}

// get response + crawler for a user's page visit
function getPageCrawler(User $user, string $url): array
{
    $response = getAs($user, $url)->assertOk();

    return [$response, crawl($response)];
}

// Assert a set of checkbox inputs exists by name/value
function assertInputs(Crawler $crawler, string $name, array $expected): void
{
    expect($crawler->filter("input[name=\"$name\"]")
        ->count())
        ->toBe(count($expected));

    foreach ($expected as $value) {
        expect($crawler->filter("input[name=\"$name\"][value=\"$value\"]")
            ->count())
            ->toBe(1, "Missing input[name=\"$name\"][value=\"$value\"]");
    }
}

// Assert specific checkbox values are checked
function assertChecked(Crawler $crawler, string $name, array $values): void
{
    foreach ($values as $value) {
        expect($crawler->filter("input[name=\"$name\"][value=\"$value\"]")
            ->attr('checked'))
            ->not->toBeNull("expected '$name' '$value' to be checked");
    }
}

// Assert specific checkbox values are not checked
function assertNotChecked(Crawler $crawler, string $name, array $values): void
{
    foreach ($values as $value) {
        expect($crawler->filter("input[name=\"$name\"][value=\"$value\"]")
            ->attr('checked'))
            ->toBeNull("Expected '$name' '$value' to be NOT checked");
    }
}
