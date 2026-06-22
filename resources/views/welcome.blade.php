@extends('layouts.app')

@section('title', 'HublyBot - Create Discord Bots Without Code')

@section('content')
<!-- Hero Section -->
<div class="relative overflow-hidden pt-32 pb-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-6">
        
        <h1 class="text-5xl sm:text-6xl font-extrabold tracking-tight text-white leading-tight">
            Create Your Own <br class="sm:hidden">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#5865F2] to-[#a855f7]">Discord Bot. No Code.</span>
        </h1>
        
        <p class="text-sm sm:text-base text-slate-400 max-w-xl mx-auto leading-relaxed">
            Set up automatic welcome messages, custom commands, and moderation rules in minutes. We handle the hosting and server stats for you.
        </p>

    </div>
</div>

<!-- Redirection Grid Sections -->
<div class="pb-24 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Builder Link -->
        <a href="{{ route('products.builder') }}" class="cyber-card rounded-2xl p-8 flex flex-col justify-between min-h-[220px]">
            <div class="space-y-4">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center">
                    <i data-lucide="blocks" class="w-5 h-5"></i>
                </div>
                <h3 class="text-md font-bold text-white">Easy Bot Creator</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Build custom bot commands and rules visually, without writing a single line of code.
                </p>
            </div>
            <div class="text-xs text-[#5865F2] font-bold mt-6 flex items-center gap-1">
                Start Creating <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </div>
        </a>

        <!-- Hosting Link -->
        <a href="{{ route('products.hosting') }}" class="cyber-card rounded-2xl p-8 flex flex-col justify-between min-h-[220px]">
            <div class="space-y-4">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center">
                    <i data-lucide="server" class="w-5 h-5"></i>
                </div>
                <h3 class="text-md font-bold text-white">Bot Hosting</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Deploy your bot online. Free tier gets up to 20 hours of runtime per week, or upgrade to stay online 24/7.
                </p>
            </div>
            <div class="text-xs text-[#5865F2] font-bold mt-6 flex items-center gap-1">
                Explore Hosting <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </div>
        </a>

        <!-- Stats Link -->
        <a href="{{ route('products.stats') }}" class="cyber-card rounded-2xl p-8 flex flex-col justify-between min-h-[220px]">
            <div class="space-y-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                    <i data-lucide="line-chart" class="w-5 h-5"></i>
                </div>
                <h3 class="text-md font-bold text-white">Stats & Analytics</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Track server growth, messages sent, and command history with simple charts.
                </p>
            </div>
            <div class="text-xs text-[#5865F2] font-bold mt-6 flex items-center gap-1">
                View Statistics <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </div>
        </a>

    </div>
</div>
@endsection
