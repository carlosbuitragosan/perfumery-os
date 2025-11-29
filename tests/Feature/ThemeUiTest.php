<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('shows a theme toggle with system, light and dark options', function () {
    $indexUrl = route('materials.index');
    [, $crawler] = getPageCrawler($this->user, $indexUrl);

    expect($crawler->filter('button[data-theme-option="light"]')->count())->toBe(1);
    expect($crawler->filter('button[data-theme-option="dark"]')->count())->toBe(1);
});
