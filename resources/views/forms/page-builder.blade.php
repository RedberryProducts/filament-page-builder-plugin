@php
    $blocks = $getBlocks();
    $state = $getState();
    $selectBlockAction = $getAction($getSelectBlockActionName());
@endphp


<x-dynamic-component :component="$getFieldWrapperView()" :hintActions="[$selectBlockAction]" :field="$field">
    @if (count($blocks) && $state)
        <ul>
            <x-filament::grid class="items-start gap-4">
                @foreach ($state as $item)
                    @php
                        $deleteAction = $getAction($getDeleteActionName());
                        $deleteAction = $deleteAction(['item' => $item]);
                        $deleteActionIsVisible = $deleteAction->isVisible();
                        $editAction = $getAction($getEditActionName());
                        $editAction = $editAction(['item' => $item]);
                        $editActionIsVisible = $editAction->isVisible();
                    @endphp
                    <li
                        class="fi-fo-repeater-item divide-y divide-gray-100 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-white/5 dark:ring-white/10">
                        <div
                            class ='fi-fo-repeater-item-header flex items-center gap-x-3 overflow-hidden px-4 py-3 justify-between'>
                            {{ $item['block_type']::getBlockLabel($item, $loop->index) }}
                            <div class="flex gap-x-4 items-center">
                                @if ($deleteActionIsVisible)
                                    {{ $renderDeleteActionButton($item['id']) }}
                                @endif
                                @if ($editActionIsVisible)
                                    {{ $renderEditActionButton($item['id']) }}
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </x-filament::grid>
        </ul>
    @endif
</x-dynamic-component>
