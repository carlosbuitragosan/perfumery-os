<?php

use App\Models\Bottle;
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
        'distillation_date' => '2021-01-30',
        'purchase_date' => '2025-03-01',
        'volume_ml' => 10,
        'density' => 0.912,
        'price' => 4.99,
        'notes' => 'test notes',
        'is_active' => true,
    ];

    return array_merge($base, $overrides);
}

// createa new bottle
function makeBottle(Material $material, array $overrides = []): Bottle
{
    return $material->bottles()->create(bottlePayload($overrides));
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
