@props([
    'url',
    'data',
    'statePath',
    'autoResizeIframe'
])

<iframe
    src="{{ $url }}"
    x-data="{
        data: @js($data),
        ready: $wire.entangle('{{ $statePath }}.ready'),
        @if($autoResizeIframe)
            height: $wire.entangle('{{ $statePath }}.height'),
        @endif
        init() {
            if (this.ready) {
                $root.contentWindow.postMessage(JSON.stringify(this.data), '*');
            }
        }
    }"
    @message.window="() => {
        if (!$data.ready) {
            $data.ready = $event.data?.type === 'readyForPreview';
            $root.contentWindow.postMessage(JSON.stringify($data.data), '*');
        }
        if ($event.data?.type === 'previewResized') {
            $data.height = $event.data.height + 'px';
        }
    }"
    {{
        $attributes->merge([
            'class' => 'w-full',
        ])->when($autoResizeIframe, fn ($attributes) => $attributes->merge([
            'x-bind:height' => 'height',
        ]))
    }}
></iframe>