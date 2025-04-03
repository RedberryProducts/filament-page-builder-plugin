<?php

namespace Redberry\PageBuilderPlugin\Traits;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait ListPreviewRendersWithIframe
{
    public bool $renderWithIframe = false;

    public array | Closure $iframeAttributes = [];

    public string | Closure $iframeUrl = '';

    public bool | Closure $autoResizeIframe = false;

    public function renderWithIframe(bool $renderWithIframe = true): static
    {
        $this->renderWithIframe = $renderWithIframe;

        return $this;
    }

    public function iframeAttributes(array | Closure $iframeAttributes): static
    {
        $this->iframeAttributes = $iframeAttributes;

        return $this;
    }

    public function getIframeAttributes(): ComponentAttributeBag
    {
        return new ComponentAttributeBag((array) $this->evaluate($this->iframeAttributes));
    }

    public function autoResizeIframe(bool | Closure $autoResizeIframe = true): static
    {
        $this->autoResizeIframe = $autoResizeIframe;

        return $this;
    }

    public function getAutoResizeIframe(): bool
    {
        return (bool) $this->evaluate($this->autoResizeIframe);
    }

    public function iframeUrl(string | Closure $iframeUrl): static
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
}
