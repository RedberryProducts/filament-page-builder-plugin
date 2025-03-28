<?php

namespace RedberryProducts\PageBuilderPlugin\Components\Forms;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Hidden;
use RedberryProducts\PageBuilderPlugin\Traits\ListPreviewRendersWithIframe;
use RedberryProducts\PageBuilderPlugin\Traits\PreviewRendersWithBlade;

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
        if (! $this->pageBuilderField) {
            throw new \Exception('Page builder field not set');
        }

        $data = $this->getGetCallback()();

        if ($this->singleItemPreview) {
            $blockType = $data['block_type'] ?? null;

            if ($blockType) {
                $formatted = $blockType::formatForSinglePreview($data['data']);

                return [
                    'id' => app(config('page-builder-plugin.block_model_class'))->newUniqueId(),
                    ...$data,
                    'block_name' => $blockType::getBlockName(),
                    'data' => $formatted,
                ];
            }

            return [];
        }

        $data = $data[$this->pageBuilderField] ?? [];

        return array_map(function ($item) {
            $blockType = $item['block_type'] ?? null;

            if ($blockType) {
                $formatted = $blockType::formatForSinglePreview($item['data']);

                return [
                    ...$item,
                    'block_name' => $blockType::getBlockName(),
                    'data' => $formatted,
                ];
            }
        }, $data);

        return [];
    }
}
