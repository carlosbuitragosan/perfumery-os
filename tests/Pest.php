<?php

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
    return array_merge([
        'name' => 'Lavender',
        'botanical' => 'Lavandula Angustifolia',
        'notes' => 'test',
        'pyramid' => ['top', 'heart'],
    ], $overrides);
}

function makeMaterial(array $overrides = []): Material
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
