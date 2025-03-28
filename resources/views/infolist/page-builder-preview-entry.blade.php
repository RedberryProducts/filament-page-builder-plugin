@php
    $blocks = $getBlocks();
    $shouldRenderWithIframe = $getRenderWithIframe();
    $state = $getState();
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @if (count($blocks) && $state)
       @if ($shouldRenderWithIframe)
            <iframe
                src="{{ $getIframeUrl() }}"
                x-data="{
                    data: @js($state),
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
        @else
            @foreach ($state as $block)
                @component($getViewForBlock($block['block_type']), ['block' => $block])
                @endcomponent
            @endforeach
       @endif
    @endif
</x-dynamic-component>
