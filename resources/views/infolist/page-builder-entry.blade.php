@php
    $blocks = $getBlocks();
    $state = $getState();
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @if (count($blocks) && $state)
        <ul>
            <x-filament::grid class="items-start gap-4">
                @foreach ($state as $item)
                    <li
                        class="fi-fo-repeater-item divide-y divide-gray-100 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-white/5 dark:ring-white/10">
                        <div class ='fi-fo-repeater-item-header flex items-center gap-x-3 overflow-hidden px-4 py-3 '>
                            <div class="flex justify-between w-full items-center">
                                {{ $item['block_type']::getBlockLabel($item, $loop->index) }}
                            </div>
                        </div>
                    </li>
                @endforeach
            </x-filament::grid>
        </ul>
    @endif
</x-dynamic-component>
