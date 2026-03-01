@php
    $blocks = $getBlocks();
    $state = $getState();
    $statePath = $getStatePath();
    $key = $getKey();
    $reorderActionName = $getReorderActionName();
    $selectBlockAction = $getAction($getSelectBlockActionName());
    $selectBlockActionIsVisible = $selectBlockAction->isVisible();
    $reorderAction = $getAction($reorderActionName);
    $reorderAction = $reorderAction([]);
    $reorderActionIsVisible = $reorderAction->isVisible();
@endphp


<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @if (count($blocks) && $state)
        <ul>
            <x-page-builder-plugin::grid
                x-sortable
                x-on:end.stop="$wire.mountAction('{{ $reorderActionName }}', { items: $event.target.sortable.toArray() }, { schemaComponent: '{{ $key }}' })"
                class="items-start gap-4">
            @foreach ($state as $item)
                @php
                    $deleteAction = $getAction($getDeleteActionName());
                    $deleteAction = $deleteAction(['item' => $item, 'index' => $loop->index]);
                    $deleteActionIsVisible = $deleteAction->isVisible();
                    $editAction = $getAction($getEditActionName());
                    $editAction = $editAction(['item' => $item, 'index' => $loop->index]);
                    $editActionIsVisible = $editAction->isVisible();

                    $blockType = $item['block_type'] ?? null;
                    $isGlobalBlock = $blockType &&
                        class_exists($blockType) &&
                        method_exists($blockType, 'isGlobalBlock') &&
                        $blockType::isGlobalBlock();
                @endphp
                <li x-sortable-item="{{ $item['id'] }}"
                    class="fi-fo-repeater-item divide-y divide-gray-100 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-white/5 dark:ring-white/10">
                    <div class="fi-fo-repeater-item-header flex items-center gap-x-3 overflow-hidden px-4 py-3">
                        @if ($reorderActionIsVisible)
                            {{ $renderReorderActionButton($item['id'], $loop->index) }}
                        @endif
                        <div class="flex justify-between w-full items-center">
                            {{ $getBlockLabel($item['block_type'], $item, $loop->index) }}
                            <div class="flex gap-x-4 items-center">
                                @if ($editActionIsVisible)
                                    @if ($isGlobalBlock)
                                        <div class="px-3 py-2">
                                            <x-heroicon-o-globe-alt class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                                        </div>
                                    @else
                                        {{ $renderEditActionButton($item['id'], $loop->index) }}
                                    @endif
                                @endif
                                @if ($deleteActionIsVisible)
                                    {{ $renderDeleteActionButton($item['id'], $loop->index) }}
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
            </x-page-builder-plugin::grid>
        </ul>
    @endif

    @if ($selectBlockActionIsVisible)
        <div @class(['mt-4' => count($blocks) && $state])>
            {{ $selectBlockAction }}
        </div>
    @endif
</x-dynamic-component>
