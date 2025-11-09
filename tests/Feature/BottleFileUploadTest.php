<?php

use App\Models\Bottle;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

it('allows a user to upload files when creating a bottle', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $this->actingAs($user);

    $material = makeMaterial();
    $file1 = UploadedFile::fake()->create('coa.pdf', 200, 'application/pdf');
    $file2 = UploadedFile::fake()->image('bottle.jpg');
    $payload = bottlePayload(['files' => [$file1, $file2]]);
    $postUrl = route('materials.bottles.store', $material);

    $response = postAs($user, $postUrl, $payload);
    $bottle = Bottle::first();
    $response->assertRedirect(route('materials.show', $bottle->material));

    Storage::disk('public')->assertExists("bottles/{$bottle->id}/coa.pdf");
    Storage::disk('public')->assertExists("bottles/{$bottle->id}/bottle.jpg");

    expect(DB::table('bottle_files')->count())->toBe(2);
    expect(DB::table('bottle_files')
        ->where('original_name', 'coa.pdf')
        ->where('bottle_id', $bottle->id)
        ->exists()
    )->toBeTrue();
});
