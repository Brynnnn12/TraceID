@props(['config' => null])

<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1a1a2e] via-[#16213e] to-[#0f3460] p-6 text-white shadow-2xl transition-transform duration-300 hover:scale-[1.02]">
    <div class="pointer-events-none absolute -right-8 -top-8">
        <div class="relative">
            <div class="h-20 w-20 rounded-full bg-[#eb001b]/20 blur-xl"></div>
            <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-[#f79e1b]/20 blur-xl"></div>
        </div>
    </div>

    <div class="relative flex items-start justify-between">
        <div>
            <p class="text-xs font-medium uppercase tracking-wider text-gray-300">Bank Transfer</p>
            <p class="mt-1 text-sm font-semibold text-white/90">{{ $config->bank_name }}</p>
        </div>
        <div class="flex items-center gap-1">
            <div class="h-8 w-12 rounded bg-[#eb001b] shadow-lg"></div>
            <div class="h-8 w-12 rounded bg-[#f79e1b] shadow-lg"></div>
        </div>
    </div>

    <div class="relative mt-6 flex items-center gap-4">
        <div class="h-8 w-11 rounded-md bg-gradient-to-br from-yellow-200 to-yellow-500 p-0.5 shadow-inner">
            <div class="h-full w-full rounded-sm border border-yellow-100/40 bg-gradient-to-br from-transparent to-yellow-400/40"></div>
        </div>
        <svg class="h-6 w-6 text-white/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M7.5 10.5a2.5 2.5 0 014.6 1.3c0 1.8-3 1.9-3 3.7a2.5 2.5 0 004.7 1.1m.7-6.1a6 6 0 01-8.7 6.7m16.5-6.7a6 6 0 00-8.7-6.7m9.9 6.7a6 6 0 01-8.7 6.7" stroke-linecap="round" />
        </svg>
    </div>

    <div class="relative mt-5">
        <p class="text-xs font-medium uppercase tracking-wider text-gray-300">Nomor Rekening</p>
        <p class="mt-1 font-mono text-2xl font-medium tracking-widest">
            {{ $config->account_number }}
        </p>
    </div>

    <div class="relative mt-5 flex items-end justify-between">
        <div>
            <p class="text-xs font-medium uppercase tracking-wider text-gray-300">Jumlah Transfer</p>
            <p class="mt-1 text-2xl font-bold text-white">{{ $config->formattedAmount() }}</p>
        </div>
        @if (filled($config->notes))
            <p class="max-w-[45%] text-right text-xs text-gray-300">{{ $config->notes }}</p>
        @endif
    </div>

    <div class="pointer-events-none absolute -bottom-12 -right-12 h-32 w-32 rounded-full bg-white/5 blur-2xl"></div>
    <div class="pointer-events-none absolute -bottom-8 -right-8 h-20 w-20 rounded-full bg-white/5 blur-xl"></div>
</div>
