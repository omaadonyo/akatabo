<div
    {{
        $attributes
            ->merge(['id' => $getId()], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->style(['display' => 'flex', 'gap' => '24px', 'align-items' => 'flex-start'])
    }}
>
    <div style="flex: 1; min-width: 0;">
        {{ $getChildSchema('left') }}
    </div>
    <div style="width: 420px; flex-shrink: 0;">
        {{ $getChildSchema('right') }}
    </div>
</div>
