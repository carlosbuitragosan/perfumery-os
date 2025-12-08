<?php

use App\Models\User;

// Create user
beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('shows a "create blend" button on the dashboard', function () {
    $createBlendsUrl = route('blends.create');
    [, $crawler] = getPageCrawler($this->user, route('dashboard'));

    $link = $crawler->filter("a[href=\"{$createBlendsUrl}\"]");

    expect($link->count())->toBe(1);
    expect($link->text())->toContain('Create Blend');

});

it('shows the create blend form', function () {
    $createBlendsUrl = route('blends.create');
    [, $crawler] = getPageCrawler($this->user, $createBlendsUrl);

    $form = $crawler->filter('form#create-blend-form');

    expect($form->count())->toBe(1);
    expect($form->filter('select[name="materials[0][material_id]"]')->count())->toBe(1);
    expect($form->filter('input[name="name"]')->count())->toBe(1);
    expect($form->filter('input[name="materials[0][drops]"]')->count())->toBe(1);
    expect($form->filter('select[name="materials[0][dilution]"]')->count())->toBe(1);
    expect($form->filter('button[type="submit"]')->text())->toContain('SAVE');
});
