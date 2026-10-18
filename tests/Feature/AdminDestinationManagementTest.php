<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminDestinationManagementTest extends TestCase
{
    use RefreshDatabase;

    private function category(): Category
    {
        return Category::create([
            'name' => 'Nature',
            'slug' => 'nature',
        ]);
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_guest_cannot_access_destination_admin(): void
    {
        $this->get('/admin/destinations')->assertRedirect('/login');
    }

    public function test_non_admin_user_cannot_access_destination_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/destinations')
            ->assertForbidden();
    }

    public function test_admin_can_view_destination_list_create_and_edit_pages(): void
    {
        $category = $this->category();
        $admin = $this->admin();
        $destination = Destination::create([
            'category_id' => $category->id,
            'name' => 'Visible Destination',
            'slug' => 'visible-destination',
            'location' => 'Java, Indonesia',
            'region' => 'Java',
            'description' => 'Visible in admin list.',
            'image' => 'destinations/visible.jpg',
            'sustainability_score' => 80,
        ]);

        $this->actingAs($admin)->get('/admin/destinations')
            ->assertOk()
            ->assertSee('Visible Destination')
            ->assertSee('New Destination');

        $this->actingAs($admin)->get('/admin/destinations/create')
            ->assertOk()
            ->assertSee('New Destination');

        $this->actingAs($admin)->get("/admin/destinations/{$destination->id}/edit")
            ->assertOk()
            ->assertSee('Edit:');
    }

    public function test_admin_can_create_destination_with_jpg_and_png(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post('/admin/destinations', [
            'category_id' => $category->id,
            'name' => 'New Eco Site',
            'slug' => 'new-eco-site',
            'location' => 'Java, Indonesia',
            'region' => 'Java',
            'description' => 'A sustainable destination.',
            'image' => UploadedFile::fake()->image('destination.jpg'),
            'latitude' => -7.5,
            'longitude' => 110.4,
            'sustainability_score' => 90,
        ]);

        $response->assertRedirect('/admin/destinations');
        $this->assertDatabaseHas('destinations', [
            'slug' => 'new-eco-site',
            'category_id' => $category->id,
            'sustainability_score' => 90,
        ]);
        $this->assertNotNull(Destination::where('slug', 'new-eco-site')->first()->image);

        $this->actingAs($admin)->post('/admin/destinations', [
            'category_id' => $category->id,
            'name' => 'Second Eco Site',
            'slug' => 'second-eco-site',
            'location' => 'Sumatra, Indonesia',
            'region' => 'Sumatra',
            'description' => 'A second sustainable destination.',
            'image' => UploadedFile::fake()->image('destination.png'),
            'sustainability_score' => 85,
        ])->assertRedirect('/admin/destinations');

        $this->assertDatabaseHas('destinations', ['slug' => 'second-eco-site']);
    }

    public function test_admin_can_update_destination_and_replace_or_remove_image(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $admin = $this->admin();
        $destination = Destination::create([
            'category_id' => $category->id,
            'name' => 'Old Name',
            'slug' => 'old-name',
            'location' => 'Java, Indonesia',
            'region' => 'Java',
            'description' => 'Old description.',
            'image' => 'destinations/old-image.jpg',
            'sustainability_score' => 75,
        ]);

        Storage::disk('public')->put('destinations/old-image.jpg', 'old-image-content');

        $response = $this->actingAs($admin)->put("/admin/destinations/{$destination->id}", [
            'category_id' => $category->id,
            'name' => 'Updated Name',
            'slug' => 'updated-name',
            'location' => 'Bali, Indonesia',
            'region' => 'Bali & Nusa Tenggara',
            'description' => 'Updated description.',
            'image' => UploadedFile::fake()->image('replacement.jpg'),
            'sustainability_score' => 88,
        ]);

        $response->assertRedirect('/admin/destinations');

        $destination->refresh();
        $this->assertSame('updated-name', $destination->slug);
        $this->assertNotSame('destinations/old-image.jpg', $destination->image);
        $this->assertTrue(Storage::disk('public')->exists($destination->image));

        $updatedImage = $destination->image;
        $this->actingAs($admin)->put("/admin/destinations/{$destination->id}", [
            'category_id' => $category->id,
            'name' => 'Updated Name',
            'slug' => 'updated-name',
            'location' => 'Bali, Indonesia',
            'region' => 'Bali & Nusa Tenggara',
            'description' => 'Updated description.',
            'remove_image' => 1,
            'sustainability_score' => 88,
        ])->assertRedirect('/admin/destinations');

        $destination->refresh();
        $this->assertNull($destination->image);
        $this->assertFalse(Storage::disk('public')->exists($updatedImage));
    }

    public function test_admin_can_delete_destination_and_image(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $admin = $this->admin();
        $destination = Destination::create([
            'category_id' => $category->id,
            'name' => 'To Delete',
            'slug' => 'to-delete',
            'location' => 'Java, Indonesia',
            'region' => 'Java',
            'description' => 'Temporary destination.',
            'image' => 'destinations/delete-image.jpg',
            'sustainability_score' => 70,
        ]);

        Storage::disk('public')->put('destinations/delete-image.jpg', 'delete-image-content');

        $this->actingAs($admin)
            ->delete("/admin/destinations/{$destination->id}")
            ->assertRedirect('/admin/destinations');

        $this->assertDatabaseMissing('destinations', ['id' => $destination->id]);
        $this->assertFalse(Storage::disk('public')->exists('destinations/delete-image.jpg'));
    }

    public function test_admin_destination_form_rejects_non_image_files(): void
    {
        $category = $this->category();
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post('/admin/destinations', [
            'category_id' => $category->id,
            'name' => 'Bad Image',
            'slug' => 'bad-image',
            'location' => 'Java, Indonesia',
            'description' => 'Destination with invalid file.',
            'image' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            'sustainability_score' => 60,
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('destinations', ['slug' => 'bad-image']);
    }

    public function test_admin_destination_slug_is_slugified_and_collisions_are_rejected(): void
    {
        $category = $this->category();
        $admin = $this->admin();

        Destination::create([
            'category_id' => $category->id,
            'name' => 'Existing Place',
            'slug' => 'existing-place',
            'location' => 'Java, Indonesia',
            'description' => 'Existing destination.',
            'sustainability_score' => 70,
        ]);

        // A raw slug with spaces/symbols must be slugified before the unique check.
        $this->actingAs($admin)->post('/admin/destinations', [
            'category_id' => $category->id,
            'name' => 'My New Place',
            'slug' => 'My New Place!',
            'location' => 'Bali, Indonesia',
            'description' => 'A valid destination.',
            'sustainability_score' => 60,
        ])->assertRedirect('/admin/destinations');

        $this->assertDatabaseHas('destinations', ['slug' => 'my-new-place']);

        // A name that slugifies to an existing slug must fail validation, not 500.
        $this->actingAs($admin)->post('/admin/destinations', [
            'category_id' => $category->id,
            'name' => 'existing place',
            'location' => 'Java, Indonesia',
            'description' => 'Duplicate slug.',
            'sustainability_score' => 55,
        ])->assertSessionHasErrors('slug');

        $this->assertDatabaseCount('destinations', 2);
    }
}
