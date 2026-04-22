@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'breadcrumbs' => [],
])

<div {{ $attributes->merge(['class' => 'app-page-header']) }}>
    @if (! empty($breadcrumbs))
        <x-breadcrumbs :items="$breadcrumbs" />
    @endif

    @if ($eyebrow)
        <p class="{{ ! empty($breadcrumbs) ? 'mt-4' : '' }} text-xs font-semibold uppercase tracking-[0.32em] text-sky-500">
            {{ $eyebrow }}
        </p>
    @endif

    <div class="mt-4 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-900">
                {{ $title }}
            </h1>

            @if ($description)
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">
                    {{ $description }}
                </p>
            @endif
        </div>

        @isset($actions)
            <div class="flex flex-wrap items-center gap-3">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
