@props([
    'amount',
    'decimals' => 2,
    'currency' => true,
])

@php
    $value = (float) ($amount ?? 0);
@endphp

<span {{ $attributes->class(['text-danger' => $value < 0]) }}>
    {{ accounting_format($value, $decimals, $currency) }}
</span>
