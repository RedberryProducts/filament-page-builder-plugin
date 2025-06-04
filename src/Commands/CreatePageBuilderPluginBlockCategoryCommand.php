<?php

namespace Redberry\PageBuilderPlugin\Commands;

use Illuminate\Console\Command;
use Redberry\PageBuilderPlugin\Traits\Commands\CreatesClassFile;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class CreatePageBuilderPluginBlockCategoryCommand extends Command
{
    use CreatesClassFile;

    public $signature = 'page-builder-plugin:make-block-category {name?} {--panel=}';

    public $description = 'create a new block category';

    public function handle(): int
    {
        $categoryName = (string) str(
            $this->argument('name') ??
            text(
                label: 'What is the category name?',
                placeholder: 'Buttons',
                required: true,
            ),
        )
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        $this->panel = $this->getPanelToCreateIn();

        $namespace = $this->getClassNameSpaces('BlockCategories');

        $categoryClass = $namespace.'\\'.$categoryName;

        try {
            if (class_exists($categoryClass)) {
                if (! confirm(
                    label: 'This block already exists. Do you want to overwrite it?',
                    default: false,
                )) {
                    return self::FAILURE;
                }
            }
        } catch (\Throwable $th) {
        }

        $this->createFileFromStub(
            'category-block',
            $this->appClassToPath($categoryClass),
            [
                '{{ class }}' => str($categoryName)->afterLast('\\')->replace('\\', ''),
                '{{ namespace }}' => str($categoryClass)->beforeLast('\\'),
            ]
        );

        return COMMAND::SUCCESS;
    }
}
