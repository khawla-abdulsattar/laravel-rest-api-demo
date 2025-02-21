<?php

use Spatie\Permission\Models\Role;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'user']);

        $this->admin = User::factory()->create();
        $this->user1 = User::factory()->create();
        $this->user1->assignRole('user');
        $this->user2 = User::factory()->create();
        $this->user2->assignRole('user');
    }

    #[Test]
    public function user_can_create_a_post()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/posts', [
            'title'   => 'New Post',
            'content' => 'This is a test post.'
        ]);

        $response->assertStatus(201);
    }



    #[Test]
    public function user_can_update_own_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user1->id]);

        $response = $this->actingAs($this->user1)->putJson("api/v1/posts/edit_posts/{$post->id}", [
            'title' => 'Updated Post Title',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Updated Post Title']);
    }

    #[Test]
    public function user_cannot_update_others_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user1->id]);

        $response = $this->actingAs($this->user2)->putJson("api/v1/posts/edit_posts/{$post->id}", [
            'title' => 'Hacked Post Title',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function user_can_delete_own_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user1->id]);

        $response = $this->actingAs($this->user1)->deleteJson("api/v1/posts/delete_post/{$post->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    #[Test]
    public function user_cannot_delete_others_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user1->id]);

        $response = $this->actingAs($this->user2)->deleteJson("api/v1/posts/delete_post/{$post->id}");

        $response->assertStatus(403);
    }
}
