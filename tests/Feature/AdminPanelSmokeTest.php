<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\ShieldSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AdminPanelSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guests_are_redirected_to_login_for_core_admin_routes(): void
    {
        foreach (['/', '/users', '/players', '/teams', '/applicants'] as $route) {
            $response = $this->get($route);
            $response->assertStatus(302);
            $this->assertStringContainsString('/login', (string) $response->headers->get('Location'));
        }
    }

    public function test_seeded_admin_credentials_can_authenticate(): void
    {
        $this->seed(ShieldSeeder::class);

        $this->assertTrue(Auth::attempt([
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]));
    }

    public function test_authenticated_admin_sees_dashboard_navigation_links(): void
    {
        $admin = $this->seedAndGetAdmin();

        $this->withoutMiddleware();
        Gate::before(static fn () => true);
        $this->actingAs($admin);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('/users', false);
        $response->assertSee('/players', false);
        $response->assertSee('/teams', false);
        $response->assertSee('/applicants', false);
    }

    public function test_authenticated_admin_can_open_core_index_and_record_pages(): void
    {
        $admin = $this->seedAndGetAdmin();
        $team = Team::factory()->create(['name' => 'Test Team']);
        $player = Player::factory()->create([
            'name' => 'Test Player',
            'team_id' => $team->id,
        ]);
        $applicant = Applicant::factory()->create(['name' => 'Test Applicant']);
        $managedUser = User::factory()->create(['name' => 'Test User']);

        $this->withoutMiddleware();
        Gate::before(static fn () => true);
        $this->actingAs($admin);

        $this->get('/users')->assertOk();
        $this->get('/players')->assertOk();
        $this->get('/teams')->assertOk();
        $this->get('/applicants')->assertOk();

        $this->get(route('filament.admin.resources.users.edit', $managedUser))->assertOk();
        $this->get(route('filament.admin.resources.players.edit', $player))->assertOk();
        $this->get(route('filament.admin.resources.teams.view', $team))->assertOk();
        $this->get(route('filament.admin.resources.applicants.edit', $applicant))->assertOk();
    }

    private function seedAndGetAdmin(): User
    {
        $this->seed(ShieldSeeder::class);

        return User::query()
            ->where('email', 'admin@example.com')
            ->firstOrFail();
    }
}
