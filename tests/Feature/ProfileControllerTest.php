<?php

namespace Tests\Feature;

use App\Enums\EProfileStatus;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fetch_all_active_profiles_for_public_users()
    {
        Profile::factory()->count(3)->create(['status' => EProfileStatus::Active]);

        $response = $this->getJson('api/profiles');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    /** @test */
    public function it_can_fetch_all_active_profiles_for_admin_users()
    {
        $admin = $this->createAdminUser();
        Profile::factory()->count(3)->create(['status' => EProfileStatus::Active]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('api/profiles');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    /** @test */
    public function it_can_create_a_profile_with_image()
    {
        $admin = $this->createAdminUser();
        // 1️⃣ Fake the public disk (prevents real file creation)
        Storage::fake('public');

        // 2️⃣ Create a fake image file (1MB JPEG)
        $file = UploadedFile::fake()->image('profile.jpg', 500, 500);

        // 3️⃣ Send the request
        $response = $this->actingAs($admin, 'sanctum')->postJson('api/profiles', [
            'firstName' => 'Rémy',
            'lastName'  => 'NORBERT',
            'status'    => 'active',
            'image'     => $file, // Attach fake file
        ]);

        // 4️⃣ Assert the request was successful
        $response->assertStatus(200);

        // 5️⃣ Verify that Laravel "saved" the file (but didn't actually write it)
        Storage::disk('public')->assertExists('profiles/' . $file->hashName());

        // 6️⃣ Check if the profile exists in the database
        $this->assertDatabaseHas('profiles', [
            'first_name' => 'Rémy',
            'last_name'  => 'NORBERT',
            'status'     => 'active',
            'image'      => 'profiles/' . $file->hashName(), // Path should be stored, not the actual file
        ]);
    }

    /** @test */
    public function it_can_update_a_profile()
    {
        Storage::fake('public');

        $admin = $this->createAdminUser();
        $profile = Profile::factory()->create();

        $newImage = UploadedFile::fake()->image('new_profile.jpg');
        $updateData = [
            '_method' => 'PUT',
            'firstName' => 'Updated Rémy',
            'lastName' => 'Updated NORBERT',
            'status' => 'inactive',
            'image' => $newImage,
        ];

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson("api/profiles/{$profile->id}?_method=PUT", $updateData);

        $response->assertStatus(200);

        Storage::disk('public')->assertExists('profiles/' . $newImage->hashName());
    }

    /** @test */
    public function it_can_delete_a_profile()
    {
        $admin = $this->createAdminUser();
        $profile = Profile::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("api/profiles/{$profile->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('profiles', ['id' => $profile->id]);
    }

    private function createAdminUser()
    {
        return User::factory()->create();
    }
}
