@extends('layouts.app')

@section('title', 'Pricing plans - HublyBot')

@section('content')
<div class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Page Header -->
    <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
        <h1 class="text-xs font-semibold uppercase tracking-widest text-[#5865F2]">Simple Plans</h1>
        <p class="text-4xl sm:text-5xl font-extrabold text-white">Choose the best fit for your server</p>
        <p class="text-slate-400">Start for free, then upgrade to unlock more bot slots, faster speeds, and advanced analytics.</p>
        
        <!-- Billing cycles (1 Month, 1 Year, 2 Years) -->
        <div class="pt-6 flex items-center justify-center">
            <div class="inline-flex rounded-xl p-1 bg-[#0a0b16] border border-white/5">
                <button onclick="setBillingCycle(1)" id="cycle-btn-1" class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 text-slate-400 hover:text-white cursor-pointer">
                    1 Month
                </button>
                <button onclick="setBillingCycle(12)" id="cycle-btn-12" class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 text-white bg-[#5865F2] cursor-pointer">
                    1 Year (12 Months) <span class="ml-1 text-[9px] bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-1.5 py-0.5 rounded-full">Save 20%</span>
                </button>
                <button onclick="setBillingCycle(24)" id="cycle-btn-24" class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 text-slate-400 hover:text-white cursor-pointer">
                    2 Years (24 Months) <span class="ml-1 text-[9px] bg-purple-500/10 text-purple-400 border border-purple-500/20 px-1.5 py-0.5 rounded-full font-mono">Save 30%</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Pricing Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch mb-20">
        
        <!-- Free Plan -->
        <div class="cyber-card rounded-2xl p-8 flex flex-col justify-between relative overflow-hidden">
            <div>
                <h3 class="text-lg font-bold text-white mb-2">Free</h3>
                <p class="text-xs text-slate-400 leading-relaxed mb-6">Perfect to test the creator and set up a basic bot.</p>
                
                <div class="flex items-baseline text-white mb-8">
                    <span class="text-4xl font-extrabold tracking-tight">0$</span>
                    <span class="text-xs text-slate-400 ml-1">/forever</span>
                </div>
                
                <!-- Quota specs -->
                <div class="bg-[#090a14] border border-white/5 rounded-xl p-4 mb-8 space-y-2.5 font-mono text-[10px]">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">BOT SLOTS</span>
                        <span class="font-bold text-white">1 Active Bot</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">RUN SPEED</span>
                        <span class="font-bold text-white">Standard Speed</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">RUN TIME LIMIT</span>
                        <span class="font-bold text-white">20 hours/week</span>
                    </div>
                </div>

                <!-- Features list -->
                <ul class="space-y-4 text-xs text-slate-300">
                    <li class="flex items-start gap-3">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-400 shrink-0"></i>
                        <span>7-day server stats history</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-400 shrink-0"></i>
                        <span>All standard creator actions</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i data-lucide="x" class="w-4 h-4 text-rose-500 shrink-0"></i>
                        <span class="text-slate-500">Includes HublyBot signature</span>
                    </li>
                </ul>
            </div>

            <div class="mt-8">
                @guest
                    <a href="{{ route('auth.redirect', ['redirect' => route('products.builder')]) }}" class="w-full flex items-center justify-center bg-white/5 hover:bg-white/10 text-white font-bold py-2.5 rounded-xl border border-white/10 text-xs transition duration-300 cursor-pointer">
                        Choose Free
                    </a>
                @else
                    <a href="{{ route('products.builder') }}" class="w-full flex items-center justify-center bg-white/5 hover:bg-white/10 text-white font-bold py-2.5 rounded-xl border border-white/10 text-xs transition duration-300 cursor-pointer">
                        Choose Free
                    </a>
                @endguest
            </div>
        </div>

        <!-- Dynamic Paid Plans from DB -->
        @foreach($plans as $plan)
            @php
                $isRecommended = $plan->slug === 'premium';
            @endphp
            <div class="{{ $isRecommended ? 'bg-[#0b0c1b]/80 border-2 border-purple-500 rounded-2xl p-8 flex flex-col justify-between relative overflow-hidden shadow-2xl shadow-purple-900/10' : 'cyber-card rounded-2xl p-8 flex flex-col justify-between relative overflow-hidden' }}">
                @if($isRecommended)
                    <div class="absolute top-0 right-0 bg-purple-500 text-white text-[9px] font-bold uppercase py-1 px-3.5 rounded-bl-xl tracking-wider">
                        Recommended
                    </div>
                @endif
                
                <div>
                    <h3 class="text-lg font-bold text-white mb-2">{{ $plan->name }}</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-6">
                        @if($plan->slug === 'premium')
                            Designed for expanding server communities and support needs.
                        @else
                            High capacity allocations for large networks and creators.
                        @endif
                    </p>
                    
                    <div class="flex items-baseline text-white mb-8">
                        <span id="{{ $plan->slug }}-price" class="text-4xl font-extrabold tracking-tight"></span>
                        <span class="text-xs text-slate-400 ml-1">/month</span>
                    </div>
                    
                    <!-- Quota specs -->
                    <div class="bg-[#090a14] border border-white/5 rounded-xl p-4 mb-8 space-y-2.5 font-mono text-[10px]">
                        @if($plan->slug === 'premium')
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">BOT SLOTS</span>
                                <span class="font-bold text-white">Up to 3 Bots</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">RUN SPEED</span>
                                <span class="font-bold text-white">High Speed</span>
                            </div>
                        @else
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">BOT SLOTS</span>
                                <span class="font-bold text-white">Up to 10 Bots</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">RUN SPEED</span>
                                <span class="font-bold text-white">Ultra Speed</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">RUN TIME LIMIT</span>
                            <span class="font-bold text-white">24/7 Continuous</span>
                        </div>
                    </div>

                    <!-- Features list from DB -->
                    <ul class="space-y-4 text-xs text-slate-300">
                        @foreach($plan->features as $feature)
                            <li class="flex items-start gap-3">
                                <i data-lucide="check" class="w-4 h-4 text-emerald-400 shrink-0"></i>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-8">
                    <a id="{{ $plan->slug }}-choose-btn" href="#" class="w-full flex items-center justify-center {{ $isRecommended ? 'bg-purple-600 hover:bg-purple-700 text-white shadow-lg shadow-purple-900/30' : 'bg-white/5 hover:bg-white/10 text-white border border-white/10' }} font-bold py-2.5 rounded-xl text-xs transition duration-300 cursor-pointer">
                        Choose {{ $plan->name }}
                    </a>
                </div>
            </div>
        @endforeach

    </div>
</div>

<!-- Scripting for live billing cycle switch -->
<script>
    let currentCycle = 12;

    const planPrices = {
        @foreach($plans as $plan)
            '{{ $plan->slug }}': {
                1: '{{ $plan->price_1 }}',
                12: '{{ $plan->price_12 }}',
                24: '{{ $plan->price_24 }}'
            },
        @endforeach
    };

    function setBillingCycle(months) {
        currentCycle = months;

        // Reset all buttons state
        [1, 12, 24].forEach(m => {
            const btn = document.getElementById('cycle-btn-' + m);
            if (btn) {
                if (m === months) {
                    btn.className = "px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 text-white bg-[#5865F2] cursor-pointer";
                } else {
                    btn.className = "px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 text-slate-400 hover:text-white cursor-pointer";
                }
            }
        });

        // Update pricing displays
        for (const [slug, prices] of Object.entries(planPrices)) {
            const priceEl = document.getElementById(slug + '-price');
            if (priceEl) {
                priceEl.innerText = '$' + prices[months];
            }

            // Update choose button URLs
            const btnEl = document.getElementById(slug + '-choose-btn');
            if (btnEl) {
                let newHref;
                @guest
                    const startUrl = encodeURIComponent("{{ route('checkout.start', ['plan' => 'SLUG_PLACEHOLDER']) }}?cycle=MONTHS_PLACEHOLDER"
                        .replace('SLUG_PLACEHOLDER', slug)
                        .replace('MONTHS_PLACEHOLDER', months));
                    newHref = "{{ route('auth.redirect') }}?redirect=" + startUrl;
                @else
                    const baseUrl = "{{ route('checkout.start', ['plan' => 'SLUG_PLACEHOLDER']) }}".replace('SLUG_PLACEHOLDER', slug);
                    newHref = baseUrl + '?cycle=' + months;
                @endguest
                btnEl.setAttribute('href', newHref);
            }
        }
    }

    // Initialize with 12 months
    setBillingCycle(12);
</script>
@endsection
