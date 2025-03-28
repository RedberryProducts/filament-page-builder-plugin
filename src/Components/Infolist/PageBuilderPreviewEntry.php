<?php

namespace RedberryProducts\PageBuilderPlugin\Components\Infolist;

use Filament\Infolists\Components\Entry;
use Illuminate\Database\Eloquent\Collection;
use RedberryProducts\PageBuilderPlugin\Contracts\BaseBlock;
use RedberryProducts\PageBuilderPlugin\Traits\ComponentLoadsPageBuilderBlocks;
use RedberryProducts\PageBuilderPlugin\Traits\ListPreviewRendersWithIframe;
use RedberryProducts\PageBuilderPlugin\Traits\PreviewRendersWithBlade;

class PageBuilderPreviewEntry extends Entry
{
    use ComponentLoadsPageBuilderBlocks;
    use ListPreviewRendersWithIframe;
    use PreviewRendersWithBlade;

    public string $view = 'page-builder-plugin::infolist.page-builder-preview-entry';

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpanFull();

        $this->relationship('pageBuilderBlocks');
    }

    public function relationship(
        string $relationship,
    ) {
        $this->relationship = $relationship;

        $this->getStateUsing(function () {
            /** @var Collection */
            $state = $this->getConstrainAppliedQuery($this->getRecord())->get();

            $state->transform(function ($item) {
                /** @var BaseBlock */
                $blockClass = $item->block_type;

                return [
                    ...$item->toArray(),
                    'data' => $blockClass::formatForListing($item->data),
                ];
            });

            return $state;
        });
    }
}
