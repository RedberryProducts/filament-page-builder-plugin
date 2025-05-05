<?php

namespace Redberry\PageBuilderPlugin\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Redberry\PageBuilderPlugin\PageBuilderPluginServiceProvider;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Redberry\\PageBuilderPlugin\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        $this->beforeApplicationDestroyed(function () {
            File::cleanDirectory(app_path());
            File::cleanDirectory(resource_path('views'));
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            ActionsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            PageBuilderPluginServiceProvider::class,
            AdminPanelProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('view.paths', [
            ...config('view.paths'),
            __DIR__ . '/resources/views',
        ]);
        config()->set('app.key', 'base64:TqTuAGK5LPb3IS6meAR6adhPMY4DLdgvm5geIQnDrZU=');


    }

    protected function defineDatabaseMigrations()
    {

        File::copy(
            __DIR__ . '/../database/migrations/create_page_builder_blocks_table.php.stub',
            database_path('migrations/create_page_builder_blocks_table.php')
        );

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations', database_path('migrations'));

        $this->beforeApplicationDestroyed(function () {
            File::delete(
                database_path('migrations/create_page_builder_blocks_table.php')
            );
        });

        // $migration = include __DIR__ . '/../database/migrations/create_page_builder_blocks_table.php.stub';
        // $migration->up();
        // copy migration stubs to the database/migrations folder
    }
}
