<?php

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelRegistry;
use Redberry\PageBuilderPlugin\Tests\SecondAdminPanelProvider;

use function Pest\Laravel\artisan;

it('can create block with type of view', function () {
    expect(file_exists(app_path('Filament/Admin/Blocks/Header.php')))->toBeFalse();

    artisan('page-builder-plugin:make-block')
        ->expectsQuestion('What is the block name?', 'Header')
        ->expectsQuestion('What type of block is this?', 'view');

    expect(file_exists(app_path('Filament/Admin/Blocks/Header.php')))->toBeTrue();
    expect(file_exists(resource_path('views/admin/blocks/header.blade.php')))->toBeTrue();
});

it('can create block with type of iframe', function () {
    expect(file_exists(app_path('Filament/Admin/Blocks/Header.php')))->toBeFalse();

    artisan('page-builder-plugin:make-block')
        ->expectsQuestion('What is the block name?', 'Header')
        ->expectsQuestion('What type of block is this?', 'iframe');

    expect(file_exists(app_path('Filament/Admin/Blocks/Header.php')))->toBeTrue();
    expect(file_exists(resource_path('views/admin/blocks/header.blade.php')))->toBeFalse();
});

it('can choose block type using --type option', function () {
    expect(file_exists(app_path('Filament/Admin/Blocks/Header.php')))->toBeFalse();

    artisan('page-builder-plugin:make-block Header --type=iframe');

    expect(file_exists(app_path('Filament/Admin/Blocks/Header.php')))->toBeTrue();
    expect(file_exists(resource_path('views/admin/blocks/header.blade.php')))->toBeFalse();
});

it('will not ask for block name if provided', function () {
    expect(file_exists(app_path('Filament/Admin/Blocks/Header.php')))->toBeFalse();

    artisan('page-builder-plugin:make-block Header')
        ->expectsQuestion('What type of block is this?', 'iframe');

    expect(file_exists(app_path('Filament/Admin/Blocks/Header.php')))->toBeTrue();
    expect(file_exists(resource_path('views/admin/blocks/header.blade.php')))->toBeFalse();
});

describe('multiple admin panels', function () {

    beforeEach(function () {
        app(PanelRegistry::class)->register(
            (new SecondAdminPanelProvider(app()))->panel(Panel::make())
        );
    });

    it('will ask for admin panel id', function () {
        expect(file_exists(app_path('Filament/SecondAdmin/Blocks/Header.php')))->toBeFalse();

        artisan('page-builder-plugin:make-block Header --type=iframe')
            ->expectsQuestion('Which panel would you like to create this in?', 'second-admin');

        expect(file_exists(app_path('Filament/SecondAdmin/Blocks/Header.php')))->toBeTrue();
        expect(file_exists(resource_path('views/second-admin/blocks/header.blade.php')))->toBeFalse();
    });

    it('will accept admin panel id from arguments', function () {
        expect(file_exists(app_path('Filament/SecondAdmin/Blocks/Header.php')))->toBeFalse();

        artisan('page-builder-plugin:make-block Header --type=iframe --panel=second-admin');

        expect(file_exists(app_path('Filament/SecondAdmin/Blocks/Header.php')))->toBeTrue();
        expect(file_exists(resource_path('views/second-admin/blocks/header.blade.php')))->toBeFalse();
    });
});