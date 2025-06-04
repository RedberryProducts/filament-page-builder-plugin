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

    public $signature = 'page-builder-plugin:make-block {name?} {--T|type=} {--panel=}';

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

        $blocksNamespace = $this->getClassNameSpaces('Blocks');

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
                    '{{ panelId }}' => $this->panel->getId(),
                ]
            );

            $this->createFileFromStub(
                'block.blade',
                resource_path(
                    $viewName
                        ->replace('.', '/')
                        ->prepend("views/{$this->panel->getId()}/blocks/")
                        ->append('.blade.php')
                ),
            );
        }

        return self::SUCCESS;
    }
}
