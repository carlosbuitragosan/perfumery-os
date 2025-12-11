<?php

use App\Models\Blend;
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

it('creates a blend and redirects to its page', function () {
    $postUrl = route('blends.store');

    $response = postAs($this->user, $postUrl, [
        'name' => 'Sunshine',
    ]);

    $this->assertDatabaseHas('blends', [
        'user_id' => $this->user->id,
        'name' => 'Sunshine',
    ]);

    $blend = Blend::where('user_id', $this->user->id)
        ->where('name', 'Sunshine')
        ->firstOrFail();

    $redirectUrl = route('blends.show', ['blend' => $blend]);

    $response->assertRedirect($redirectUrl);
});

it('shows existing blend on the dashboard', function () {
    $blend = Blend::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Sunshine',
    ]);

    [, $crawler] = getPageCrawler($this->user, route('dashboard'));

    $link = $crawler->filter('a[href="'.route('blends.show', $blend).'"]');

    expect($link->count())->toBe(1);
    expect($link->text())->toContain('Sunshine');
});
