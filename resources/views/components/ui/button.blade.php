@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button'])
@php
    $classes = 'ui-button ui-button-'.$variant.' ui-button-'.$size;
    $tag = $href ? 'a' : 'button';
@endphp
<{{ $tag }} @if($href) href="{{ $href }}" @else type="{{ $type }}" @endif {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</{{ $tag }}>
