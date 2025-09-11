<?php

use Redberry\PageBuilderPlugin\Models\GlobalBlockConfig;
use Redberry\PageBuilderPlugin\Tests\Fixtures\Blocks\GlobalViewBlock;

beforeEach(function () {
    copy(
        __DIR__ . '/../../database/migrations/create_global_block_configs_table.php.stub',
        database_path('migrations/create_global_block_configs_table.php')
    );

    $this->artisan('migrate', ['--database' => 'testing'])->run();
});

afterEach(function () {
    unlink(database_path('migrations/create_global_block_configs_table.php'));
});

it('can create a global block config', function () {
    $config = GlobalBlockConfig::create([
        'name' => 'Test Block',
        'class_name' => GlobalViewBlock::class,
        'configuration' => [
            'title' => 'Test Title',
            'content' => 'Test Content',
        ],
    ]);

    expect($config)->toBeInstanceOf(GlobalBlockConfig::class)
        ->and($config->name)->toBe('Test Block')
        ->and($config->class_name)->toBe(GlobalViewBlock::class)
        ->and($config->configuration)->toBe([
            'title' => 'Test Title',
            'content' => 'Test Content',
        ]);
});

it('can get config value', function () {
    $config = GlobalBlockConfig::create([
        'name' => 'Test Block',
        'class_name' => GlobalViewBlock::class,
        'configuration' => [
            'title' => 'Test Title',
            'content' => 'Test Content',
        ],
    ]);

    expect($config->getConfigValue('title'))->toBe('Test Title')
        ->and($config->getConfigValue('content'))->toBe('Test Content')
        ->and($config->getConfigValue('non_existent'))->toBeNull();
});

it('handles missing configuration gracefully', function () {
    $config = GlobalBlockConfig::create([
        'name' => 'Test Block',
        'class_name' => GlobalViewBlock::class,
        'configuration' => null,
    ]);

    expect($config->getConfigValue('title'))->toBeNull();
});

it('returns empty array when no discovery paths configured', function () {
    config(['page-builder-plugin.global_blocks_discovery_paths' => []]);

    $method = new ReflectionMethod(GlobalBlockConfig::class, 'discoverGlobalBlocks');
    $method->setAccessible(true);

    $blocks = $method->invoke(new GlobalBlockConfig);

    expect($blocks)->toBe([]);
});

it('processes glob patterns correctly', function () {
    config(['page-builder-plugin.global_blocks_discovery_paths' => ['tests/non-existent/*/path']]);

    $method = new ReflectionMethod(GlobalBlockConfig::class, 'discoverGlobalBlocks');
    $method->setAccessible(true);

    $blocks = $method->invoke(new GlobalBlockConfig);

    expect($blocks)->toBe([]);
});

it('processes direct paths correctly', function () {
    config(['page-builder-plugin.global_blocks_discovery_paths' => ['tests/non-existent/direct/path']]);

    $method = new ReflectionMethod(GlobalBlockConfig::class, 'discoverGlobalBlocks');
    $method->setAccessible(true);

    $blocks = $method->invoke(new GlobalBlockConfig);

    expect($blocks)->toBe([]);
});

it('handles mixed path types in configuration', function () {
    config(['page-builder-plugin.global_blocks_discovery_paths' => [
        'non/existent/direct/path',
        'non/existent/*/glob/path',
    ]]);

    $method = new ReflectionMethod(GlobalBlockConfig::class, 'discoverGlobalBlocks');
    $method->setAccessible(true);

    $blocks = $method->invoke(new GlobalBlockConfig);

    expect($blocks)->toBe([]);
});

it('correctly identifies glob patterns by checking for asterisk', function () {
    config(['page-builder-plugin.global_blocks_discovery_paths' => ['test/path/without/asterisk']]);

    $method = new ReflectionMethod(GlobalBlockConfig::class, 'discoverGlobalBlocks');
    $method->setAccessible(true);

    $blocks = $method->invoke(new GlobalBlockConfig);

    expect($blocks)->toBe([]);
});
