@php
    $gridDirection = $getGridDirection() ?? 'column';
    $id = $getId();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
    $options = $getOptions();
    $idSanitized = str_replace(['-', '.'], '_', $id);
    $hasNoCategories = count($options) === 1;
@endphp


<div class="flex items-start w-full justify-start">
    @if (!$hasNoCategories)
        <nav class="mr-4 flex sticky top-0 left-0 flex-col ">
            @foreach ($options as $category => $_)
                @continue(!$category)
                <button type="button" x-data="{
                    scrollToBlockGroup() {
                        const blockGroup = document.querySelector(
                            @js('#' . $idSanitized . '-' . $category . '-header')
                        );
                        blockGroup.scrollIntoView({ behavior: 'smooth' });
                    }
                }" @click="scrollToBlockGroup"
                    class="
                        rounded-md dark:hover:bg-white/10 p-2 transition-all
                    ">
                    {{ $category }}
                </button>
            @endforeach
        </nav>
    @endif
    <div class="flex-col flex">
        @foreach ($options as $category => $categoryOptions)
            <section class="w-full">
                <h1 id="{{ $idSanitized . '-' . $category . '-header' }}" class="my-2">{{ $category }}</h1>
                <hr class="mb-2  dark:border-white/10 border-gray-200" />
                <x-filament::grid :default="$getColumns('default')" :sm="$getColumns('sm')" :md="$getColumns('md')" :lg="$getColumns('lg')"
                    :xl="$getColumns('xl')" :two-xl="$getColumns('2xl')" :direction="$gridDirection" :attributes="\Filament\Support\prepare_inherited_attributes($attributes)
                        ->merge($getExtraAttributes(), escape: false)
                        ->class(['fi-fo-radio gap-4 w-full'])">
                    @foreach ($categoryOptions as $value => $label)
                        @php
                            $thumbnail = $getBlockThumbnail($value);
                        @endphp
                        <div @class([
                            'break-inside-avoid' => $gridDirection === 'column',
                        ])>
                            <label for="{{ $id . '-' . $value }}" class="flex w-full h-full group">
                                <x-filament::input.radio :valid="!$errors->has($statePath)" :attributes="\Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())
                                    ->merge(
                                        [
                                            'disabled' => $isDisabled || $isOptionDisabled($value, $label),
                                            'id' => $id . '-' . $value,
                                            'name' => $id,
                                            'value' => $value,
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
                                     dark:bg-gray-900 dark:border-white/10  dark:hover:bg-white/5 hover:bg-gray-50">
                                    <span
                                        class="text-sm font-medium leading-6 text-gray-950 dark:text-white">{{ $label }}</span>
                                    @if ((bool) $thumbnail)
                                        @if ($thumbnail instanceof \Illuminate\Contracts\Support\Htmlable)
                                            {!! $thumbnail !!}
                                        @else
                                            <img src="{{ $thumbnail }}" alt="{{ $label }}"
                                                class="w-full h-32 object-cover rounded-lg mt-2">
                                        @endif
                                    @endif
                                </div>
                            </label>
                        </div>
                    @endforeach
                </x-filament::grid>
            </section>
        @endforeach
    </div>
</div>
