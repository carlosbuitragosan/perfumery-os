<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

// Bind Laravel’s base TestCase to all Feature tests
uses(Tests\TestCase::class)->in('Feature');

// Run migrations + refresh DB for every Feature test
uses(RefreshDatabase::class)->in('Feature');
