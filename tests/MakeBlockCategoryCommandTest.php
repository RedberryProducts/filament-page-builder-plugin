<?php

use Filament\Panel;
use Filament\PanelRegistry;
use Redberry\PageBuilderPlugin\Tests\SecondAdminPanelProvider;

use function Pest\Laravel\artisan;

describe('one admin panel', function () {
    it('can create block category ', function () {
        expect(file_exists(app_path('Filament/Admin/BlockCategories/Buttons.php')))->toBeFalse();

        artisan('page-builder-plugin:make-block-category')
            ->expectsQuestion('What is the category name?', 'Buttons');

        expect(file_exists(app_path('Filament/Admin/BlockCategories/Buttons.php')))->toBeTrue();
    });

    it('will accept category name as argument', function () {
        expect(file_exists(app_path('Filament/Admin/BlockCategories/Buttons.php')))->toBeFalse();

        artisan('page-builder-plugin:make-block-category Buttons');

        expect(file_exists(app_path('Filament/Admin/BlockCategories/Buttons.php')))->toBeTrue();
    });
});

describe('multiple admin panels', function () {
    beforeEach(function () {
        app(PanelRegistry::class)->register(
            (new SecondAdminPanelProvider(app()))->panel(Panel::make())
        );
    });

    it('will ask for admin panel id', function () {
        expect(file_exists(app_path('Filament/SecondAdmin/BlockCategories/Buttons.php')))->toBeFalse();

        artisan('page-builder-plugin:make-block-category Buttons')
            ->expectsQuestion('Which panel would you like to create this in?', 'second-admin');

        expect(file_exists(app_path('Filament/SecondAdmin/BlockCategories/Buttons.php')))->toBeTrue();
    });

    it('will accept admin panel id as option', function () {
        expect(file_exists(app_path('Filament/SecondAdmin/BlockCategories/Buttons.php')))->toBeFalse();

        artisan('page-builder-plugin:make-block-category Buttons --panel=second-admin');

        expect(file_exists(app_path('Filament/SecondAdmin/BlockCategories/Buttons.php')))->toBeTrue();
    });
});
