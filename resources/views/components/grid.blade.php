@props([
    'isGrid' => true,
    'default' => 1,
    'direction' => 'row',
    'sm' => null,
    'md' => null,
    'lg' => null,
    'xl' => null,
    'twoXl' => null,
])

   <div    {{
    $attributes
        ->style(
            match ($direction) {
                'column' => [
                    "columns: {$default}" => $default,
                ],
                'row' => [
                    "display: grid" => $isGrid,
                    "grid-template-columns: repeat({$default}, minmax(0, 1fr))" => $default,
                ],
            },
        )
    }}
>
    {{ $slot }}
</div>
