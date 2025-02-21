<?php
namespace Tests\Feature;

use Spatie\Permission\Models\Role;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }
    public function only_admin_can_access_users_list()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->getJson('/api/v1/users');
        $response->assertStatus(200);
    }

    /** @test */
    public function user_cannot_access_users_list()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->getJson('/api/v1/users');
        $response->assertStatus(403);
    }
}
