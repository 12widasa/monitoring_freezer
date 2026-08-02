@props([
    'messages' => [],
])

@php
    $messages = array_filter((array) $messages);
@endphp

<ul {{ $attributes->class([
    'mt-1.5 space-y-1 text-xs text-rose-600',
    'hidden' => empty($messages),
]) }} aria-live="polite">
    @foreach ($messages as $message)
        <li>{{ $message }}</li>
    @endforeach
</ul>