@props([
    'items' => [],
])

@if (! empty($items))
    <nav aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
            @foreach ($items as $item)
                @php
                    $label = is_array($item) ? ($item['label'] ?? '') : (string) $item;
                    $href = is_array($item) ? ($item['href'] ?? null) : null;
                    $current = is_array($item) ? ($item['current'] ?? false) : false;
                @endphp

                <li class="flex items-center gap-2">
                    @if (! $loop->first)
                        <span aria-hidden="true" class="text-slate-300">/</span>
                    @endif

                    @if ($href && ! $current)
                        <a href="{{ $href }}" class="font-medium text-slate-500 transition hover:text-slate-900">
                            {{ $label }}
                        </a>
                    @else
                        <span @if($current) aria-current="page" @endif class="{{ $current ? 'font-semibold text-slate-900' : 'font-medium text-slate-500' }}">
                            {{ $label }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
