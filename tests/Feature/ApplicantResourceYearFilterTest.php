<?php

namespace Tests\Feature;

use App\Filament\Resources\ApplicantResource\Pages\ListApplicants;
use App\Models\Applicant;
use App\Models\User;
use Database\Seeders\ShieldSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class ApplicantResourceYearFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_applicants_default_to_current_application_year_and_can_be_filtered(): void
    {
        $admin = $this->seedAndGetAdmin();
        $currentYear = now()->year;
        $previousYear = $currentYear - 1;

        $currentYearApplicant = Applicant::factory()->create([
            'name' => 'Current Year Applicant',
            'application_date' => now()->setYear($currentYear)->toDateString(),
        ]);
        $secondCurrentYearApplicant = Applicant::factory()->create([
            'name' => 'Second Current Year Applicant',
            'application_date' => now()->setYear($currentYear)->subMonth()->toDateString(),
        ]);
        $previousYearApplicant = Applicant::factory()->create([
            'name' => 'Previous Year Applicant',
            'application_date' => now()->setYear($previousYear)->toDateString(),
        ]);

        $this->withoutMiddleware();
        Gate::before(static fn () => true);
        $this->actingAs($admin);

        Livewire::test(ListApplicants::class)
            ->assertTableFilterExists('application_year')
            ->assertCanSeeTableRecords([$currentYearApplicant, $secondCurrentYearApplicant])
            ->assertCanNotSeeTableRecords([$previousYearApplicant])
            ->filterTable('application_year', (string) $previousYear)
            ->assertCanSeeTableRecords([$previousYearApplicant])
            ->assertCanNotSeeTableRecords([$currentYearApplicant, $secondCurrentYearApplicant])
            ->filterTable('application_year', '')
            ->assertCanSeeTableRecords([$currentYearApplicant, $secondCurrentYearApplicant, $previousYearApplicant]);
    }

    private function seedAndGetAdmin(): User
    {
        $this->seed(ShieldSeeder::class);

        return User::query()
            ->where('email', 'admin@example.com')
            ->firstOrFail();
    }
}
