@php
    $logoPath = '/images/brand/logo-nome.webp';
@endphp

<img
    src="{{ $logoPath }}"
    alt="{{ config('app.name', 'Omoshindan') }}"
    {{ $attributes->merge(['class' => 'block object-contain']) }}
    loading="eager"
    decoding="async"
>
