<?php

namespace App\Providers;

use App\Models\Contact;
use App\Observers\ContactObserver;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Disable mass assignment protection for all models
        Model::unguard();

        // Set the default pagination page size for all tables to 50
        Table::configureUsing(function (Table $table): void {
            $table->defaultPaginationPageOption(50);
        });

        // Observe the Contact model for changes
        // This is used to prevent the deletion of a contact if it is associated with other players
        Contact::observe(ContactObserver::class);
    }
}
