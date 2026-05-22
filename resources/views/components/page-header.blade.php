@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'mb-5 flex flex-col gap-3 sm:mb-6 sm:flex-row sm:items-center sm:justify-between']) }}>
    <div class="min-w-0">
        <h1 class="truncate text-xl font-bold text-slate-900 sm:text-2xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-0.5 text-sm text-slate-600">{{ $subtitle }}</p>
        @endif
    </div>
    @if (isset($actions))
        <div class="flex shrink-0 flex-col gap-2 sm:flex-row sm:items-center">
            {{ $actions }}
        </div>
    @endif
</div>
