@php
    $shouldRenderWithIframe = $getRenderWithIframe();
    $singleItemPreview = $getSingleItemPreview();
    $pageBuilderData = $getPageBuilderData();
    $iframeAttributes = $getIframeAttributes();
    $autoResizeIframe = $getAutoResizeIframe();
@endphp
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @if (count($pageBuilderData))
        @if (!$shouldRenderWithIframe)
            @if ($singleItemPreview)
                @component($getViewForBlock($pageBuilderData['block_type']), ['block' => $pageBuilderData])
                @endcomponent
            @else
                @foreach ($pageBuilderData as $block)
                    @component($getViewForBlock($block['block_type']), ['block' => $block])
                    @endcomponent
                @endforeach
            @endif
        @else
            <x-page-builder-plugin::iframe url="{{ $getIframeUrl() }}" :data=$pageBuilderData
                statePath="{{ $getStatePath() }}" autoResizeIframe="{{ $autoResizeIframe }}"
                :attributes=$iframeAttributes />
        @endif
    @endif
</x-dynamic-component>
