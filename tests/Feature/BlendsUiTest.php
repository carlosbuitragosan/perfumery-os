<?php

use App\Models\User;

// Create user
beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('shows a "create blend" button on the dashboard', function () {
    $blendsUrl = route('blends.create');
    [, $crawler] = getPageCrawler($this->user, route('dashboard'));

    $link = $crawler->filter("a[href=\"{$blendsUrl}\"]");

    expect($link->count())->toBe(1);
    expect($link->text())->toContain('Create Blend');

});
