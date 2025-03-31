<?php

namespace RedberryProducts\PageBuilderPlugin\Components\Infolist;

use Closure;
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

        $this->relationship();
    }

    public function relationship(
        string $relationship = "pageBuilderBlocks",
        ?Closure $modifyRelationshipQueryUsing = null,
    ) {
        $this->relationship = $relationship;
        $this->modifyRelationshipQueryUsing = $modifyRelationshipQueryUsing;

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
