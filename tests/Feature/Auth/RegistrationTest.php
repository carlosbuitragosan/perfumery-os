<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registration is disabled', function () {
    $this->get('/register')->assertNotFound();
});
