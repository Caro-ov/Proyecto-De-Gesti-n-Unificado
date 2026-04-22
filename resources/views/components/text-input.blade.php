@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-sky-500 focus:ring-sky-500']) }}>
