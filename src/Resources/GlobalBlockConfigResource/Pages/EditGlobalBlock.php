<?php

namespace Redberry\PageBuilderPlugin\Resources\GlobalBlockConfigResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Redberry\PageBuilderPlugin\Resources\GlobalBlockConfigResource;

class EditGlobalBlock extends EditRecord
{
    protected static string $resource = GlobalBlockConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Preview Block')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(function () {
                    return null;
                })
                ->hidden(true),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $blockClass = $this->record->class_name;
        
        if (class_exists($blockClass) && $this->record->configuration) {
            try {
                if (method_exists($blockClass, 'getBaseBlockSchema')) {
                    $schema = $blockClass::getBaseBlockSchema();
                } else {
                    $schema = $blockClass::getBlockSchema();
                }

                foreach ($schema as $field) {
                    if (method_exists($field, 'getName')) {
                        $fieldName = $field->getName();
                        $configValue = $this->record->getConfigValue($fieldName);
                        if ($configValue !== null) {
                            $data[$fieldName] = $configValue;
                        }
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $configuration = [];
        $blockClass = $this->record->class_name;
        
        if (class_exists($blockClass)) {
            try {
                if (method_exists($blockClass, 'getBaseBlockSchema')) {
                    $schema = $blockClass::getBaseBlockSchema();
                } else {
                    $schema = $blockClass::getBlockSchema();
                }

                foreach ($schema as $field) {
                    if (method_exists($field, 'getName')) {
                        $fieldName = $field->getName();
                        if (array_key_exists($fieldName, $data)) {
                            $configuration[$fieldName] = $data[$fieldName];
                        }
                    }
                }
            } catch (\Exception $e) {
            }
        }

        $data['configuration'] = $configuration;
        
        if (class_exists($blockClass)) {
            try {
                if (method_exists($blockClass, 'getBaseBlockSchema')) {
                    $schema = $blockClass::getBaseBlockSchema();
                } else {
                    $schema = $blockClass::getBlockSchema();
                }

                foreach ($schema as $field) {
                    if (method_exists($field, 'getName')) {
                        $fieldName = $field->getName();
                        unset($data[$fieldName]);
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}