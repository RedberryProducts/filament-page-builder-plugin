<?php

namespace Redberry\PageBuilderPlugin\Traits\Commands;

use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use function Laravel\Prompts\select;

trait CreatesClassFile
{
    public ?Panel $panel = null;

    public function createFileFromStub(string $stub, string $path, array $replacements = []): void
    {
        /** @var Filesystem */
        $filesystem = app(Filesystem::class);

        if (! file_exists($stubPath = base_path("stubs/page-builder-plugin/{$stub}.stub"))) {
            $stubPath = __DIR__."/../../../stubs/{$stub}.stub";
        }

        $contents = strtr(file_get_contents($stubPath), $replacements);

        $filesystem->ensureDirectoryExists(
            pathinfo($path, PATHINFO_DIRNAME)
        );

        $filesystem->put($path, $contents);
    }

    public function appClassToPath(string $class): string
    {
        $appNamespace = app()->getNamespace();
        $relativePath = Str::replaceFirst($appNamespace, '', $class);

        return app_path(
            str_replace('\\', '/', $relativePath).'.php'
        );
    }

    public function getPanelToCreateIn()
    {
        $panel = $this->option('panel');

        if ($panel) {
            $panel = Filament::getPanel($panel);
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

        return $panel;
    }

    public function getClassNameSpaces(string $folder): string
    {
        $namespace = array_map(
            fn (string $namespace): string => str_replace('Resources', $folder, $namespace),
            $this->panel->getResourceNamespaces(),
        );

        $namespace = (count($namespace) > 1) ?
            select(
                label: 'Which namespace would you like to create this in?',
                options: $namespace
            ) : (Arr::first($namespace));

        return $namespace;
    }
}
