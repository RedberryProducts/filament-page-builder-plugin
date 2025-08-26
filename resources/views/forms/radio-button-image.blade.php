@php
    $gridDirection = $getGridDirection() ?? 'row';
    $id = $getId();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
    $options = $getOptions();
    $idSanitized = str_replace(['-', '.'], '_', $id);
    $hasNoCategories = count($options) === 1;
    $defaultCategory = $getDefaultCategory();
    $formattedOptions = $getFormattedOptions();
@endphp
<div x-data="{ activeTab: @js($defaultCategory), options: @js($formattedOptions) }" class="">
    @if (!$hasNoCategories)
        <x-filament::tabs contained="true">
            @foreach ($options as $category => $_)
                @continue(!$category)
                @if (!$isCategoryClass($category))
                    <x-filament::tabs.item x-on:click="activeTab = '{{ addslashes($category) }}'"
                        alpine-active="activeTab === '{{ addslashes($category) }}'">
                        {{ $getCategoryTitle($category) }}
                    </x-filament::tabs.item>
                @else
                    <x-filament::tabs.item :attributes="$category::getCategoryAttributes()->merge([
                        'icon' => $category::getCategoryIcon(),
                    ])" x-on:click="activeTab = '{{ addslashes($category) }}'"
                        alpine-active="activeTab === '{{ addslashes($category) }}'">
                        {{ $getCategoryTitle($category) }}
                    </x-filament::tabs.item>
                @endif
            @endforeach
            <x-filament::tabs.item
                x-on:click="activeTab = 'all'"
                alpine-active="activeTab === 'all'"
                :attributes="$getAllTabAttributes()"
            >
                {{ __('All') }}
            </x-filament::tabs.item>
        </x-filament::tabs>
    @endif
    <div class="flex-col mt-4 flex">
        <x-filament::grid :default="$getColumns('default')" :sm="$getColumns('sm')" :md="$getColumns('md')" :lg="$getColumns('lg')" :xl="$getColumns('xl')"
            :two-xl="$getColumns('2xl')" :direction="$gridDirection" :isGrid="true" :attributes="\Filament\Support\prepare_inherited_attributes($attributes)
                ->merge($getExtraAttributes(), escape: false)
                ->class(['gap-4 w-full'])">
            @foreach ($formattedOptions as $option)
                <template x-if="activeTab === @js($option['category_class']) || activeTab === 'all'">
                    <div @class([
                        'break-inside-avoid' => $gridDirection === 'column',
                    ])>
                        <label for="{{ $id . '-' . $option['class'] }}" class="flex w-full group">
                            <x-filament::input.radio :valid="!$errors->has($statePath)" :attributes="\Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())
                                ->merge(
                                    [
                                        'disabled' =>
                                            $isDisabled || $isOptionDisabled($option['class'], $option['label']),
                                        'id' => $id . '-' . $option['class'],
                                        'name' => $id,
                                        'value' => $option['class'],
                                        'wire:loading.attr' => 'disabled',
                                        $applyStateBindingModifiers('wire:model') => $statePath,
                                    ],
                                    escape: false,
                                )
                                ->class(['peer hidden'])" />
                            <div
                                class="border-gray-200 cursor-pointer px-2 pb-2 peer-checked:bg-gray-100
                            dark:peer-checked:bg-white/10 peer-checked:border-primary-500
                             transition-all rounded-lg text-center border w-full bg-white
                             peer-disabled:bg-gray-100 peer-disabled:cursor-not-allowed
                             dark:peer-disabled:bg-gray-800 dark:border-gray-700
                             dark:bg-gray-900 dark:border-white/10  dark:hover:bg-white/5 hover:bg-gray-50">
                                <span
                                    class="text-sm font-medium leading-6 text-gray-950 dark:text-white">{{ $option['label'] }}</span>
                                @if ((bool) $option['thumbnail'] ?? false)
                                    @if ($option['thumbnail'] instanceof \Illuminate\Contracts\Support\Htmlable)
                                        {!! $option['thumbnail'] !!}
                                    @else
                                        <img src="{{ $option['thumbnail'] }}" alt="{{ $option['label'] }}"
                                            class="w-full h-32 object-cover rounded-lg mt-2">
                                    @endif
                                @endif
                            </div>
                        </label>
                    </div>
                </template>
            @endforeach
            </template>
        </x-filament::grid>
    </div>
</div>
