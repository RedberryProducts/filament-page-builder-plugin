<?php

use Filament\Panel;
use Redberry\PageBuilderPlugin\GlobalBlocksPlugin;
use Redberry\PageBuilderPlugin\Resources\GlobalBlockConfigResource;

it('can be instantiated', function () {
    $plugin = GlobalBlocksPlugin::make();

    expect($plugin)->toBeInstanceOf(GlobalBlocksPlugin::class);
});

it('has correct plugin ID', function () {
    $plugin = GlobalBlocksPlugin::make();

    expect($plugin->getId())->toBe('page-builder-global-blocks');
});

it('can configure enable global blocks', function () {
    $plugin = GlobalBlocksPlugin::make()
        ->enableGlobalBlocks(false);

    expect($plugin)->toBeInstanceOf(GlobalBlocksPlugin::class);
});

it('can configure custom resource', function () {
    $customResourceClass = 'App\\Filament\\Resources\\CustomGlobalBlocksResource';

    $plugin = GlobalBlocksPlugin::make()
        ->resource($customResourceClass);

    expect($plugin)->toBeInstanceOf(GlobalBlocksPlugin::class);
});

it('registers resource when enabled', function () {
    $panel = Panel::make();

    $plugin = GlobalBlocksPlugin::make()
        ->enableGlobalBlocks(true);

    $plugin->register($panel);

    expect($panel->getResources())->toContain(GlobalBlockConfigResource::class);
});

it('does not register resource when disabled', function () {
    $panel = Panel::make();

    $plugin = GlobalBlocksPlugin::make()
        ->enableGlobalBlocks(false);

    $plugin->register($panel);

    expect($panel->getResources())->not->toContain(GlobalBlockConfigResource::class);
});

it('registers custom resource when specified', function () {
    $customResourceClass = GlobalBlockConfigResource::class; // Using existing class for test
    $panel = Panel::make();

    $plugin = GlobalBlocksPlugin::make()
        ->resource($customResourceClass);

    $plugin->register($panel);

    expect($panel->getResources())->toContain($customResourceClass);
});

it('handles non-existent resource class gracefully', function () {
    $panel = Panel::make();

    $plugin = GlobalBlocksPlugin::make()
        ->resource('NonExistentResourceClass');

    $plugin->register($panel);

    expect($panel->getResources())->toBeEmpty();
});

it('can configure navigation group', function () {
    $plugin = GlobalBlocksPlugin::make()
        ->navigationGroup('Custom Group');

    expect($plugin)->toBeInstanceOf(GlobalBlocksPlugin::class);
});

it('can configure navigation sort', function () {
    $plugin = GlobalBlocksPlugin::make()
        ->navigationSort(15);

    expect($plugin)->toBeInstanceOf(GlobalBlocksPlugin::class);
});

it('can configure navigation icon', function () {
    $plugin = GlobalBlocksPlugin::make()
        ->navigationIcon('heroicon-o-document-text');

    expect($plugin)->toBeInstanceOf(GlobalBlocksPlugin::class);
});

it('can remove navigation icon with empty string', function () {
    $plugin = GlobalBlocksPlugin::make()
        ->navigationIcon('');

    expect($plugin)->toBeInstanceOf(GlobalBlocksPlugin::class);
});

it('applies navigation customizations to resource', function () {
    $panel = Panel::make();

    $plugin = GlobalBlocksPlugin::make()
        ->navigationGroup('Custom Group')
        ->navigationSort(25)
        ->navigationIcon('heroicon-o-cog');

    $plugin->register($panel);

    expect(GlobalBlockConfigResource::getNavigationGroup())->toBe('Custom Group');
    expect(GlobalBlockConfigResource::getNavigationSort())->toBe(25);
    expect(GlobalBlockConfigResource::getNavigationIcon())->toBe('heroicon-o-cog');
});

it('applies empty icon to resource', function () {
    $panel = Panel::make();

    $plugin = GlobalBlocksPlugin::make()
        ->navigationIcon('');

    $plugin->register($panel);

    expect(GlobalBlockConfigResource::getNavigationIcon())->toBe('');
});
