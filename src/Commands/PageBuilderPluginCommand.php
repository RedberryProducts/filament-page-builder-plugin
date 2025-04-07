<?php

namespace Redberry\PageBuilderPlugin\Commands;

use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class PageBuilderPluginCommand extends Command
{
    public $signature = 'page-builder-plugin:make-block {name?} {--T|type=} {--panel=}';

    public $description = 'create a new block';

    public function handle(): int
    {
        $block = (string) str(
            $this->argument('name') ??
            text(
                label: 'What is the block name?',
                placeholder: 'Header',
                required: true,
            ),
        )
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        $panel = $this->option('panel');

        if ($panel) {
            $panel = Filament::getPanel($panel, isStrict: false);
        }

        if (! $panel) {
            $panels = Filament::getPanels();

            /** @var Panel $panel */
            $panel = (count($panels) > 1) ? $panels[select(
                label: 'Which panel would you like to create this in?',
                options: array_map(
                    fn (Panel $panel): string => $panel->getId(),
                    $panels,
                ),
                default: Filament::getDefaultPanel()->getId()
            )] : Arr::first($panels);
        }

        $blocksNamespace = array_map(
            fn (string $namespace): string => str_replace('Resources', 'Blocks', $namespace),
            $panel->getResourceNamespaces(),
        );

        $blocksNamespace = (count($blocksNamespace) > 1) ?
            select(
                label: 'Which namespace would you like to create this in?',
                options: $blocksNamespace
            ) : (Arr::first($blocksNamespace));

        $blockClass = $blocksNamespace . '\\' . $block;

        try {
            if (class_exists($blockClass)) {
                if (! confirm(
                    label: 'This block already exists. Do you want to overwrite it?',
                    default: false,
                )) {
                    return self::FAILURE;
                }
            }
        } catch (\Throwable $th) {
        }

        $blockType = $this->option('type') ?? select(
            label: 'What type of block is this?',
            options: ['iframe', 'view'],
            default: 'iframe',
        );

        if ($blockType === 'iframe') {
            $this->createFileFromStub(
                'block',
                $this->appClassToPath($blockClass),
                [
                    '{{ class }}' => str($blockClass)->afterLast('\\')->replace('\\', ''),
                    '{{ namespace }}' => str($blockClass)->beforeLast('\\'),
                ]
            );
        }

        if ($blockType === 'view') {
            $viewName = str($block)->replace('\\', '.')->kebab()->replace('.-', '.');
            $this->createFileFromStub(
                'block.view',
                $this->appClassToPath($blockClass),
                [
                    '{{ class }}' => str($block)->afterLast('\\')->replace('\\', ''),
                    '{{ namespace }}' => str($blockClass)->beforeLast('\\'),
                    '{{ viewName }}' => $viewName,
                ]
            );

            $this->createFileFromStub(
                'block.blade',
                resource_path(
                    $viewName
                        ->replace('.', '/')
                        ->prepend('views/blocks/')
                        ->append('.blade.php')
                ),
            );
        }

        return self::SUCCESS;
    }

    public function createFileFromStub(string $stub, string $path, array $replacements = []): void
    {
        /** @var Filesystem */
        $filesystem = app(Filesystem::class);

        if (! file_exists($stubPath = base_path("stubs/page-builder-plugin/{$stub}.stub"))) {
            $stubPath = __DIR__ . "/../../stubs/{$stub}.stub";
        }

        $contents = strtr(file_get_contents($stubPath), $replacements);

        $filesystem->ensureDirectoryExists(
            pathinfo($path, PATHINFO_DIRNAME)
        );

        $filesystem->put($path, $contents);
    }

    private function appClassToPath(string $class): string
    {
        $appNamespace = app()->getNamespace();
        $relativePath = Str::replaceFirst($appNamespace, '', $class);

        return app_path(
            str_replace('\\', '/', $relativePath) . '.php'
        );
    }
}
