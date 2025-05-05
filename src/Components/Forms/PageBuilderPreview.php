<?php

namespace Redberry\PageBuilderPlugin\Components\Forms;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Hidden;
use Redberry\PageBuilderPlugin\Traits\ListPreviewRendersWithIframe;
use Redberry\PageBuilderPlugin\Traits\PreviewRendersWithBlade;

class PageBuilderPreview extends Field
{
    use ListPreviewRendersWithIframe;
    use PreviewRendersWithBlade;

    public ?string $pageBuilderField = null;

    public bool $singleItemPreview = false;

    public string $view = 'page-builder-plugin::forms.page-builder-preview';

    protected function setUp(): void
    {
        parent::setUp();

        $this->schema([
            Hidden::make('ready')->default(false),
            Hidden::make('height')->default(0),
        ]);

        $this->dehydrated(false);
    }

    public function pageBuilderField(string $pageBuilderField): static
    {
        $this->pageBuilderField = $pageBuilderField;

        return $this;
    }

    public function getPageBuilderField(): ?string
    {
        return $this->pageBuilderField;
    }

    public function singleItemPreview(bool $singleItemPreview = true): static
    {
        $this->singleItemPreview = $singleItemPreview;

        return $this;
    }

    public function getSingleItemPreview(): bool
    {
        return $this->singleItemPreview;
    }

    public function getPageBuilderData(): array
    {
        if (is_null($this->pageBuilderField)) {
            throw new \Exception('Page builder field not set');
        }

        $data = $this->getGetCallback()($this->pageBuilderField);

        if ($this->singleItemPreview) {
            $blockType = $data['block_type'] ?? null;
            $id = $data['block_id'] ?? null;

            if ($blockType) {
                $closure = Closure::fromCallable([$blockType, 'formatForSingleView']);
                $formatted = $this->evaluate($closure, [
                    'data' => $data['data'] ?? [],
                ]);

                return [
                    'id' => $id,
                    'block_name' => $blockType::getBlockName(),
                    'block_type' => $blockType,
                    'data' => $formatted,
                ];
            }

            return [];
        }

        $data = $data ?? [];

        return array_map(function ($item) {
            $blockType = $item['block_type'] ?? null;

            if ($blockType) {
                $closure = Closure::fromCallable([$blockType, 'formatForListingView']);
                $formatted = $this->evaluate($closure, [
                    'data' => $item['data'] ?? [],
                ]);

                return [
                    ...$item,
                    'block_name' => $blockType::getBlockName(),
                    'block_type' => $blockType,
                    'data' => $formatted,
                ];
            }
        }, $data);

    }
}
