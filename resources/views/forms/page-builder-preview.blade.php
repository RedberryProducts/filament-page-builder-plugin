@php
    $shouldRenderWithIframe = $getRenderWithIframe();
    $singleItemPreview = $getSingleItemPreview();
    $pageBuilderData = $getPageBuilderData();
@endphp
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @if (!$shouldRenderWithIframe)
        @if ($singleItemPreview)
            @component($getViewForBlock($pageBuilderData['block_type']), ['block' => $pageBuilderData])
            @endcomponent
        @else
            @foreach ($pageBuilderData as $block)
                @component($getViewForBlock($block['block_type']), ['data' => $block['data'], 'id' => $block['id']])
                @endcomponent
            @endforeach
        @endif
    @else
        {{-- TODO: give it better height calculations. --}}
        <iframe
            src="{{ $getIframeUrl() }}"
            x-data="{
                data: @js($singleItemPreview ? $pageBuilderData['data'] : $pageBuilderData),
                ready: $wire.entangle('{{ $getStatePath() }}.ready'),
                init() {
                    if (this.ready) {
                        $root.contentWindow.postMessage(JSON.stringify(this.data), '*');
                    }
                }
            }"
            @message.window="() => {
                if (!$data.ready) {
                    $data.ready = $event.data === 'readyForData';
                    $root.contentWindow.postMessage(JSON.stringify($data.data), '*');
                }
            }"
            class="w-full h-screen"
            frameborder="0"
            allowfullscreen
        >
        </iframe>
    @endif
</x-dynamic-component>
