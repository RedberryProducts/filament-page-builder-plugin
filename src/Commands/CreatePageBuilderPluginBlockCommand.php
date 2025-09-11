<?php

namespace Redberry\PageBuilderPlugin\Commands;

use Filament\Panel;
use Illuminate\Console\Command;
use Redberry\PageBuilderPlugin\Traits\Commands\CreatesClassFile;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class CreatePageBuilderPluginBlockCommand extends Command
{
    use CreatesClassFile;

    public $signature = 'page-builder-plugin:make-block {name?} {--T|type=} {--panel=} {--global : Create a global block in Blocks/Globals directory}';

    public $description = 'create a new block';

    public ?Panel $panel = null;

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

        $this->panel = $this->getPanelToCreateIn();

        $isGlobal = $this->option('global');

        $blocksNamespace = $this->getClassNameSpaces('Blocks');

        if ($isGlobal) {
            $this->createGlobalsCategoryIfNotExists();
            $isFirstGlobalBlock = $this->isFirstGlobalBlock();
            $blocksNamespace .= '\\Globals';
        }

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
            $stubName = $isGlobal ? 'block.global' : 'block';
            $replacements = [
                '{{ class }}' => str($blockClass)->afterLast('\\')->replace('\\', ''),
                '{{ namespace }}' => str($blockClass)->beforeLast('\\'),
            ];

            if ($isGlobal) {
                $replacements['{{ globalsNamespace }}'] = $this->getClassNameSpaces('BlockCategories');
            }

            $this->createFileFromStub(
                $stubName,
                $this->appClassToPath($blockClass),
                $replacements
            );
        }

        if ($blockType === 'view') {
            $viewName = str($block)->replace('\\', '.')->kebab()->replace('.-', '.');
            if ($isGlobal) {
                $viewName = 'globals.' . $viewName;
            }

            $stubName = $isGlobal ? 'block.global.view' : 'block.view';
            $replacements = [
                '{{ class }}' => str($block)->afterLast('\\')->replace('\\', ''),
                '{{ namespace }}' => str($blockClass)->beforeLast('\\'),
                '{{ viewName }}' => $viewName,
                '{{ panelId }}' => $this->panel->getId(),
            ];

            if ($isGlobal) {
                $replacements['{{ globalsNamespace }}'] = $this->getClassNameSpaces('BlockCategories');
            }

            $this->createFileFromStub(
                $stubName,
                $this->appClassToPath($blockClass),
                $replacements
            );

            $viewPath = str($viewName)
                ->replace('.', '/')
                ->prepend("views/{$this->panel->getId()}/blocks/")
                ->append('.blade.php');

            $this->createFileFromStub(
                'block.blade',
                resource_path($viewPath),
            );
        }

        if ($isGlobal && $isFirstGlobalBlock) {
            $this->info('To manage global blocks in Filament, add the GlobalBlocksPlugin to your panel:');
        }

        return self::SUCCESS;
    }

    protected function createGlobalsCategoryIfNotExists(): void
    {
        $categoryNamespace = $this->getClassNameSpaces('BlockCategories');
        $globalsClass = $categoryNamespace . '\\Globals';

        if (class_exists($globalsClass)) {
            return;
        }

        $this->createFileFromStub(
            'category-block',
            $this->appClassToPath($globalsClass),
            [
                '{{ class }}' => 'Globals',
                '{{ namespace }}' => $categoryNamespace,
            ]
        );

        $this->info("Created Globals category at: {$globalsClass}");
    }

    protected function isFirstGlobalBlock(): bool
    {
        $globalBlocksPath = app_path("Filament/{$this->panel->getId()}/Blocks/Globals");

        if (! is_dir($globalBlocksPath)) {
            return true;
        }

        $existingBlocks = glob($globalBlocksPath . '/*.php');

        return empty($existingBlocks);
    }
}
