@extends('layouts.app')

@section('title', 'Online Bot Hosting - HublyBot')

@section('content')
<div class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
        <h1 class="text-xs font-semibold uppercase tracking-widest text-[#5865F2]">Keep Your Bot Online</h1>
        <p class="text-4xl sm:text-5xl font-extrabold text-white">Zero-Configuration Bot Hosting</p>
        <p class="text-slate-400">Keep your custom bot active 24/7 without leaving your computer running. Everything is managed behind the scenes in just one click.</p>
    </div>

    <!-- Visual Dashboard -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch mb-16">
        
        <!-- Left: Interactive Controller -->
        <div class="lg:col-span-4 bg-[#080914] border border-[#5865F2]/20 rounded-3xl p-6 flex flex-col justify-between shadow-xl">
            <div class="space-y-6">
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Bot Power Station</h4>
                    <p class="text-[10px] text-slate-500 leading-normal">Simulate turning your bot online or offline. Check the Discord channel view on the right to see the bot react immediately!</p>
                </div>

                <!-- Custom iOS-style Switch Card -->
                <div class="bg-[#05060c] border border-white/5 rounded-2xl p-5 flex flex-col items-center gap-4 text-center">
                    <span id="control-status-badge" class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Active</span>
                    
                    <button onclick="toggleBotPower()" class="cursor-pointer relative inline-flex h-10 w-20 flex-shrink-0 rounded-full border-2 border-transparent bg-emerald-500 transition-colors duration-300 ease-in-out focus:outline-none shadow-lg shadow-emerald-500/20" id="bot-power-btn">
                        <span id="bot-power-switch" class="pointer-events-none inline-block h-9 w-9 transform rounded-full bg-white shadow-md transition duration-300 ease-in-out translate-x-10"></span>
                    </button>

                    <div>
                        <h5 id="bot-power-title" class="text-sm font-bold text-white uppercase tracking-wider">HublyBot is Active</h5>
                        <p class="text-[9px] text-slate-500">Listening to server events</p>
                    </div>
                </div>

                <!-- Running Metrics gauge -->
                <div class="bg-[#05060c] border border-white/5 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-400 uppercase tracking-wide">
                        <span>Runtime Usage This Week</span>
                        <span id="quota-txt" class="font-mono text-purple-400">14 / 20 hours</span>
                    </div>
                    <div class="w-full bg-white/5 rounded-full h-2">
                        <div id="quota-bar" class="bg-gradient-to-r from-purple-500 to-[#5865F2] h-2 rounded-full transition-all duration-300" style="width: 70%"></div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-white/5">
                <a href="{{ route('pricing') }}" class="cursor-pointer w-full text-center block py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-xs font-bold uppercase text-white shadow-lg shadow-purple-900/20 transition-all duration-200">Remove 20h Limit</a>
            </div>
        </div>

        <!-- Right: Beautiful Discord Simulation Workspace -->
        <div class="lg:col-span-8 bg-[#0b0c16] border border-[#5865F2]/10 rounded-3xl overflow-hidden shadow-2xl flex flex-col min-h-[440px]">
            
            <!-- Window header -->
            <div class="bg-[#08090f] border-b border-white/5 px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex space-x-1.5">
                        <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                    </div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider ml-4">Discord Simulator</span>
                </div>
                <span class="text-[10px] text-slate-500 bg-white/5 px-2 py-0.5 rounded">#welcome-channel</span>
            </div>

            <!-- Discord grid layout -->
            <div class="flex-grow grid grid-cols-12 items-stretch h-full">
                
                <!-- Sidebar: channels list -->
                <div class="col-span-3 bg-[#07080d] border-r border-white/5 p-3 space-y-4 hidden sm:block">
                    <div class="space-y-1">
                        <p class="text-[8px] font-bold text-slate-600 uppercase tracking-widest px-2">Text Channels</p>
                        <div class="px-2 py-1.5 rounded-lg bg-white/5 text-xs text-white font-medium flex items-center gap-1.5">
                            <span class="text-slate-500">#</span> welcome-channel
                        </div>
                        <div class="px-2 py-1.5 rounded-lg text-xs text-slate-500 font-medium flex items-center gap-1.5 cursor-not-allowed">
                            <span class="text-slate-600">#</span> general-chat
                        </div>
                        <div class="px-2 py-1.5 rounded-lg text-xs text-slate-500 font-medium flex items-center gap-1.5 cursor-not-allowed">
                            <span class="text-slate-600">#</span> moderation-logs
                        </div>
                    </div>
                </div>

                <!-- Chat console area -->
                <div class="col-span-9 bg-[#0b0c16] p-4 flex flex-col justify-between h-full">
                    <!-- Message container -->
                    <div class="space-y-4 overflow-y-auto h-64" id="discord-chat-box">
                        
                        <!-- Static user message -->
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#5865F2] flex items-center justify-center text-xs font-bold text-white shrink-0">
                                JD
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-bold text-white">JohnDoe</span>
                                    <span class="text-[8px] text-slate-500">Today at 17:40</span>
                                </div>
                                <p class="text-xs text-slate-300 mt-1">Hello! I just invited our new bot helper to the server.</p>
                            </div>
                        </div>

                        <!-- Bot message, toggles off/on -->
                        <div class="flex items-start gap-3 transition-opacity duration-300" id="bot-message-row">
                            <div class="relative shrink-0">
                                <div class="w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center text-xs font-bold text-white">
                                    <i data-lucide="bot" class="w-4 h-4 text-purple-200"></i>
                                </div>
                                <div id="chat-bot-status" class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-[#0b0c16] bg-emerald-500"></div>
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-bold text-purple-400">HublyBot</span>
                                    <span class="text-[8px] bg-[#5865F2] text-white px-1.5 py-0.5 rounded font-bold uppercase tracking-wider scale-90">BOT</span>
                                    <span class="text-[8px] text-slate-500">Today at 17:41</span>
                                </div>
                                
                                <!-- Simulating Bot Welcome Embed -->
                                <div class="mt-2 border-l-4 border-purple-500 bg-[#0e101f] border border-white/5 rounded-r-xl p-3.5 max-w-sm space-y-2" id="bot-embed-card">
                                    <h5 class="text-xs font-bold text-white">Welcome, JohnDoe! 🎉</h5>
                                    <p class="text-[10px] text-slate-400">Welcome to our community. Check out #rules to get started.</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Input placeholder -->
                    <div class="mt-4 pt-3 border-t border-white/5 flex items-center gap-3">
                        <div class="w-full bg-[#07080d] border border-white/5 rounded-xl px-4 py-2 text-xs text-slate-600 flex items-center justify-between">
                            <span>Message #welcome-channel</span>
                            <i data-lucide="plus-circle" class="w-4.5 h-4.5 text-slate-600"></i>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<script>
    let isOnline = true;

    function toggleBotPower() {
        isOnline = !isOnline;
        
        const btn = document.getElementById('bot-power-btn');
        const switchDot = document.getElementById('bot-power-switch');
        const badge = document.getElementById('control-status-badge');
        const title = document.getElementById('bot-power-title');
        
        // Simulator elements
        const chatStatus = document.getElementById('chat-bot-status');
        const embed = document.getElementById('bot-embed-card');
        const messageRow = document.getElementById('bot-message-row');

        if (isOnline) {
            btn.className = "cursor-pointer relative inline-flex h-10 w-20 flex-shrink-0 rounded-full border-2 border-transparent bg-emerald-500 transition-colors duration-300 ease-in-out focus:outline-none shadow-lg shadow-emerald-500/20";
            switchDot.className = "pointer-events-none inline-block h-9 w-9 transform rounded-full bg-white shadow-md transition duration-300 ease-in-out translate-x-10";
            badge.innerText = "Active";
            badge.className = "text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20";
            title.innerText = "HublyBot is Active";

            chatStatus.className = "absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-[#0b0c16] bg-emerald-500";
            messageRow.style.opacity = "1";
        } else {
            btn.className = "cursor-pointer relative inline-flex h-10 w-20 flex-shrink-0 rounded-full border-2 border-transparent bg-rose-500 transition-colors duration-300 ease-in-out focus:outline-none shadow-lg shadow-rose-500/20";
            switchDot.className = "pointer-events-none inline-block h-9 w-9 transform rounded-full bg-white shadow-md transition duration-300 ease-in-out translate-x-0";
            badge.innerText = "Offline";
            badge.className = "text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20";
            title.innerText = "HublyBot is Stopped";

            chatStatus.className = "absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-[#0b0c16] bg-rose-500";
            messageRow.style.opacity = "0.4";
        }

        lucide.createIcons();
    }
</script>
@endsection
