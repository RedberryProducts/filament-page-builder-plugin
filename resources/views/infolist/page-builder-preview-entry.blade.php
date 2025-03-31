@php
    $blocks = $getBlocks();
    $shouldRenderWithIframe = $getRenderWithIframe();
    $state = $getState();
    $iframeAttributes = $getIframeAttributes();
    $autoResizeIframe = $getAutoResizeIframe();
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @if (count($blocks) && $state)
       @if ($shouldRenderWithIframe)
            <x-page-builder-plugin::iframe
                url="{{ $getIframeUrl() }}"
                :data=$state
                statePath="{{ $getStatePath() }}"
                autoResizeIframe="{{ $autoResizeIframe }}"
                :attributes=$iframeAttributes
            />
        @else
            @foreach ($state as $block)
                @component($getViewForBlock($block['block_type']), ['block' => $block])
                @endcomponent
            @endforeach
       @endif
    @endif
</x-dynamic-component>
