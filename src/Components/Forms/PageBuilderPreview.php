<?php

namespace RedberryProducts\PageBuilderPlugin\Components\Forms;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Hidden;

class PageBuilderPreview extends Field
{
    public string|null $pageBuilderField = null;

    public bool $renderWithIframe = false;

    public string|Closure $iframeUrl = '';

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

    public function getPageBuilderField(): string|null
    {
        return $this->pageBuilderField;
    }

    public function singleItemPreview(bool $singleItemPreview = true): static
    {
        $this->singleItemPreview = $singleItemPreview;

        return $this;
    }

    public function renderWithIframe(bool $renderWithIframe = true): static
    {
        $this->renderWithIframe = $renderWithIframe;

        return $this;
    }

    public function iframeUrl(string|Closure $iframeUrl): static
    {
        $this->iframeUrl = $iframeUrl;
        $this->renderWithIframe();

        return $this;
    }

    public function getIframeUrl(): string
    {
        return (string) $this->evaluate($this->iframeUrl);
    }

    public function getRenderWithIframe(): bool
    {
        return $this->renderWithIframe;
    }

    public function getSingleItemPreview(): bool
    {
        return $this->singleItemPreview;
    }

    public function getViewForBlock(string $class)
    {
        $view = $class::getView();

        if ($view) {
            return $view;
        }

        throw new \Exception('View not found for block ' . $class . " if you want to use view method of rendering you need to declare view for a block.");
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
                    'block_type' => $blockType,
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
                    'block_type' => $blockType,
                    'data' => $formatted,
                ];
            }
        }, $data);

        return [];
    }
}