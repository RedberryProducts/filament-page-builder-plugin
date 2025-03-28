<?php

namespace RedberryProducts\PageBuilderPlugin\Traits;

use Closure;

trait ListPreviewRendersWithIframe
{
    public bool $renderWithIframe = false;

    public string | Closure $iframeUrl = '';

    public function renderWithIframe(bool $renderWithIframe = true): static
    {
        $this->renderWithIframe = $renderWithIframe;

        return $this;
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
