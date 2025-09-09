<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Blog\Post;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_create_a_post()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $postData = [
            'body' => 'This is a test post.',
            'status' => 'active',
        ];

        $response = $this->postJson('/api/posts', $postData);

        $response->assertStatus(201)
            ->assertJsonFragment(['body' => 'This is a test post.']);
    }

    public function test_a_user_can_update_their_own_post()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $post = Post::factory()->create(['user_id' => $user->id]);

        $updateData = ['body' => 'Updated body.'];

        $response = $this->putJson("/api/posts/{$post->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['body' => 'Updated body.']);
    }

    public function test_a_user_cannot_update_another_users_post()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user1, 'sanctum');

        $post = Post::factory()->create(['user_id' => $user2->id]);

        $updateData = ['body' => 'Updated body.'];

        $response = $this->putJson("/api/posts/{$post->id}", $updateData);

        $response->assertStatus(403);
    }
}
