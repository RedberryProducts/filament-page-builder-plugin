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
            <iframe
                src="{{ $getIframeUrl() }}"
                x-data="{
                    data: @js($state),
                    ready: $wire.entangle('{{ $getStatePath() }}.ready'),
                    @if($autoResizeIframe)
                        height: $wire.entangle('{{ $getStatePath() }}.height'),
                    @endif
                    init() {
                        if (this.ready) {
                            $root.contentWindow.postMessage(JSON.stringify(this.data), '*');
                        }
                    }
                }"
                 {{-- TODO: move this to alpine component --}}
                @message.window="() => {
                    if (!$data.ready) {
                        $data.ready = $event.data?.type === 'readyForPreview';
                        $root.contentWindow.postMessage(JSON.stringify($data.data), '*');
                    }
                    if ($event.data?.type === 'previewResized') {
                        $data.height = $event.data.height + 'px';
                    }
                }"
                frameborder="0"
                allowfullscreen
                {{
                    $iframeAttributes->merge([
                        'class' => 'w-full',
                    ])->when($autoResizeIframe, fn ($attributes) => $attributes->merge([
                        'x-bind:height' => 'height',
                    ]))
                }}
            >
            </iframe>
        @else
            @foreach ($state as $block)
                @component($getViewForBlock($block['block_type']), ['block' => $block])
                @endcomponent
            @endforeach
       @endif
    @endif
</x-dynamic-component>
