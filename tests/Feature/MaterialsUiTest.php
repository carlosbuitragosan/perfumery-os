<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirect guests to login on /materials', function () {
    $this->get('/materials')->assertRedirect('/login');
});
