<?php

namespace Redberry\PageBuilderPlugin\Resources\GlobalBlockConfigResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Redberry\PageBuilderPlugin\Resources\GlobalBlockConfigResource;

class ListGlobalBlocks extends ListRecords
{
    protected static string $resource = GlobalBlockConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh_blocks')
                ->label('Refresh Global Blocks')
                ->icon('heroicon-o-arrow-path')
                ->action('refreshGlobalBlocks')
                ->color('gray'),
        ];
    }

    public function refreshGlobalBlocks(): void
    {
        $this->getResource()::getModel()::refreshGlobalBlocks();

        \Filament\Notifications\Notification::make()
            ->title('Global blocks refreshed successfully')
            ->success()
            ->send();

        $this->redirect($this->getResource()::getUrl('index'));
    }
}