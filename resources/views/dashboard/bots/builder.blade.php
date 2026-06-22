<!DOCTYPE html>
<html lang="en" class="scroll-smooth bg-[#030307]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($bot) ? 'Edit Bot - HublyBot' : 'Create Bot - HublyBot' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .cyber-grid {
            background-image: linear-gradient(rgba(88, 101, 242, 0.05) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(88, 101, 242, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            background-position: center top;
        }
        
        .cyber-card {
            background: rgba(10, 11, 22, 0.85);
            border: 1px solid rgba(88, 101, 242, 0.15);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6), 
                        inset 0 0 12px rgba(88, 101, 242, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .neon-glow-btn {
            background: linear-gradient(135deg, #5865F2 0%, #7c3aed 100%);
            box-shadow: 0 4px 14px rgba(88, 101, 242, 0.3);
            transition: all 0.2s ease;
        }
        .neon-glow-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(88, 101, 242, 0.5);
        }

        .submenu-collapse {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.25s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease-out;
        }
        .submenu-collapse.expanded {
            max-height: 500px;
            opacity: 1;
        }

        #billing-chevron, #bots-chevron {
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: center;
        }
        #billing-chevron.rotated, #bots-chevron.rotated {
            transform: rotate(180deg);
        }

        .custom-select-menu::-webkit-scrollbar,
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-select-menu::-webkit-scrollbar-track,
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(8, 9, 20, 0.5);
            border-radius: 8px;
        }
        .custom-select-menu::-webkit-scrollbar-thumb,
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(88, 101, 242, 0.3);
            border-radius: 8px;
        }
        .custom-select-menu::-webkit-scrollbar-thumb:hover,
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(88, 101, 242, 0.5);
        }

        .dragging-active .group\/node * {
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-[#030307] text-slate-100 font-sans min-h-screen flex relative overflow-x-hidden selection:bg-purple-500 selection:text-white">

    <div class="fixed top-[-20%] left-[-10%] w-[60vw] h-[60vw] rounded-full bg-purple-950/10 blur-[130px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-20%] right-[-10%] w-[60vw] h-[60vw] rounded-full bg-blue-950/10 blur-[130px] pointer-events-none z-0"></div>

    <div class="fixed inset-0 cyber-grid pointer-events-none z-0"></div>

    <button onclick="toggleSidebar()" class="fixed top-4 left-4 z-50 p-2.5 rounded-xl bg-[#090a14] border border-white/10 text-white lg:hidden cursor-pointer shadow-lg">
        <i data-lucide="menu" id="toggle-icon" class="w-5 h-5"></i>
    </button>

    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-[#080914]/90 border-r border-white/5 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col justify-between p-6 backdrop-blur-xl">
        <div class="space-y-8">
            
            <div class="flex items-center gap-2.5 pt-4 lg:pt-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <span class="text-lg font-bold tracking-tight text-white">Hubly<span class="text-[#5865F2]">Bot</span></span>
                </a>
            </div>

            <nav class="space-y-1.5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border {{ Request::is('dashboard') ? 'bg-[#5865F2]/10 border-[#5865F2]/25 text-white' : 'border-transparent text-slate-400 hover:text-white hover:bg-white/5' }} text-xs font-bold transition">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 {{ Request::is('dashboard') ? 'text-[#5865F2]' : '' }}"></i> Welcome
                </a>

                <div class="space-y-1">
                    <button onclick="toggleBotsMenu()" class="w-full flex items-center justify-between px-4 py-3 rounded-xl border border-transparent {{ Request::is('dashboard/bots*') ? 'text-white bg-white/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }} text-xs font-bold transition cursor-pointer">
                        <span class="flex items-center gap-3">
                            <i data-lucide="bot" class="w-4 h-4 {{ Request::is('dashboard/bots*') ? 'text-[#5865F2]' : '' }}"></i> My Bots
                        </span>
                        <i data-lucide="chevron-down" id="bots-chevron" class="w-4 h-4 {{ Request::is('dashboard/bots*') ? 'rotated' : '' }}"></i>
                    </button>
                    <div id="bots-submenu" class="submenu-collapse {{ Request::is('dashboard/bots*') ? 'expanded' : '' }} pl-6 space-y-1 pt-1">
                        <a href="{{ route('dashboard.bots') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition {{ Request::is('dashboard/bots') ? 'text-[#5865F2] bg-[#5865F2]/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            Overview
                        </a>
                        @if(Auth::user()->bots()->count() < Auth::user()->maxBotsLimit())
                            <a href="{{ route('dashboard.bots.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold text-emerald-400 hover:text-white hover:bg-emerald-500/10 transition flex items-center gap-1.5">
                                <i data-lucide="plus" class="w-3.5 h-3.5 text-emerald-400"></i> Create Bot
                            </a>
                        @endif
                        @foreach(Auth::user()->bots as $sidebarBot)
                            <a href="{{ route('dashboard.bots.edit', $sidebarBot) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition truncate {{ Request::is('dashboard/bots/'.$sidebarBot->id.'/edit') ? 'text-[#5865F2] bg-[#5865F2]/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sidebarBot->status === 'active' ? 'bg-emerald-500' : 'bg-slate-500' }}"></span>
                                {{ $sidebarBot->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('pricing') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-transparent text-slate-400 hover:text-white hover:bg-white/5 text-xs font-bold transition">
                    <i data-lucide="tag" class="w-4 h-4"></i> Pricing Plans
                </a>

                <div class="space-y-1">
                    <button onclick="toggleBillingMenu()" class="w-full flex items-center justify-between px-4 py-3 rounded-xl border border-transparent {{ Request::is('billing*') ? 'text-white bg-white/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }} text-xs font-bold transition cursor-pointer">
                        <span class="flex items-center gap-3">
                            <i data-lucide="credit-card" class="w-4 h-4 {{ Request::is('billing*') ? 'text-[#5865F2]' : '' }}"></i> Billing
                        </span>
                        <i data-lucide="chevron-down" id="billing-chevron" class="w-4 h-4 {{ Request::is('billing*') ? 'rotated' : '' }}"></i>
                    </button>
                    <div id="billing-submenu" class="submenu-collapse {{ Request::is('billing*') ? 'expanded' : '' }} pl-6 space-y-1 pt-1">
                        <a href="{{ route('billing.subscription') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition {{ Route::is('billing.subscription') ? 'text-[#5865F2] bg-[#5865F2]/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            Subscription
                        </a>
                        <a href="{{ route('billing.card') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition {{ Route::is('billing.card') ? 'text-[#5865F2] bg-[#5865F2]/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            Payment Methods
                        </a>
                        <a href="{{ route('billing.invoices') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition {{ Route::is('billing.invoices') ? 'text-[#5865F2] bg-[#5865F2]/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            Invoices
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <div class="space-y-4 pt-6 border-t border-white/5">
            <div class="flex items-center gap-3">
                <img src="{{ Auth::user()->avatar ?? 'https://cdn.discordapp.com/embed/avatars/0.png' }}" alt="Avatar" class="w-9 h-9 rounded-full border border-white/10 shadow-lg">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0 w-full">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl border border-rose-500/20 bg-rose-500/5 hover:bg-rose-500/10 text-rose-400 text-xs font-bold transition cursor-pointer">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 z-30 hidden transition-opacity lg:hidden"></div>

    <main class="flex-grow lg:pl-64 min-h-screen flex flex-col z-10 relative">
        <div class="flex-grow p-6 lg:p-10 max-w-[1700px] w-full mx-auto space-y-6 pt-20 lg:pt-10">

            @if(isset($bot) && $tab !== 'create')
                <div class="flex border-b border-white/5 mb-8">
                    <a href="{{ route('dashboard.bots.setup', $bot) }}" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider border-b-2 {{ $tab === 'setup' ? 'border-[#5865F2] text-white' : 'border-transparent text-slate-400 hover:text-white' }} transition flex items-center gap-2">
                        <i data-lucide="settings" class="w-4 h-4 text-[#5865F2]"></i> Bot Setup
                    </a>
                    <a href="{{ route('dashboard.bots.builder', $bot) }}" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider border-b-2 {{ $tab === 'builder' ? 'border-purple-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }} transition flex items-center gap-2">
                        <i data-lucide="git-branch" class="w-4 h-4 text-purple-400"></i> Logic Builder
                    </a>
                    <a href="{{ route('dashboard.bots.hosting', $bot) }}" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider border-b-2 {{ $tab === 'hosting' ? 'border-emerald-500 text-white' : 'border-transparent text-slate-400 hover:text-white' }} transition flex items-center gap-2">
                        <i data-lucide="server" class="w-4 h-4 text-emerald-400"></i> Hosting & Logs
                    </a>
                </div>
            @endif

            <form id="bot-builder-form" action="{{ isset($bot) ? route('dashboard.bots.update', $bot) : route('dashboard.bots.store') }}" method="POST" class="space-y-8">
                @csrf
                <input type="hidden" name="settings" id="settings-input" value="[]">
                <input type="hidden" name="current_tab" value="{{ $tab }}">

                @if($tab === 'builder')
                    <input type="hidden" name="name" value="{{ $bot->name }}">
                    <input type="hidden" name="token" value="{{ $bot->token }}">
                    <input type="hidden" name="status" value="{{ $bot->status }}">
                @endif

                @if($tab === 'setup' || $tab === 'create')
                    @php
                        $needsMembers = false;
                        $needsMessageContent = false;
                        if (isset($bot) && is_array($bot->settings) && isset($bot->settings['tree'])) {
                            $flatten = function($nodes) use (&$flatten, &$needsMembers, &$needsMessageContent) {
                                foreach ($nodes as $node) {
                                    if ($node['type'] === 'file' && isset($node['logic'])) {
                                        foreach ($node['logic'] as $block) {
                                            if ($block['type'] === 'trigger') {
                                                if (in_array($block['event'], ['join', 'leave'])) $needsMembers = true;
                                                if ($block['event'] === 'message') $needsMessageContent = true;
                                            }
                                        }
                                    } elseif ($node['type'] === 'folder' && isset($node['children'])) {
                                        $flatten($node['children']);
                                    }
                                }
                            };
                            $flatten($bot->settings['tree']);
                        }
                    @endphp
                    <div id="tab-view-setup" class="flex flex-col lg:flex-row gap-8 items-start w-full">
                        
                        <div class="w-full lg:w-[40%] space-y-6 shrink-0 relative z-20">
                            
                            @if($needsMembers || $needsMessageContent)
                                <div class="cyber-card border-amber-500/25 bg-amber-500/5 rounded-2xl p-5 space-y-3 flex items-start gap-3">
                                    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-400 shrink-0 mt-0.5 animate-pulse"></i>
                                    <div class="space-y-1">
                                        <h4 class="text-xs font-bold text-amber-400 uppercase tracking-wider">Intents Required</h4>
                                        <p class="text-[10px] text-slate-400 leading-relaxed">
                                            Your logic builder configuration currently uses triggers requiring developer portal settings:
                                            @if($needsMembers) <span class="text-amber-300 font-semibold font-mono">Server Members</span> (join/leave events) @endif
                                            @if($needsMessageContent) @if($needsMembers) and @endif <span class="text-amber-300 font-semibold font-mono">Message Content</span> (message/command actions) @endif.
                                            Enable these in the <a href="https://discord.com/developers/applications" target="_blank" class="text-blue-400 hover:underline">Discord Portal</a>.
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <div class="cyber-card rounded-2xl p-6 space-y-5">
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                                    <i data-lucide="settings" class="w-4 h-4 text-[#5865F2]"></i> Bot Details
                                </h3>
                                
                                <div class="space-y-4">
                                    
                                    <div class="space-y-1.5">
                                        <label for="bot-name" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bot Name</label>
                                        <input type="text" id="bot-name" name="name" required placeholder="My Awesome Bot" value="{{ old('name', $bot->name ?? '') }}" class="w-full bg-slate-950/50 border border-white/10 focus:border-[#5865F2]/50 text-white rounded-xl px-4 py-3 text-xs outline-none transition">
                                    </div>

                                    <div class="space-y-1.5">
                                        <div class="flex justify-between items-center w-full">
                                            <label for="bot-token" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                                                Discord Bot Token
                                                <span class="group relative cursor-pointer text-slate-500 hover:text-white">
                                                    <i data-lucide="help-circle" class="w-3.5 h-3.5"></i>
                                                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-2 rounded-lg bg-black text-[9px] leading-relaxed text-slate-300 font-normal opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity">
                                                        Paste your Discord Bot Token from the developer portal to connect it.
                                                    </span>
                                                </span>
                                            </label>
                                            <button type="button" onclick="openTutorialModal()" class="text-[#5865F2] hover:text-indigo-400 hover:underline text-[9px] font-bold uppercase tracking-wider cursor-pointer transition">How to create & invite bot?</button>
                                        </div>
                                        <input type="password" id="bot-token" name="token" placeholder="MTIzaDk..." value="{{ old('token', $bot->token ?? '') }}" class="w-full bg-slate-950/50 border border-white/10 focus:border-[#5865F2]/50 text-white rounded-xl px-4 py-3 text-xs outline-none transition font-mono">
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</label>
                                        <div class="relative custom-select" id="select-bot-status">
                                            @php
                                                $currentStatus = old('status', $bot->status ?? 'inactive');
                                                $statusLabel = $currentStatus === 'active' ? 'Active / Online' : 'Inactive / Offline';
                                            @endphp
                                            <input type="hidden" name="status" id="bot-status" value="{{ $currentStatus }}">
                                            <button type="button" onclick="toggleCustomSelect('bot-status')" class="w-full bg-slate-950/50 border border-white/10 text-white rounded-xl px-4 py-3 text-xs flex justify-between items-center cursor-pointer outline-none transition focus:border-[#5865F2]/50">
                                                <span id="label-bot-status">{{ $statusLabel }}</span>
                                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                                            </button>
                                            <div id="menu-bot-status" class="custom-select-menu hidden absolute left-0 right-0 mt-1 border border-white/15 rounded-xl py-1 z-[999] shadow-2xl" style="background-color: #080914 !important; opacity: 1 !important;">
                                                <div onclick="selectBotStatus('inactive')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left">Inactive / Offline</div>
                                                <div onclick="selectBotStatus('active')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left">Active / Online</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full lg:w-[60%] space-y-6 flex-grow relative z-10">
                            <div class="cyber-card rounded-2xl p-6 space-y-5">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                                        <i data-lucide="variable" class="w-4 h-4 text-purple-400"></i> Custom Variables
                                    </h3>
                                    <button type="button" onclick="addVariable()" class="px-3 py-1.5 rounded-lg border border-purple-500/30 bg-purple-500/10 hover:bg-purple-500/20 text-[10px] font-bold text-purple-300 transition flex items-center gap-1 cursor-pointer">
                                        <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Variable
                                    </button>
                                </div>
                                
                                <p class="text-[11px] text-slate-400 leading-relaxed">
                                    Define custom constants or settings for your bot logic. Use them in message replies as <code class="text-purple-400 font-mono bg-purple-950/40 px-1 py-0.5 rounded">{var.KEY}</code>.
                                </p>

                                <div class="space-y-3" id="variables-list">
                                    
                                </div>

                                <div id="variables-empty-state" class="hidden flex-col items-center justify-center py-8 text-center space-y-3">
                                    <div class="w-10 h-10 rounded-lg bg-slate-950/60 border border-white/5 flex items-center justify-center text-slate-600">
                                        <i data-lucide="variable" class="w-5 h-5"></i>
                                    </div>
                                    <p class="text-[10px] text-slate-500">No custom variables defined yet.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($tab === 'builder')
                    <div id="tab-view-builder" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start w-full">
                        
                        <div class="w-full md:col-span-1 order-1 md:order-2 space-y-6 relative z-20">
                            <div class="cyber-card rounded-2xl p-5 space-y-4">
                                <div class="flex justify-between items-center border-b border-white/5 pb-3">
                                    <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                                        <i data-lucide="folder-tree" class="w-4 h-4 text-purple-400"></i> Explorer
                                    </h3>
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" onclick="promptCreateNode(null, 'file')" title="New File" class="p-1.5 rounded bg-white/5 border border-white/10 hover:bg-white/10 text-slate-300 hover:text-white transition cursor-pointer">
                                            <i data-lucide="file-plus" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button type="button" onclick="promptCreateNode(null, 'folder')" title="New Folder" class="p-1.5 rounded bg-white/5 border border-white/10 hover:bg-white/10 text-slate-300 hover:text-white transition cursor-pointer">
                                            <i data-lucide="folder-plus" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="max-h-[350px] overflow-y-auto pr-1 space-y-1 text-xs select-none" id="tree-container" ondragover="onRootDragOver(event)" ondrop="onRootDrop(event)">
                                    
                                </div>
                            </div>

                            <div class="cyber-card rounded-2xl p-5 space-y-4">
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                                    <i data-lucide="help-circle" class="w-4 h-4 text-blue-400"></i> Placeholders Cheat Sheet
                                </h3>
                                <div class="text-[11px] text-slate-400 space-y-2.5 leading-relaxed">
                                    <p>Use these keywords in message content actions to output dynamic information:</p>
                                    <div class="relative">
                                        <input type="text" placeholder="Search placeholders..." oninput="filterPlaceholders(this)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2.5 py-1.5 text-xs outline-none focus:border-blue-500/50">
                                    </div>
                                    <div class="border border-white/5 rounded-xl overflow-hidden bg-slate-950/40 max-h-[250px] overflow-y-auto custom-scrollbar">
                                        <table class="w-full text-left border-collapse">
                                            <thead>
                                                <tr class="border-b border-white/10 bg-[#0a0b16] sticky top-0 z-10">
                                                    <th class="p-2 font-bold text-[9px] text-slate-300 uppercase">Placeholder</th>
                                                    <th class="p-2 font-bold text-[9px] text-slate-300 uppercase">Description</th>
                                                </tr>
                                            </thead>
                                            <tbody id="placeholder-table-body" class="divide-y divide-white/5 text-[10px]">
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{user}</td>
                                                    <td class="p-2 text-slate-300">Mentions the target user (e.g. <span class="text-slate-400">@Arthur</span>)</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{username}</td>
                                                    <td class="p-2 text-slate-300">Target user's plain text name</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{user.id}</td>
                                                    <td class="p-2 text-slate-300">Discord User ID (numeric string)</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{user.avatar}</td>
                                                    <td class="p-2 text-slate-300">Avatar image URL of the user</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{server}</td>
                                                    <td class="p-2 text-slate-300">The Discord server name</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{server.id}</td>
                                                    <td class="p-2 text-slate-300">Server ID (numeric string)</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{server.member_count}</td>
                                                    <td class="p-2 text-slate-300">Total members on this server</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{channel}</td>
                                                    <td class="p-2 text-slate-300">The current channel name</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{channel.id}</td>
                                                    <td class="p-2 text-slate-300">Channel ID (numeric string)</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{message.content}</td>
                                                    <td class="p-2 text-slate-300">Full raw text of the triggering message</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{date} / {time}</td>
                                                    <td class="p-2 text-slate-300">Current date and time of execution</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{message.url}</td>
                                                    <td class="p-2 text-slate-300">Direct URL link to the message</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{message.id}</td>
                                                    <td class="p-2 text-slate-300">Triggering message ID</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{channel.mention}</td>
                                                    <td class="p-2 text-slate-300">Mention of the channel (clickable)</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{user.tag}</td>
                                                    <td class="p-2 text-slate-300">Full user tag/name</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{arg.NAME}</td>
                                                    <td class="p-2 text-slate-300">Command argument value (e.g. <span class="text-purple-400">{arg.reason}</span>)</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{var.NAME}</td>
                                                    <td class="p-2 text-slate-300">Custom variable (e.g. <span class="text-purple-400">{var.prefix}</span>)</td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2 font-mono text-[#5865F2]">{same}</td>
                                                    <td class="p-2 text-slate-300">Targets the channel where the trigger/action occurred</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full md:col-span-3 order-2 md:order-1 space-y-6 relative z-10">
                            <div class="cyber-card rounded-2xl p-6 relative flex flex-col justify-between gap-4">
                                <div class="relative z-10 flex justify-between items-center border-b border-white/5 pb-4">
                                    <span class="text-xs font-bold text-white uppercase tracking-widest flex items-center gap-2" id="active-file-title">
                                        <i data-lucide="file-code" class="w-4 h-4 text-[#5865F2]"></i> Select a file
                                    </span>
                                    <span class="text-[10px] text-slate-500" id="file-triggers-count">0 Blocks</span>
                                </div>

                                <div id="canvas-viewport" class="relative w-full h-[550px] bg-[#05060c] border border-white/10 rounded-2xl overflow-hidden cursor-grab active:cursor-grabbing" onmousedown="onCanvasMouseDown(event)">
                                    
                                    <div id="canvas-content" class="absolute origin-top-left" style="transform: translate(0px, 0px) scale(1); width: 3000px; height: 3000px;">
                                        
                                        <div class="absolute inset-0 cyber-grid opacity-[0.08] pointer-events-none"></div>

                                        <svg class="absolute inset-0 pointer-events-none w-full h-full" id="canvas-svg" style="min-width: 3000px; min-height: 3000px;">
                                            <defs>
                                                
                                                <marker id="arrow" viewBox="0 0 10 10" refX="2" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                                    <path d="M 0 1.5 L 10 5 L 0 8.5 z" fill="#5865F2" />
                                                </marker>
                                            </defs>
                                            
                                            <path id="temp-connection-path" d="" stroke="#5865F2" stroke-width="2" stroke-dasharray="4" fill="none" class="hidden" />
                                        </svg>

                                        <div id="triggers-container" class="absolute inset-0 w-full h-full">
                                            
                                        </div>
                                    </div>

                                    <div class="absolute bottom-4 right-4 z-30 flex items-center gap-1 bg-slate-950/80 border border-white/10 rounded-xl p-1.5 shadow-2xl backdrop-blur-md">
                                        <button type="button" onclick="zoomCanvas(0.1)" title="Zoom In" class="p-1.5 rounded-lg bg-white/5 border border-white/5 hover:bg-white/10 text-slate-300 hover:text-white transition cursor-pointer">
                                            <i data-lucide="zoom-in" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button type="button" onclick="zoomCanvas(-0.1)" title="Zoom Out" class="p-1.5 rounded-lg bg-white/5 border border-white/5 hover:bg-white/10 text-slate-300 hover:text-white transition cursor-pointer">
                                            <i data-lucide="zoom-out" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button type="button" onclick="resetCanvasTransform()" title="Reset View" class="p-1.5 rounded-lg bg-white/5 border border-white/5 hover:bg-white/10 text-slate-300 hover:text-white transition cursor-pointer">
                                            <i data-lucide="maximize" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>

                                    <div id="canvas-empty-state" class="absolute inset-0 flex flex-col items-center justify-center py-20 text-center space-y-4 z-0 pointer-events-none">
                                        <div class="w-12 h-12 rounded-xl bg-slate-950/60 border border-white/10 flex items-center justify-center text-slate-500" id="empty-state-icon">
                                            <i data-lucide="mouse-pointer-click" class="w-6 h-6 animate-pulse"></i>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-xs font-bold text-white uppercase tracking-wider" id="empty-state-title">No File Selected</p>
                                            <p class="text-[10px] text-slate-500 max-w-xs mx-auto" id="empty-state-desc">Select a file from the explorer sidebar, or create a new file to start building your bot logic flows.</p>
                                        </div>
                                    </div>
                                </div>

                                <div id="blocks-toolbox" class="hidden border border-white/5 bg-[#090b16]/40 p-4 rounded-xl flex items-center gap-4 relative z-40">
                                    
                                    <div class="relative custom-select" id="select-add-event">
                                        <button type="button" onclick="toggleCustomSelect('add-event')" class="px-4 py-2.5 rounded-xl border border-purple-500/20 bg-purple-500/5 hover:bg-purple-500/10 text-xs font-bold text-purple-300 transition flex items-center gap-1.5 cursor-pointer">
                                            <i data-lucide="zap" class="w-3.5 h-3.5 text-purple-400"></i> Events <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-purple-400"></i>
                                        </button>
                                        <div id="menu-add-event" class="custom-select-menu hidden absolute bottom-full left-0 mb-2 border border-white/15 rounded-xl pt-0 pb-1 z-[9999] shadow-2xl w-[220px] max-h-[300px] overflow-y-auto" style="background-color: #080914 !important; opacity: 1 !important;">
                                            <div class="p-2 border-b border-white/10 sticky top-0 bg-[#080914] z-50 rounded-t-xl">
                                                <input type="text" placeholder="Search events..." oninput="filterMenu(this, 'menu-add-event')" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2.5 py-1 text-xs outline-none focus:border-purple-500/50">
                                            </div>
                                            <div onclick="addNewBlock('trigger', 'message')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="message-square" class="w-3.5 h-3.5 text-purple-400"></i> Message Keywords</div>
                                            <div onclick="addNewBlock('trigger', 'command')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="terminal" class="w-3.5 h-3.5 text-purple-400"></i> Slash Command</div>
                                            <div onclick="addNewBlock('trigger', 'join')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="user-plus" class="w-3.5 h-3.5 text-purple-400"></i> Member Joins</div>
                                            <div onclick="addNewBlock('trigger', 'leave')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="user-minus" class="w-3.5 h-3.5 text-purple-400"></i> Member Leaves</div>
                                            <div onclick="addNewBlock('trigger', 'reaction')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="smile" class="w-3.5 h-3.5 text-purple-400"></i> Message Reaction</div>
                                            <div onclick="addNewBlock('trigger', 'channel_create')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="folder-plus" class="w-3.5 h-3.5 text-purple-400"></i> Channel Created</div>
                                            <div onclick="addNewBlock('trigger', 'role_create')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="shield-plus" class="w-3.5 h-3.5 text-purple-400"></i> Role Created</div>
                                            <div onclick="addNewBlock('trigger', 'message_edit')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="edit" class="w-3.5 h-3.5 text-purple-400"></i> Message Edited</div>
                                            <div onclick="addNewBlock('trigger', 'message_delete')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="trash" class="w-3.5 h-3.5 text-purple-400"></i> Message Deleted</div>
                                            <div onclick="addNewBlock('trigger', 'member_ban')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="user-x" class="w-3.5 h-3.5 text-purple-400"></i> Member Banned</div>
                                            <div onclick="addNewBlock('trigger', 'member_unban')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="user-check" class="w-3.5 h-3.5 text-purple-400"></i> Member Unbanned</div>
                                            <div onclick="addNewBlock('trigger', 'voice_update')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="volume-2" class="w-3.5 h-3.5 text-purple-400"></i> Voice Channel Update</div>
                                            <div onclick="addNewBlock('trigger', 'invite_update')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="link" class="w-3.5 h-3.5 text-purple-400"></i> Invite Created/Deleted</div>
                                            <div onclick="addNewBlock('trigger', 'thread_update')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="git-pull-request" class="w-3.5 h-3.5 text-purple-400"></i> Thread Created/Deleted</div>
                                            <div onclick="addNewBlock('trigger', 'member_update')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="user-cog" class="w-3.5 h-3.5 text-purple-400"></i> Member Profile Update</div>
                                            <div onclick="addNewBlock('trigger', 'emoji_update')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="smile-plus" class="w-3.5 h-3.5 text-purple-400"></i> Emoji/Sticker Update</div>
                                            <div onclick="addNewBlock('trigger', 'auto_moderation_exec')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="shield-alert" class="w-3.5 h-3.5 text-purple-400"></i> AutoMod Rule Triggered</div>
                                            <div onclick="addNewBlock('trigger', 'audit_log_create')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="file-text" class="w-3.5 h-3.5 text-purple-400"></i> Audit Log Entry Created</div>
                                            <div onclick="addNewBlock('trigger', 'scheduled_event_update')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="calendar" class="w-3.5 h-3.5 text-purple-400"></i> Scheduled Event State</div>
                                            <div onclick="addNewBlock('trigger', 'boost_event')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="gem" class="w-3.5 h-3.5 text-purple-400"></i> Server Boosted</div>
                                            <div onclick="addNewBlock('trigger', 'poll_vote')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="check-square" class="w-3.5 h-3.5 text-purple-400"></i> Member Votes Poll</div>
                                            <div onclick="addNewBlock('trigger', 'stage_instance_update')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="mic" class="w-3.5 h-3.5 text-purple-400"></i> Stage Channel Live</div>
                                            <div onclick="addNewBlock('trigger', 'ready')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="play-circle" class="w-3.5 h-3.5 text-purple-400"></i> Bot Started / Ready</div>
                                            <div onclick="addNewBlock('trigger', 'typing_start')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="keyboard" class="w-3.5 h-3.5 text-purple-400"></i> User Typing Started</div>
                                        </div>
                                    </div>

                                    <div class="relative custom-select" id="select-add-condition">
                                        <button type="button" onclick="toggleCustomSelect('add-condition')" class="px-4 py-2.5 rounded-xl border border-amber-500/20 bg-amber-500/5 hover:bg-amber-500/10 text-xs font-bold text-amber-300 transition flex items-center gap-1.5 cursor-pointer">
                                            <i data-lucide="git-commit" class="w-3.5 h-3.5 text-amber-400"></i> Conditions <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-amber-400"></i>
                                        </button>
                                        <div id="menu-add-condition" class="custom-select-menu hidden absolute bottom-full left-0 mb-2 border border-white/15 rounded-xl pt-0 pb-1 z-[9999] shadow-2xl w-[220px] max-h-[300px] overflow-y-auto" style="background-color: #080914 !important; opacity: 1 !important;">
                                            <div class="p-2 border-b border-white/10 sticky top-0 bg-[#080914] z-50 rounded-t-xl">
                                                <input type="text" placeholder="Search conditions..." oninput="filterMenu(this, 'menu-add-condition')" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2.5 py-1 text-xs outline-none focus:border-purple-500/50">
                                            </div>
                                            <div onclick="addNewBlock('condition', 'cond_has_role')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="shield-check" class="w-3.5 h-3.5 text-amber-400"></i> User Has Role</div>
                                            <div onclick="addNewBlock('condition', 'cond_channel_name')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="hash" class="w-3.5 h-3.5 text-amber-400"></i> Channel Name Matches</div>
                                            <div onclick="addNewBlock('condition', 'cond_random')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="dices" class="w-3.5 h-3.5 text-amber-400"></i> Random Chance %</div>
                                            <div onclick="addNewBlock('condition', 'cond_is_bot')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="bot" class="w-3.5 h-3.5 text-amber-400"></i> Actor is Bot</div>
                                            <div onclick="addNewBlock('condition', 'cond_nsfw')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="alert-octagon" class="w-3.5 h-3.5 text-amber-400"></i> Channel is NSFW</div>
                                            <div onclick="addNewBlock('condition', 'cond_has_link')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="external-link" class="w-3.5 h-3.5 text-amber-400"></i> Message Has Link</div>
                                            <div onclick="addNewBlock('condition', 'cond_has_attachments')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="paperclip" class="w-3.5 h-3.5 text-amber-400"></i> Message Has Attachments</div>
                                            <div onclick="addNewBlock('condition', 'cond_user_perm')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="key" class="w-3.5 h-3.5 text-amber-400"></i> User Has Permission</div>
                                            <div onclick="addNewBlock('condition', 'cond_msg_len')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="ruler" class="w-3.5 h-3.5 text-amber-400"></i> Message Length Check</div>
                                            <div onclick="addNewBlock('condition', 'cond_time_range')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="clock" class="w-3.5 h-3.5 text-amber-400"></i> Time/Schedule Range</div>
                                            <div onclick="addNewBlock('condition', 'cond_user_id')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="user" class="w-3.5 h-3.5 text-amber-400"></i> Specific User ID</div>
                                            <div onclick="addNewBlock('condition', 'cond_channel_type')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="columns" class="w-3.5 h-3.5 text-amber-400"></i> Channel Type</div>
                                            <div onclick="addNewBlock('condition', 'cond_boost_tier')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="gem" class="w-3.5 h-3.5 text-amber-400"></i> Server Boost Level</div>
                                            <div onclick="addNewBlock('condition', 'cond_bot_perm')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="shield-check" class="w-3.5 h-3.5 text-amber-400"></i> Bot Permissions</div>
                                        </div>
                                    </div>

                                    <div class="relative custom-select" id="select-add-action">
                                        <button type="button" onclick="toggleCustomSelect('add-action')" class="px-4 py-2.5 rounded-xl border border-blue-500/20 bg-blue-500/5 hover:bg-blue-500/10 text-xs font-bold text-[#5865F2] transition flex items-center gap-1.5 cursor-pointer">
                                            <i data-lucide="play" class="w-3.5 h-3.5 text-[#5865F2]"></i> Actions <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-[#5865F2]"></i>
                                        </button>
                                        <div id="menu-add-action" class="custom-select-menu hidden absolute bottom-full left-0 mb-2 border border-white/15 rounded-xl pt-0 pb-1 z-[9999] shadow-2xl w-[220px] max-h-[300px] overflow-y-auto" style="background-color: #080914 !important; opacity: 1 !important;">
                                            <div class="p-2 border-b border-white/10 sticky top-0 bg-[#080914] z-50 rounded-t-xl">
                                                <input type="text" placeholder="Search actions..." oninput="filterMenu(this, 'menu-add-action')" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2.5 py-1 text-xs outline-none focus:border-purple-500/50">
                                            </div>
                                            <div onclick="addNewBlock('action', 'reply')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="message-square" class="w-3.5 h-3.5 text-blue-400"></i> Send In Channel</div>
                                            <div onclick="addNewBlock('action', 'reply_direct')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="reply" class="w-3.5 h-3.5 text-blue-400"></i> Reply to Message / Command</div>
                                            <div onclick="addNewBlock('action', 'send_console')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="terminal" class="w-3.5 h-3.5 text-blue-400"></i> Send to Console</div>
                                            <div onclick="addNewBlock('action', 'dm')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="mail" class="w-3.5 h-3.5 text-blue-400"></i> Send Direct Message (DM)</div>
                                            <div onclick="addNewBlock('action', 'role')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="shield" class="w-3.5 h-3.5 text-blue-400"></i> Assign Role</div>
                                            <div onclick="addNewBlock('action', 'remove_role')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="shield-alert" class="w-3.5 h-3.5 text-blue-400"></i> Remove Role</div>
                                            <div onclick="addNewBlock('action', 'create_channel')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="plus-circle" class="w-3.5 h-3.5 text-blue-400"></i> Create Channel</div>
                                            <div onclick="addNewBlock('action', 'delete_channel')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="minus-circle" class="w-3.5 h-3.5 text-blue-400"></i> Delete Channel</div>
                                            <div onclick="addNewBlock('action', 'kick_member')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="user-minus" class="w-3.5 h-3.5 text-blue-400"></i> Kick Member</div>
                                            <div onclick="addNewBlock('action', 'ban_member')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="user-x" class="w-3.5 h-3.5 text-blue-400"></i> Ban Member</div>
                                            <div onclick="addNewBlock('action', 'timeout_member')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="timer" class="w-3.5 h-3.5 text-blue-400"></i> Timeout Member</div>
                                            <div onclick="addNewBlock('action', 'add_reaction')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="smile" class="w-3.5 h-3.5 text-blue-400"></i> Add Message Reaction</div>
                                            <div onclick="addNewBlock('action', 'pin_message')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="pin" class="w-3.5 h-3.5 text-blue-400"></i> Pin Message</div>
                                            <div onclick="addNewBlock('action', 'unpin_message')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="pin-off" class="w-3.5 h-3.5 text-blue-400"></i> Unpin Message</div>
                                            <div onclick="addNewBlock('action', 'create_thread')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="message-square-plus" class="w-3.5 h-3.5 text-blue-400"></i> Create Thread</div>
                                            <div onclick="addNewBlock('action', 'send_webhook')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="webhook" class="w-3.5 h-3.5 text-blue-400"></i> Send Webhook / API</div>
                                            <div onclick="addNewBlock('action', 'wait_delay')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="hourglass" class="w-3.5 h-3.5 text-blue-400"></i> Wait / Delay Flow</div>
                                            <div onclick="addNewBlock('action', 'send_embed')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="layout-template" class="w-3.5 h-3.5 text-blue-400"></i> Send Rich Embed</div>
                                            <div onclick="addNewBlock('action', 'set_nickname')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="user-circle" class="w-3.5 h-3.5 text-blue-400"></i> Set Nickname</div>
                                            <div onclick="addNewBlock('action', 'lock_channel')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="lock" class="w-3.5 h-3.5 text-blue-400"></i> Lock Channel</div>
                                            <div onclick="addNewBlock('action', 'unlock_channel')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="unlock" class="w-3.5 h-3.5 text-blue-400"></i> Unlock Channel</div>
                                            <div onclick="addNewBlock('action', 'delete_message')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="trash-2" class="w-3.5 h-3.5 text-blue-400"></i> Delete Message</div>
                                            <div onclick="addNewBlock('action', 'create_event')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="calendar-plus" class="w-3.5 h-3.5 text-blue-400"></i> Create Scheduled Event</div>
                                            <div onclick="addNewBlock('action', 'stage_start')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="mic-2" class="w-3.5 h-3.5 text-blue-400"></i> Start Stage Instance</div>
                                            <div onclick="addNewBlock('action', 'create_automod_rule')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="shield-alert" class="w-3.5 h-3.5 text-blue-400"></i> Create AutoMod Rule</div>
                                            <div onclick="addNewBlock('action', 'send_poll')" class="px-4 py-2.5 text-xs hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left flex items-center gap-2"><i data-lucide="bar-chart-3" class="w-3.5 h-3.5 text-blue-400"></i> Send Message Poll</div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($tab === 'hosting')
                    @php
                        $pidFile = storage_path("app/bot_{$bot->id}.pid");
                        $isRunning = false;
                        $pidValue = 'N/A';
                        $uptimeSeconds = 0;
                        if (File::exists($pidFile)) {
                            $pid = (int) File::get($pidFile);
                            if (substr(php_uname(), 0, 7) == "Windows") {
                                $output = [];
                                exec("tasklist /FI \"PID eq {$pid}\"", $output);
                                $isRunning = count($output) > 3;
                            } else {
                                if (function_exists('posix_kill')) {
                                    $isRunning = posix_kill($pid, 0);
                                } else {
                                    $isRunning = file_exists("/proc/{$pid}");
                                }
                            }
                            if ($isRunning) {
                                $pidValue = "#hb-{$pid}";
                                $uptimeSeconds = time() - File::lastModified($pidFile);
                            }
                        }
                    @endphp
                    <div id="tab-view-hosting" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start w-full">
                        
                        <div class="space-y-6">
                            
                            <div class="cyber-card rounded-2xl p-6 space-y-6 relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl pointer-events-none"></div>
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                                    <i data-lucide="power" class="w-4 h-4 text-emerald-400"></i> Bot Control Panel
                                </h3>

                                <div class="bg-slate-950/60 border border-white/5 rounded-2xl p-5 flex items-center justify-between">
                                    <div class="space-y-1">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Current Status</span>
                                        <div class="flex items-center gap-2">
                                            <span id="hosting-status-ping" class="relative flex h-2 w-2">
                                                @if($isRunning)
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                                @else
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                                @endif
                                            </span>
                                            <span id="hosting-status-text" class="text-xs font-bold {{ $isRunning ? 'text-emerald-400' : 'text-rose-500' }} tracking-wide uppercase">
                                                {{ $isRunning ? 'Online / Running' : 'Offline / Stopped' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right space-y-1">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Process ID</span>
                                        <p id="hosting-pid" class="text-xs font-mono font-bold text-slate-300">{{ $pidValue }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <button type="button" id="btn-start" onclick="controlBot('start')" {{ $isRunning ? 'disabled' : '' }} class="py-3 px-4 rounded-xl border border-emerald-500/20 bg-emerald-500/5 hover:bg-emerald-500/10 text-emerald-400 text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                                        <i data-lucide="play" class="w-3.5 h-3.5"></i> Start
                                    </button>
                                    <button type="button" id="btn-stop" onclick="controlBot('stop')" {{ !$isRunning ? 'disabled' : '' }} class="py-3 px-4 rounded-xl border border-rose-500/20 bg-rose-500/5 hover:bg-rose-500/10 text-rose-400 text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                                        <i data-lucide="square" class="w-3.5 h-3.5"></i> Stop
                                    </button>
                                    <button type="button" id="btn-restart" onclick="controlBot('restart')" {{ !$isRunning ? 'disabled' : '' }} class="py-3 px-4 rounded-xl border border-amber-500/20 bg-amber-500/5 hover:bg-amber-500/10 text-amber-400 text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Restart
                                    </button>
                                </div>
                            </div>

                            <div class="cyber-card rounded-2xl p-6 space-y-6">
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                                    <i data-lucide="activity" class="w-4 h-4 text-blue-400"></i> Resource Telemetry
                                </h3>

                                <div class="space-y-4">
                                    
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center text-xs font-bold">
                                            <span class="text-slate-400">CPU Allocation</span>
                                            <span id="metric-cpu" class="text-slate-300 font-mono">0.00 / {{ Auth::user()->activePlan() !== null ? '1.00' : '0.10' }} vCPU (0%)</span>
                                        </div>
                                        <div class="w-full bg-slate-950/70 border border-white/5 rounded-full h-2 overflow-hidden">
                                            <div id="bar-cpu" class="bg-gradient-to-r from-blue-500 to-indigo-500 h-full rounded-full transition-all duration-500" style="width: 0%"></div>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center text-xs font-bold">
                                            <span class="text-slate-400">Memory Usage</span>
                                            <span id="metric-ram" class="text-slate-300 font-mono">0.0 MB / {{ Auth::user()->activePlan() !== null ? '1024' : '128' }} MB (0%)</span>
                                        </div>
                                        <div class="w-full bg-slate-950/70 border border-white/5 rounded-full h-2 overflow-hidden">
                                            <div id="bar-ram" class="bg-gradient-to-r from-purple-500 to-indigo-500 h-full rounded-full transition-all duration-500" style="width: 0%"></div>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center bg-slate-950/40 border border-white/5 rounded-xl p-3.5">
                                        <span class="text-xs font-bold text-slate-400">System Uptime</span>
                                        <span id="metric-uptime" class="text-xs font-bold text-slate-300 font-mono">0d 0h 0m 0s</span>
                                    </div>

                                    @if(Auth::user()->activePlan() === null)
                                    
                                    <div id="free-session-container" class="space-y-2 border-t border-white/5 pt-4">
                                        <div class="flex justify-between items-center text-xs font-bold">
                                            <span class="text-slate-400 flex items-center gap-1">
                                                <i data-lucide="timer" class="w-3.5 h-3.5 text-amber-400"></i> Free Session Left
                                            </span>
                                            <span id="free-session-time" class="text-amber-400 font-mono">02:00:00</span>
                                        </div>
                                        <div class="w-full bg-slate-950/70 border border-white/5 rounded-full h-2 overflow-hidden">
                                            <div id="bar-free-session" class="h-full rounded-full transition-all duration-1000" style="width: 100%; background: linear-gradient(to right, #f59e0b, #ea580c);"></div>
                                        </div>
                                        <p class="text-[9px] text-slate-500 leading-normal">
                                            Bots on the Free Tier have a 2-hour session limit. Upgrading lifts this limit.
                                        </p>
                                    </div>
                                    @endif
                                </div>

                                @if(Auth::user()->activePlan() === null)
                                
                                <div onclick="showPremiumGate('Dedicated Hosting', 'Upgrade your plan to unlock dedicated nodes, unlimited memory, 24/7 background worker persistence, and priority CPU execution.')" class="bg-gradient-to-r from-purple-950/20 to-blue-950/20 border border-purple-500/15 rounded-xl p-3.5 flex items-start gap-3 cursor-pointer hover:border-purple-500/30 transition group">
                                    <i data-lucide="crown" class="w-4 h-4 text-purple-400 shrink-0 mt-0.5 group-hover:scale-110 transition-transform"></i>
                                    <div class="space-y-1">
                                        <p class="text-[10px] font-bold text-white flex items-center gap-1">Upgrade to Premium <i data-lucide="arrow-right" class="w-3 h-3 text-purple-400"></i></p>
                                        <p class="text-[9px] leading-relaxed text-slate-400">Unlock dedicated micro-VMs, 1024MB RAM, and guaranteed 99.9% uptime SLA.</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="lg:col-span-2 space-y-6">
                            <div class="cyber-card rounded-2xl overflow-hidden border border-white/10 flex flex-col h-[525px]">
                                
                                <div class="bg-[#080914] border-b border-white/5 px-5 py-4 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="flex gap-1.5">
                                            <span class="w-3 h-3 rounded-full bg-rose-500/80"></span>
                                            <span class="w-3 h-3 rounded-full bg-amber-500/80"></span>
                                            <span class="w-3 h-3 rounded-full bg-emerald-500/80"></span>
                                        </div>
                                        <span class="text-xs font-bold text-slate-400 ml-2 font-mono flex items-center gap-1">
                                            <i data-lucide="terminal" class="w-3.5 h-3.5 text-slate-500"></i> console.log
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="clearConsole()" class="px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/5 hover:bg-white/10 text-[10px] font-bold text-slate-400 hover:text-white transition flex items-center gap-1 cursor-pointer">
                                            <i data-lucide="trash-2" class="w-3 h-3"></i> Clear
                                        </button>
                                        <button type="button" onclick="downloadLogs()" class="px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/5 hover:bg-white/10 text-[10px] font-bold text-slate-400 hover:text-white transition flex items-center gap-1 cursor-pointer">
                                            <i data-lucide="download" class="w-3 h-3"></i> Export
                                        </button>
                                    </div>
                                </div>

                                <div id="terminal-body" class="bg-[#04050a] flex-1 p-5 overflow-y-auto font-mono text-[11px] leading-relaxed text-slate-300 space-y-1.5 scrollbar-thin select-text">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($tab !== 'hosting')
                
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-8 bg-[#090b16]/60 border border-white/10 p-5 rounded-2xl backdrop-blur-md relative z-30 shadow-2xl">
                    <div class="flex items-center gap-3">
                        @if(isset($bot))
                            <button type="button" onclick="confirmDeleteBot()" class="px-5 py-3 rounded-xl border border-rose-500/20 bg-rose-500/5 hover:bg-rose-500/10 text-rose-400 text-xs font-bold transition cursor-pointer flex items-center gap-2">
                                <i data-lucide="trash-2" class="w-4 h-4 text-rose-400"></i> Delete Bot
                            </button>
                        @endif
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3 justify-center">
                        @if($tab === 'builder')
                            <button type="button" onclick="clearActiveFileCanvas()" class="px-5 py-3 rounded-xl border border-amber-500/25 bg-amber-500/5 hover:bg-amber-500/10 text-amber-400 text-xs font-bold transition cursor-pointer flex items-center gap-2">
                                <i data-lucide="eraser" class="w-4 h-4"></i> Clear Canvas
                            </button>
                        @endif
                        <a href="{{ route('dashboard.bots') }}" class="px-5 py-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 text-xs font-bold text-slate-300 hover:text-white transition cursor-pointer flex items-center gap-1.5">
                            Cancel
                        </a>
                        <button type="submit" class="neon-glow-btn text-white text-xs font-bold px-6 py-3 rounded-xl flex items-center gap-2 cursor-pointer shadow-lg shadow-purple-500/25 transition-all">
                            <i data-lucide="check" class="w-4.5 h-4.5"></i> Save Configuration
                        </button>
                    </div>
                </div>
                @endif
            </form>

            @if(isset($bot))
                <form id="delete-bot-form" action="{{ route('dashboard.bots.destroy', $bot) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>
        </div>

        <footer class="w-full bg-[#020204]/50 border-t border-white/5 py-6 text-center text-[10px] text-slate-500 mt-auto">
            <p>&copy; 2026 HublyBot. All rights reserved.</p>
        </footer>
    </main>

    <script>
        lucide.createIcons();

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const icon = document.getElementById('toggle-icon');

            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                icon.setAttribute('data-lucide', 'x');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                icon.setAttribute('data-lucide', 'menu');
            }
            lucide.createIcons();
        }

        function toggleBillingMenu() {
            const submenu = document.getElementById('billing-submenu');
            const chevron = document.getElementById('billing-chevron');
            if (submenu.classList.contains('expanded')) {
                submenu.classList.remove('expanded');
                chevron.classList.remove('rotated');
            } else {
                submenu.classList.add('expanded');
                chevron.classList.add('rotated');
            }
        }

        function toggleBotsMenu() {
            const submenu = document.getElementById('bots-submenu');
            const chevron = document.getElementById('bots-chevron');
            if (submenu.classList.contains('expanded')) {
                submenu.classList.remove('expanded');
                chevron.classList.remove('rotated');
            } else {
                submenu.classList.add('expanded');
                chevron.classList.add('rotated');
            }
        }
    </script>

    <script>
        /**
         * Visual Logic Builder Canvas State and Interaction Engine
         *
         * Manages node lifecycle, drag-and-drop connections, layout tree persistence,
         * advanced action configurations, variable registration, and real-time validation.
         */
        const DISCORD_LOCALES = {
            "bg": "Bulgarian (bg)",
            "cs": "Czech (cs)",
            "da": "Danish (da)",
            "de": "German (de)",
            "el": "Greek (el)",
            "en-GB": "English, UK (en-GB)",
            "es-ES": "Spanish (es-ES)",
            "es-419": "Spanish, LATAM (es-419)",
            "fi": "Finnish (fi)",
            "fr": "French (fr)",
            "hi": "Hindi (hi)",
            "hr": "Croatian (hr)",
            "hu": "Hungarian (hu)",
            "id": "Indonesian (id)",
            "it": "Italian (it)",
            "ja": "Japanese (ja)",
            "ko": "Korean (ko)",
            "lt": "Lithuanian (lt)",
            "nl": "Dutch (nl)",
            "no": "Norwegian (no)",
            "pl": "Polish (pl)",
            "pt-BR": "Portuguese, Brazilian (pt-BR)",
            "ro": "Romanian (ro)",
            "ru": "Russian (ru)",
            "sv-SE": "Swedish (sv-SE)",
            "th": "Thai (th)",
            "tr": "Turkish (tr)",
            "uk": "Ukrainian (uk)",
            "vi": "Vietnamese (vi)",
            "zh-CN": "Chinese, China (zh-CN)",
            "zh-TW": "Chinese, Taiwan (zh-TW)"
        };

        let workspaceState = {
            variables: [],
            tree: []
        };
        
        let activeFileId = null;
        let openFolders = {}; 
        let openAdvancedSettings = {}; 
        const isPremiumUser = @json(Auth::user()->activePlan() !== null);

        function toggleCustomSelect(id) {
            
            document.querySelectorAll('.custom-select-menu').forEach(el => {
                if (el.id !== `menu-${id}`) {
                    el.classList.add('hidden');
                }
            });
            const menu = document.getElementById(`menu-${id}`);
            if (menu) {
                menu.classList.toggle('hidden');
                if (!menu.classList.contains('hidden')) {
                    const searchInput = menu.querySelector('input[type="text"]');
                    if (searchInput) {
                        searchInput.value = '';
                        filterMenu(searchInput, menu.id);
                        setTimeout(() => searchInput.focus(), 50);
                    }
                }
            }
        }

        function filterMenu(input, menuId) {
            const filter = input.value.toLowerCase();
            const menu = document.getElementById(menuId);
            if (!menu) return;
            const items = menu.querySelectorAll('div[onclick]');
            items.forEach(item => {
                const text = item.innerText.toLowerCase();
                if (text.includes(filter)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function filterPlaceholders(input) {
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll('#placeholder-table-body tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function selectBotStatus(statusVal) {
            const input = document.getElementById('bot-status');
            const label = document.getElementById('label-bot-status');
            if (input && label) {
                input.value = statusVal;
                label.innerText = statusVal === 'active' ? 'Active / Online' : 'Inactive / Offline';
            }
            const menu = document.getElementById('menu-bot-status');
            if (menu) {
                menu.classList.add('hidden');
            }
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-select')) {
                document.querySelectorAll('.custom-select-menu').forEach(el => el.classList.add('hidden'));
            }
        });

        function migrateLogicToNodes(logicArray) {
            if (!Array.isArray(logicArray)) return [];
            let newLogic = [];
            logicArray.forEach(block => {
                if (block.type !== 'trigger' && block.type !== 'action') {
                    
                    const trgId = String(block.id || ('trg_' + Math.random().toString(36).substr(2, 9)));
                    const actId = 'act_' + trgId.replace('trg_', '');
                    
                    newLogic.push({
                        id: trgId,
                        type: 'trigger',
                        event: block.type || 'message',
                        trigger_val: block.trigger_val || '',
                        targetActionIds: [actId],
                        x: block.x || 100,
                        y: block.y || 100
                    });
                    
                    newLogic.push({
                        id: actId,
                        type: 'action',
                        action_type: block.action_type || 'reply',
                        action_val: block.action_val || '',
                        channel_val: block.channel_val || '',
                        x: (block.x || 100) + 380,
                        y: block.y || 100
                    });
                } else {
                    if (block.type === 'trigger') {
                        if (!block.targetActionIds) {
                            block.targetActionIds = block.targetActionId ? [block.targetActionId] : [];
                            delete block.targetActionId;
                        }
                    }
                    newLogic.push(block);
                }
            });
            return newLogic;
        }

        function migrateTree(nodes) {
            if (!Array.isArray(nodes)) return;
            nodes.forEach(node => {
                if (node.type === 'file') {
                    node.logic = migrateLogicToNodes(node.logic);
                    if (Array.isArray(node.logic)) {
                        node.logic.forEach(blk => {
                            if (blk.event === 'command') {
                                if (!blk.localizations) {
                                    blk.localizations = {};
                                    ['fr', 'de', 'es'].forEach(lang => {
                                        const nameKey = `adv_loc_name_${lang}`;
                                        const descKey = `adv_loc_desc_${lang}`;
                                        if (blk[nameKey] || blk[descKey]) {
                                            blk.localizations[lang] = {
                                                name: blk[nameKey] || '',
                                                desc: blk[descKey] || ''
                                            };
                                            delete blk[nameKey];
                                            delete blk[descKey];
                                        }
                                    });
                                }
                                if (blk.command_params) {
                                    blk.command_params.forEach(param => {
                                        if (!param.localizations) {
                                            param.localizations = {};
                                            ['fr', 'de', 'es'].forEach(lang => {
                                                const nameKey = `loc_name_${lang}`;
                                                const descKey = `loc_desc_${lang}`;
                                                if (param[nameKey] || param[descKey]) {
                                                    param.localizations[lang] = {
                                                        name: param[nameKey] || '',
                                                        desc: param[descKey] || ''
                                                    };
                                                    delete param[nameKey];
                                                    delete param[descKey];
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        });
                    }
                } else if (node.type === 'folder' && node.children) {
                    migrateTree(node.children);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            let rawSettings = [];
            @if(isset($bot) && is_array($bot->settings))
                rawSettings = @json($bot->settings);
            @endif

            if (Array.isArray(rawSettings)) {
                if (rawSettings.length === 0) {
                    workspaceState = {
                        variables: [],
                        tree: [
                            {
                                id: 'file_main',
                                name: 'main.bot',
                                type: 'file',
                                logic: [
                                    {
                                        id: 'trg_welcome',
                                        type: 'trigger',
                                        event: 'join',
                                        trigger_val: '',
                                        targetActionId: 'act_welcome',
                                        x: 1200,
                                        y: 1400
                                    },
                                    {
                                        id: 'act_welcome',
                                        type: 'action',
                                        action_type: 'reply',
                                        action_val: 'Welcome to our server, {user}! 🎉',
                                        channel_val: '#welcome-channel',
                                        x: 1600,
                                        y: 1400
                                    }
                                ]
                            }
                        ]
                    };
                } else {
                    
                    const migratedLogic = rawSettings.map((trg, i) => ({
                        ...trg,
                        x: trg.x || 100,
                        y: trg.y || (100 + i * 240)
                    }));
                    workspaceState = {
                        variables: [],
                        tree: [
                            {
                                id: 'file_main',
                                name: 'main.bot',
                                type: 'file',
                                logic: migratedLogic
                            }
                        ]
                    };
                }
            } else if (rawSettings && typeof rawSettings === 'object') {
                workspaceState = {
                    variables: rawSettings.variables || [],
                    tree: rawSettings.tree || []
                };
            }

            migrateTree(workspaceState.tree);

            const autoOpenFolders = (nodes) => {
                nodes.forEach(n => {
                    if (n.type === 'folder') {
                        openFolders[n.id] = true;
                        if (n.children) autoOpenFolders(n.children);
                    }
                });
            };
            autoOpenFolders(workspaceState.tree);

            const findFirstFile = (nodes) => {
                for (let n of nodes) {
                    if (n.type === 'file') return n.id;
                    if (n.type === 'folder' && n.children) {
                        const found = findFirstFile(n.children);
                        if (found) return found;
                    }
                }
                return null;
            };
            activeFileId = findFirstFile(workspaceState.tree);

            if (document.getElementById('variables-list')) {
                renderVariables();
            }
            if (document.getElementById('tree-container')) {
                renderExplorer();
                renderActiveFileCanvas();
                setTimeout(centerCanvas, 50);
            }

            const viewport = document.getElementById('canvas-viewport');
            if (viewport) {
                viewport.addEventListener('wheel', (e) => {
                    
                    let parent = e.target;
                    while (parent && parent !== viewport) {
                        const style = window.getComputedStyle(parent);
                        const overflowY = style.overflowY;
                        const isScrollable = (overflowY === 'auto' || overflowY === 'scroll') && (parent.scrollHeight > parent.clientHeight);
                        if (isScrollable) {
                            return; 
                        }
                        parent = parent.parentElement;
                    }

                    e.preventDefault();
                    const zoomDelta = e.deltaY < 0 ? 0.05 : -0.05;
                    zoomCanvas(zoomDelta, e.clientX, e.clientY);
                });

                viewport.addEventListener('dragover', (e) => {
                    if (e.dataTransfer.types.includes('text/blocktype')) {
                        e.preventDefault();
                        e.dataTransfer.dropEffect = 'copy';
                    }
                });

                viewport.addEventListener('drop', (e) => {
                    const blockType = e.dataTransfer.getData('text/blockType');
                    const subType = e.dataTransfer.getData('text/subType');
                    if (blockType && subType) {
                        e.preventDefault();

                        const canvasContent = document.getElementById('canvas-content');
                        if (canvasContent) {
                            const rect = canvasContent.getBoundingClientRect();
                            const x = (e.clientX - rect.left) / zoomScale;
                            const y = (e.clientY - rect.top) / zoomScale;
                            addNewBlock(blockType, subType, x, y);
                        }
                    }
                });

                initializeToolboxDragAndDrop();
            }

            document.getElementById('bot-builder-form').addEventListener('submit', (e) => {
                document.getElementById('settings-input').value = JSON.stringify(workspaceState);
            });
        });

        function renderVariables() {
            const container = document.getElementById('variables-list');
            const emptyState = document.getElementById('variables-empty-state');
            if (!container) return;
            container.innerHTML = '';

            if (workspaceState.variables.length === 0) {
                emptyState.classList.remove('hidden');
                emptyState.classList.add('flex');
                return;
            } else {
                emptyState.classList.add('hidden');
                emptyState.classList.remove('flex');
            }

            workspaceState.variables.forEach((variable, index) => {
                const row = `
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center bg-slate-950/40 border border-white/5 p-3 rounded-xl">
                        <div class="md:col-span-5 space-y-1">
                            <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Variable Key / Name</label>
                            <input type="text" value="${variable.key}" placeholder="e.g. prefix" oninput="updateVariable(${index}, 'key', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-2 text-xs outline-none">
                        </div>
                        <div class="md:col-span-6 space-y-1">
                            <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Constant Value</label>
                            <input type="text" value="${variable.value}" placeholder="e.g. !" oninput="updateVariable(${index}, 'value', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-2 text-xs outline-none">
                        </div>
                        <div class="md:col-span-1 text-right pt-4 md:pt-0">
                            <button type="button" onclick="deleteVariable(${index})" class="p-2 text-slate-500 hover:text-rose-400 transition cursor-pointer rounded-lg hover:bg-white/5">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', row);
            });
            lucide.createIcons();
        }

        function addVariable() {
            workspaceState.variables.push({ key: '', value: '' });
            renderVariables();
        }

        function deleteVariable(index) {
            workspaceState.variables.splice(index, 1);
            renderVariables();
        }

        function updateVariable(index, field, value) {
            workspaceState.variables[index][field] = value;
        }

        function findNodeById(nodes, id) {
            for (let node of nodes) {
                if (node.id === id) return node;
                if (node.type === 'folder' && node.children) {
                    const found = findNodeById(node.children, id);
                    if (found) return found;
                }
            }
            return null;
        }

        function removeNodeById(nodes, id) {
            for (let i = 0; i < nodes.length; i++) {
                if (nodes[i].id === id) {
                    nodes.splice(i, 1);
                    return true;
                }
                if (nodes[i].type === 'folder' && nodes[i].children) {
                    const removed = removeNodeById(nodes[i].children, id);
                    if (removed) return true;
                }
            }
            return false;
        }

        function renderExplorer() {
            const container = document.getElementById('tree-container');
            if (!container) return;
            container.innerHTML = workspaceState.tree.length === 0 
                ? `<div class="text-center text-slate-500 py-6 text-xs font-medium">Empty explorer. Create a file or folder.</div>` 
                : renderTreeNodes(workspaceState.tree, 0);
            lucide.createIcons();
        }

        function renderTreeNodes(nodes, depth) {
            let html = '';
            const sortedNodes = [...nodes].sort((a, b) => (b.type || '').localeCompare(a.type || ''));

            sortedNodes.forEach(node => {
                const isFolder = node.type === 'folder';
                const isOpen = openFolders[node.id] !== false; 
                const isSelected = node.id === activeFileId;
                const paddingLeft = depth * 12;
                
                const iconName = isFolder ? (isOpen ? 'folder-open' : 'folder') : 'file-code';
                const iconColor = isFolder ? 'text-amber-500' : 'text-[#5865F2]';
                
                html += `
                    <div class="group/node flex items-center justify-between px-2.5 py-1.5 rounded-xl transition ${isSelected ? 'bg-[#5865F2]/10 border border-[#5865F2]/20 text-white font-bold' : 'hover:bg-white/5 text-slate-300'}" style="margin-left: ${paddingLeft}px;"
                         draggable="true"
                         ondragstart="onNodeDragStart(event, '${node.id}')"
                         ondragend="onNodeDragEnd(event)"
                         ondragover="onNodeDragOver(event, '${node.id}')"
                         ondragleave="onNodeDragLeave(event, '${node.id}')"
                         ondrop="onNodeDrop(event, '${node.id}')">
                        <div class="flex items-center gap-2 flex-grow min-w-0 cursor-pointer" onclick="${isFolder ? `toggleFolder('${node.id}')` : `selectFile('${node.id}')`}">
                            ${isFolder ? `<i data-lucide="${isOpen ? 'chevron-down' : 'chevron-right'}" class="w-3.5 h-3.5 text-slate-500 shrink-0"></i>` : ''}
                            <i data-lucide="${iconName}" class="w-4 h-4 ${iconColor} shrink-0"></i>
                            <span class="truncate text-[11px]">${node.name}</span>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover/node:opacity-100 transition-opacity">
                            <button type="button" onclick="renameNode('${node.id}')" title="Rename" class="p-1 text-slate-400 hover:text-white hover:bg-white/10 rounded cursor-pointer">
                                <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                            </button>
                            ${isFolder ? `
                            <button type="button" onclick="promptCreateNode('${node.id}', 'file')" title="New File" class="p-1 text-slate-400 hover:text-white hover:bg-white/10 rounded cursor-pointer">
                                <i data-lucide="file-plus" class="w-3.5 h-3.5"></i>
                            </button>
                            <button type="button" onclick="promptCreateNode('${node.id}', 'folder')" title="New Folder" class="p-1 text-slate-400 hover:text-white hover:bg-white/10 rounded cursor-pointer">
                                <i data-lucide="folder-plus" class="w-3.5 h-3.5"></i>
                            </button>
                            ` : ''}
                            <button type="button" onclick="deleteNode('${node.id}')" title="Delete" class="p-1 text-slate-500 hover:text-rose-400 hover:bg-white/10 rounded cursor-pointer">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                `;

                if (isFolder && isOpen && node.children && node.children.length > 0) {
                    html += renderTreeNodes(node.children, depth + 1);
                }
            });
            return html;
        }

        function toggleFolder(folderId) {
            openFolders[folderId] = openFolders[folderId] === false ? true : false;
            renderExplorer();
        }

        function selectFile(fileId) {
            activeFileId = fileId;
            renderExplorer();
            renderActiveFileCanvas();
        }

        let activePromptResolve = null;
        function showCustomPrompt(title, label, placeholder, defaultValue = '') {
            return new Promise((resolve) => {
                const modal = document.getElementById('custom-prompt-modal');
                const titleEl = document.getElementById('prompt-modal-title');
                const labelEl = document.getElementById('prompt-modal-label');
                const inputEl = document.getElementById('prompt-modal-input');
                const submitBtn = document.getElementById('prompt-modal-submit');
                
                titleEl.innerHTML = `<i data-lucide="plus" class="w-4 h-4 text-purple-400"></i> ${title}`;
                labelEl.innerText = label;
                inputEl.placeholder = placeholder;
                inputEl.value = defaultValue;
                
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modal.querySelector('.cyber-card').classList.remove('scale-95');
                    inputEl.focus();
                }, 10);

                lucide.createIcons();
                
                activePromptResolve = resolve;

                const newSubmitBtn = submitBtn.cloneNode(true);
                submitBtn.parentNode.replaceChild(newSubmitBtn, submitBtn);
                newSubmitBtn.addEventListener('click', () => {
                    const val = inputEl.value;
                    closePromptModal(val);
                });

                inputEl.onkeydown = (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        newSubmitBtn.click();
                    }
                };
            });
        }
        
        function closePromptModal(val = null) {
            const modal = document.getElementById('custom-prompt-modal');
            modal.classList.add('opacity-0');
            modal.querySelector('.cyber-card').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
            if (activePromptResolve) {
                activePromptResolve(val);
                activePromptResolve = null;
            }
        }

        let activeConfirmResolve = null;
        function showCustomConfirm(title, message, isDanger = false) {
            return new Promise((resolve) => {
                const modal = document.getElementById('custom-confirm-modal');
                const titleEl = document.getElementById('confirm-modal-title');
                const msgEl = document.getElementById('confirm-modal-message');
                const submitBtn = document.getElementById('confirm-modal-submit');
                
                titleEl.innerHTML = `<i data-lucide="alert-triangle" class="w-4 h-4 ${isDanger ? 'text-rose-400' : 'text-purple-400'}"></i> ${title}`;
                if (isDanger) {
                    titleEl.classList.remove('text-purple-400');
                    titleEl.classList.add('text-rose-400');
                    submitBtn.className = "px-5 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold cursor-pointer transition shadow-lg shadow-rose-500/25";
                } else {
                    titleEl.classList.remove('text-rose-400');
                    titleEl.classList.add('text-purple-400');
                    submitBtn.className = "px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold cursor-pointer transition shadow-lg shadow-purple-500/25";
                }
                msgEl.innerText = message;
                
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modal.querySelector('.cyber-card').classList.remove('scale-95');
                }, 10);

                lucide.createIcons();
                
                activeConfirmResolve = resolve;
                
                const newSubmitBtn = submitBtn.cloneNode(true);
                submitBtn.parentNode.replaceChild(newSubmitBtn, submitBtn);
                newSubmitBtn.addEventListener('click', () => {
                    closeConfirmModal(true);
                });
            });
        }
        
        function closeConfirmModal(result = false) {
            const modal = document.getElementById('custom-confirm-modal');
            modal.classList.add('opacity-0');
            modal.querySelector('.cyber-card').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
            if (activeConfirmResolve) {
                activeConfirmResolve(result);
                activeConfirmResolve = null;
            }
        }

        async function promptCreateNode(parentId, type) {
            const name = await showCustomPrompt(
                `Create New ${type === 'folder' ? 'Folder' : 'File'}`,
                `${type === 'folder' ? 'Folder' : 'File'} Name`,
                type === 'folder' ? 'e.g. command_handlers' : 'e.g. ping.bot'
            );
            if (!name || name.trim() === '') return;
            
            const newNode = {
                id: 'node_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
                name: name.trim(),
                type: type,
                ...(type === 'folder' ? { children: [] } : { logic: [] })
            };

            if (!parentId) {
                workspaceState.tree.push(newNode);
            } else {
                const parent = findNodeById(workspaceState.tree, parentId);
                if (parent && parent.type === 'folder') {
                    if (!parent.children) parent.children = [];
                    parent.children.push(newNode);
                    openFolders[parentId] = true;
                }
            }

            renderExplorer();
            if (type === 'file') {
                selectFile(newNode.id);
            }
        }

        async function deleteNode(nodeId) {
            const confirmed = await showCustomConfirm(
                "Delete Item",
                "Are you sure you want to delete this node? Folders will delete all sub-contents.",
                true
            );
            if (confirmed) {
                removeNodeById(workspaceState.tree, nodeId);
                if (activeFileId === nodeId) {
                    activeFileId = null;
                }
                renderExplorer();
                renderActiveFileCanvas();
            }
        }

        async function renameNode(nodeId) {
            const node = findNodeById(workspaceState.tree, nodeId);
            if (!node) return;
            const newName = await showCustomPrompt(
                `Rename ${node.type === 'folder' ? 'Folder' : 'File'}`,
                `New Name`,
                node.type === 'folder' ? 'e.g. commands' : 'e.g. command.bot',
                node.name
            );
            if (newName && newName.trim() !== '') {
                node.name = newName.trim();
                renderExplorer();
                if (activeFileId === nodeId) {
                    const fileTitle = document.getElementById('active-file-title');
                    if (fileTitle) {
                        fileTitle.innerHTML = `<i data-lucide="file-code" class="w-4 h-4 text-[#5865F2]"></i> ${node.name}`;
                        lucide.createIcons();
                    }
                }
            }
        }

        let draggedNodeId = null;

        function onNodeDragStart(e, nodeId) {
            draggedNodeId = nodeId;
            e.dataTransfer.setData('text/plain', nodeId);
            e.currentTarget.classList.add('opacity-50');
            const treeContainer = document.getElementById('tree-container');
            if (treeContainer) treeContainer.classList.add('dragging-active');
        }

        function onNodeDragEnd(e) {
            e.currentTarget.classList.remove('opacity-50');
            const treeContainer = document.getElementById('tree-container');
            if (treeContainer) treeContainer.classList.remove('dragging-active');
            draggedNodeId = null;
        }

        function onNodeDragOver(e, targetNodeId) {
            const targetNode = findNodeById(workspaceState.tree, targetNodeId);
            if (!targetNode) return;
            
            if (isDescendantOrSelf(draggedNodeId, targetNodeId)) return;
            
            e.preventDefault();
            e.currentTarget.classList.add('bg-[#5865F2]/10');
        }

        function onNodeDragLeave(e, targetNodeId) {
            e.currentTarget.classList.remove('bg-[#5865F2]/10');
        }

        function onNodeDrop(e, targetNodeId) {
            e.preventDefault();
            e.stopPropagation(); 
            e.currentTarget.classList.remove('bg-[#5865F2]/10');
            const nodeId = e.dataTransfer.getData('text/plain') || draggedNodeId;
            if (!nodeId || nodeId === targetNodeId) return;

            moveNode(nodeId, targetNodeId);
        }

        function onRootDragOver(e) {
            e.preventDefault();
        }

        function onRootDrop(e) {
            e.preventDefault();
            const nodeId = e.dataTransfer.getData('text/plain') || draggedNodeId;
            if (!nodeId) return;
            
            moveNode(nodeId, null);
        }

        function isDescendantOrSelf(parentId, childId) {
            if (parentId === childId) return true;
            const parentNode = findNodeById(workspaceState.tree, parentId);
            if (parentNode && parentNode.type === 'folder' && parentNode.children) {
                return parentNode.children.some(child => isDescendantOrSelf(child.id, childId));
            }
            return false;
        }

        function findParentNode(nodes, childId, currentParent = null) {
            for (let node of nodes) {
                if (node.id === childId) return currentParent;
                if (node.type === 'folder' && node.children) {
                    const found = findParentNode(node.children, childId, node);
                    if (found !== undefined) return found;
                }
            }
            return undefined;
        }

        function moveNode(nodeId, targetNodeId) {
            const node = findNodeById(workspaceState.tree, nodeId);
            if (!node) return;

            let destinationFolderId = null;
            if (targetNodeId !== null) {
                const targetNode = findNodeById(workspaceState.tree, targetNodeId);
                if (targetNode) {
                    if (targetNode.type === 'folder') {
                        destinationFolderId = targetNode.id;
                    } else {
                        
                        const parentFolder = findParentNode(workspaceState.tree, targetNode.id);
                        destinationFolderId = parentFolder ? parentFolder.id : null;
                    }
                }
            }

            if (destinationFolderId !== null && isDescendantOrSelf(nodeId, destinationFolderId)) {
                return;
            }

            removeNodeById(workspaceState.tree, nodeId);

            if (destinationFolderId === null) {
                workspaceState.tree.push(node);
            } else {
                const parent = findNodeById(workspaceState.tree, destinationFolderId);
                if (parent && parent.type === 'folder') {
                    if (!parent.children) parent.children = [];
                    parent.children.push(node);
                    openFolders[destinationFolderId] = true;
                }
            }

            renderExplorer();
        }
           
        function getEventLabel(type) {
            if (type === 'join') return 'New Member Joins';
            if (type === 'message') return 'Message Contains Keyword';
            if (type === 'command') return 'Slash Command';
            if (type === 'reaction') return 'User Reacts Emoji';
            if (type === 'leave') return 'Member Leaves Server';
            if (type === 'channel_create') return 'Channel Created';
            if (type === 'role_create') return 'Role Created';
            if (type === 'message_edit') return 'Message Edited';
            if (type === 'message_delete') return 'Message Deleted';
            if (type === 'member_ban') return 'Member Banned';
            if (type === 'member_unban') return 'Member Unbanned';
            if (type === 'voice_update') return 'Voice Channel Update';
            if (type === 'invite_update') return 'Invite Created/Deleted';
            if (type === 'thread_update') return 'Thread Created/Deleted';
            if (type === 'member_update') return 'Member Profile Update';
            if (type === 'emoji_update') return 'Emoji/Sticker Update';
            if (type === 'boost_event') return 'Server Boosted';
            if (type === 'poll_vote') return 'Member Votes Poll';
            if (type === 'auto_moderation_exec') return 'AutoMod Rule Triggered';
            if (type === 'audit_log_create') return 'Audit Log Entry Created';
            if (type === 'scheduled_event_update') return 'Scheduled Event State';
            if (type === 'stage_instance_update') return 'Stage Channel Live';
            if (type === 'ready') return 'Bot Started / Ready';
            if (type === 'typing_start') return 'User Typing Started';
            return 'Select Event';
        }

        function getActionLabel(type) {
            if (type === 'reply') return 'Send Message In Channel';
            if (type === 'reply_direct') return 'Reply to Message / Command';
            if (type === 'send_console') return 'Send to Console';
            if (type === 'dm') return 'Send Direct Message (DM)';
            if (type === 'role') return 'Assign Role';
            if (type === 'remove_role') return 'Remove Role';
            if (type === 'create_channel') return 'Create Channel';
            if (type === 'delete_channel') return 'Delete Channel';
            if (type === 'kick_member') return 'Kick Member';
            if (type === 'ban_member') return 'Ban Member';
            if (type === 'timeout_member') return 'Timeout Member';
            if (type === 'add_reaction') return 'Add Message Reaction';
            if (type === 'pin_message') return 'Pin Message';
            if (type === 'unpin_message') return 'Unpin Message';
            if (type === 'create_thread') return 'Create Thread';
            if (type === 'send_webhook') return 'Send Webhook/API';
            if (type === 'wait_delay') return 'Wait / Delay Flow';
            if (type === 'send_embed') return 'Send Rich Embed';
            if (type === 'set_nickname') return 'Set Nickname';
            if (type === 'lock_channel') return 'Lock Channel';
            if (type === 'unlock_channel') return 'Unlock Channel';

            if (type === 'delete_message') return 'Delete Message';
            if (type === 'create_event') return 'Create Scheduled Event';
            if (type === 'stage_start') return 'Start Stage Instance';
            if (type === 'create_automod_rule') return 'Create AutoMod Rule';
            if (type === 'send_poll') return 'Send Message Poll';
            return 'Select Action';
        }

        function getConditionLabel(type) {
            if (type === 'cond_has_role') return 'User Has Role';
            if (type === 'cond_channel_name') return 'Channel Name Matches';
            if (type === 'cond_random') return 'Random Chance %';
            if (type === 'cond_is_bot') return 'Actor is Bot';
            if (type === 'cond_nsfw') return 'Channel is NSFW';
            if (type === 'cond_has_link') return 'Message Has Link';
            if (type === 'cond_has_attachments') return 'Message Has Attachments';
            if (type === 'cond_user_perm') return 'User Has Permission';
            if (type === 'cond_msg_len') return 'Message Length Check';
            if (type === 'cond_time_range') return 'Time/Schedule Range';
            if (type === 'cond_account_age') return 'Account Age Filter';
            if (type === 'cond_has_role_hierarchy') return 'Role Hierarchy Rank';
            if (type === 'cond_user_id') return 'Specific User ID';
            if (type === 'cond_channel_type') return 'Channel Type';
            if (type === 'cond_boost_tier') return 'Server Boost Level';
            if (type === 'cond_bot_perm') return 'Bot Permissions';
            return 'Select Condition';
        }

        let panX = 0;
        let panY = 0;
        let zoomScale = 1.0;
        let isPanning = false;
        let panStartX = 0;
        let panStartY = 0;

        function updateCanvasTransform() {
            const canvasContent = document.getElementById('canvas-content');
            if (canvasContent) {
                canvasContent.style.transform = `translate(${panX}px, ${panY}px) scale(${zoomScale})`;
            }
            requestAnimationFrame(drawConnections);
        }

        function zoomCanvas(amount, clientX = null, clientY = null) {
            const viewport = document.getElementById('canvas-viewport');
            if (!viewport) return;
            const rect = viewport.getBoundingClientRect();

            let targetX, targetY;
            if (clientX !== null && clientY !== null) {
                targetX = clientX - rect.left;
                targetY = clientY - rect.top;
            } else {
                targetX = rect.width / 2;
                targetY = rect.height / 2;
            }

            const canvasX = (targetX - panX) / zoomScale;
            const canvasY = (targetY - panY) / zoomScale;

            const newScale = Math.max(0.3, Math.min(2.0, zoomScale + amount));

            panX = targetX - canvasX * newScale;
            panY = targetY - canvasY * newScale;
            zoomScale = newScale;

            updateCanvasTransform();
        }

        function centerCanvas() {
            const viewport = document.getElementById('canvas-viewport');
            if (viewport) {
                const rect = viewport.getBoundingClientRect();
                const viewportWidth = rect.width || 1000;
                const viewportHeight = rect.height || 550;
                const canvasWidth = 3000;
                const canvasHeight = 3000;
                panX = -(canvasWidth / 2) + (viewportWidth / 2);
                panY = -(canvasHeight / 2) + (viewportHeight / 2);
                zoomScale = 1.0;
                updateCanvasTransform();
            }
        }

        function resetCanvasTransform() {
            centerCanvas();
        }

        function onCanvasMouseDown(e) {
            
            if (e.target.closest('.node-block') || e.target.closest('button') || e.target.closest('input') || e.target.closest('.custom-select')) {
                return;
            }
            isPanning = true;
            panStartX = e.clientX - panX;
            panStartY = e.clientY - panY;
            document.addEventListener('mousemove', onCanvasMouseMove);
            document.addEventListener('mouseup', onCanvasMouseUp);
            e.preventDefault();
        }

        function onCanvasMouseMove(e) {
            if (!isPanning) return;
            panX = e.clientX - panStartX;
            panY = e.clientY - panStartY;
            updateCanvasTransform();
        }

        function onCanvasMouseUp() {
            isPanning = false;
            document.removeEventListener('mousemove', onCanvasMouseMove);
            document.removeEventListener('mouseup', onCanvasMouseUp);
        }

        function toggleAdvancedSettings(nodeId) {
            const el = document.getElementById(`advanced-settings-${nodeId}`);
            if (el) {
                el.classList.toggle('hidden');
                openAdvancedSettings[nodeId] = !el.classList.contains('hidden');
                lucide.createIcons();
            }
        }

        function renderActiveFileCanvas() {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            const container = document.getElementById('triggers-container');
            const toolbox = document.getElementById('blocks-toolbox');
            if (!container) return;
            const emptyState = document.getElementById('canvas-empty-state');
            const fileTitle = document.getElementById('active-file-title');
            const triggersCountSpan = document.getElementById('file-triggers-count');

            container.innerHTML = '';

            if (!activeFile || activeFile.type !== 'file') {
                emptyState.classList.remove('hidden');
                emptyState.classList.add('flex');
                document.getElementById('empty-state-icon').innerHTML = '<i data-lucide="mouse-pointer-click" class="w-6 h-6 animate-pulse"></i>';
                document.getElementById('empty-state-title').innerText = "No File Selected";
                document.getElementById('empty-state-desc').innerText = "Select a file from the explorer sidebar, or create a new file to start building your bot logic flows.";
                
                fileTitle.innerHTML = '<i data-lucide="file-code" class="w-4 h-4 text-slate-500"></i> No Selected File';
                triggersCountSpan.innerText = "0 Blocks";
                if (toolbox) toolbox.classList.add('hidden');
                lucide.createIcons();
                requestAnimationFrame(drawConnections);
                return;
            }

            fileTitle.innerHTML = `<i data-lucide="file-code" class="w-4 h-4 text-[#5865F2]"></i> ${activeFile.name}`;
            if (toolbox) toolbox.classList.remove('hidden');

            const blocks = activeFile.logic || [];
            triggersCountSpan.innerText = `${blocks.length} Block${blocks.length !== 1 ? 's' : ''}`;

            if (blocks.length === 0) {
                emptyState.classList.remove('hidden');
                emptyState.classList.add('flex');
                document.getElementById('empty-state-icon').innerHTML = '<i data-lucide="plus-circle" class="w-6 h-6"></i>';
                document.getElementById('empty-state-title').innerText = "Empty Logic file";
                document.getElementById('empty-state-desc').innerText = "Add Event Trigger and Execution Action blocks below to design your flows.";
                lucide.createIcons();
                requestAnimationFrame(drawConnections);
                return;
            }

            emptyState.classList.add('hidden');
            emptyState.classList.remove('flex');

            blocks.forEach((node) => {
                let nodeHtml = '';

                if (node.type === 'trigger') {
                    const eventTitle = getEventLabel(node.event);
                    let eventIcon = 'zap';
                    if (node.event === 'message_edit') eventIcon = 'edit';
                    if (node.event === 'message_delete') eventIcon = 'trash';
                    if (node.event === 'member_ban') eventIcon = 'user-x';
                    if (node.event === 'member_unban') eventIcon = 'user-check';
                    if (node.event === 'voice_update') eventIcon = 'volume-2';
                    if (node.event === 'invite_update') eventIcon = 'link';
                    if (node.event === 'thread_update') eventIcon = 'git-pull-request';
                    if (node.event === 'member_update') eventIcon = 'user-cog';
                    if (node.event === 'emoji_update') eventIcon = 'smile-plus';
                    if (node.event === 'message') eventIcon = 'message-square';
                    if (node.event === 'command') eventIcon = 'terminal';
                    if (node.event === 'join') eventIcon = 'user-plus';
                    if (node.event === 'leave') eventIcon = 'user-minus';
                    if (node.event === 'reaction') eventIcon = 'smile';
                    if (node.event === 'channel_create') eventIcon = 'folder-plus';
                    if (node.event === 'role_create') eventIcon = 'shield-plus';

                    let inputHtml = '';
                    let advancedHtml = '';

                    if (node.event === 'message') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Message Keyword</label>
                                <input type="text" 
                                       value="${node.trigger_val || ''}" 
                                       placeholder="e.g. hello"
                                       oninput="updateTriggerVal('${node.id}', this.value)"
                                       class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-purple-500/50">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Match Strategy</label>
                                    <select onchange="updateAdvancedField('${node.id}', 'adv_match_type', this.value)"
                                            class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] outline-none cursor-pointer focus:border-purple-500/50">
                                        <option value="contains" ${node.adv_match_type === 'contains' || !node.adv_match_type ? 'selected' : ''}>Contains Keyword</option>
                                        <option value="exact" ${node.adv_match_type === 'exact' ? 'selected' : ''}>Exact Match</option>
                                        <option value="starts" ${node.adv_match_type === 'starts' ? 'selected' : ''}>Starts With</option>
                                        <option value="ends" ${node.adv_match_type === 'ends' ? 'selected' : ''}>Ends With</option>
                                        <option value="regex" ${node.adv_match_type === 'regex' ? 'selected' : ''}>RegEx Pattern</option>
                                    </select>
                                </div>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1 cursor-pointer select-none">
                                        <input type="checkbox" ${node.adv_ignore_case !== false ? 'checked' : ''} onchange="updateAdvancedField('${node.id}', 'adv_ignore_case', this.checked)" class="rounded bg-slate-950 border-white/10 text-purple-600 focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                        <span class="text-[9px] text-slate-400">Ignore Case</span>
                                    </label>
                                    <label class="flex items-center gap-1 cursor-pointer select-none">
                                        <input type="checkbox" ${node.adv_ignore_bots !== false ? 'checked' : ''} onchange="updateAdvancedField('${node.id}', 'adv_ignore_bots', this.checked)" class="rounded bg-slate-950 border-white/10 text-purple-600 focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                        <span class="text-[9px] text-slate-400">Ignore Bots</span>
                                    </label>
                                </div>
                            </div>
                        `;
                    } else if (node.event === 'command') {
                        const params = node.command_params || [];
                        let paramsHtml = '';
                        params.forEach((param, index) => {
                            
                            param.localizations = param.localizations || {};
                            let paramLocsHtml = '';
                            Object.keys(param.localizations).forEach(locale => {
                                const localeName = DISCORD_LOCALES[locale] || locale;
                                paramLocsHtml += `
                                    <div class="border border-white/5 bg-slate-950/30 p-1.5 rounded-lg space-y-1 relative mt-1">
                                        <div class="flex justify-between items-center">
                                            <span class="text-[8px] font-bold text-purple-400 block">${localeName}</span>
                                            <button type="button" onclick="removeParamLocalization('${node.id}', ${index}, '${locale}')" class="text-slate-500 hover:text-rose-400 transition cursor-pointer p-0.5 rounded hover:bg-white/5">
                                                <i data-lucide="x" class="w-3 h-3"></i>
                                            </button>
                                        </div>
                                        <input type="text" value="${param.localizations[locale].name || ''}" placeholder="Localized Name" oninput="updateParamLocalization('${node.id}', ${index}, '${locale}', 'name', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded px-2 py-0.5 text-[9px] focus:border-purple-500/50 font-mono">
                                        <input type="text" value="${param.localizations[locale].desc || ''}" placeholder="Localized Description" oninput="updateParamLocalization('${node.id}', ${index}, '${locale}', 'desc', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded px-2 py-0.5 text-[9px] focus:border-purple-500/50">
                                    </div>
                                `;
                            });

                            let paramLocaleItems = '';
                            Object.keys(DISCORD_LOCALES).forEach(locale => {
                                if (!param.localizations[locale]) {
                                    paramLocaleItems += `<div onclick="addParamLocalization('${node.id}', ${index}, '${locale}')" class="px-2 py-1.5 hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left">${DISCORD_LOCALES[locale]}</div>`;
                                }
                            });

                            let paramAddLocHtml = '';
                            if (paramLocaleItems) {
                                paramAddLocHtml = `
                                    <div class="relative custom-select mt-1.5" id="select-param-loc-${node.id}-${index}">
                                        <button type="button" onclick="toggleCustomSelect('param-loc-${node.id}-${index}')" class="w-full bg-slate-950/60 border border-white/10 text-white rounded px-2 py-1 text-[9px] flex justify-between items-center cursor-pointer outline-none">
                                            <span class="truncate text-slate-400">Add Locale...</span>
                                            <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400"></i>
                                        </button>
                                        <div id="menu-param-loc-${node.id}-${index}" class="custom-select-menu custom-scrollbar hidden absolute left-0 right-0 mt-1 border border-white/15 rounded-lg pt-0 pb-1 z-[999] shadow-2xl bg-[#080914] text-[9px] max-h-[150px] overflow-y-auto w-[180px]">
                                            <div class="p-1.5 border-b border-white/10 sticky top-0 bg-[#080914] z-50 rounded-t-lg">
                                                <input type="text" placeholder="Search locale..." oninput="filterMenu(this, 'menu-param-loc-${node.id}-${index}')" class="w-full bg-slate-950/70 border border-white/10 text-white rounded px-2 py-0.5 text-[9px] outline-none">
                                            </div>
                                            ${paramLocaleItems}
                                        </div>
                                    </div>
                                `;
                            }

                            paramsHtml += `
                                <div class="border border-white/5 bg-slate-950/40 rounded-xl p-2 space-y-1.5 relative">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Param #${index + 1}</span>
                                        <button type="button" onclick="deleteCommandParam('${node.id}', ${index})" class="text-slate-500 hover:text-rose-400 transition cursor-pointer p-0.5 rounded hover:bg-white/5">
                                            <i data-lucide="x" class="w-3 h-3"></i>
                                        </button>
                                    </div>
                                    <div class="space-y-1">
                                        <input type="text" 
                                               value="${param.name || ''}" 
                                               placeholder="Name (e.g. user)" 
                                               oninput="updateCommandParam('${node.id}', ${index}, 'name', this.value)"
                                               class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] outline-none focus:border-purple-500/50 font-mono">
                                    </div>
                                    <div class="space-y-1">
                                        <input type="text" 
                                               value="${param.desc || ''}" 
                                               placeholder="Description of parameter" 
                                               oninput="updateCommandParam('${node.id}', ${index}, 'desc', this.value)"
                                               class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] outline-none focus:border-purple-500/50">
                                    </div>

                                    <div class="border-t border-white/5 pt-1.5 mt-1 space-y-1">
                                        <span class="text-[8px] font-bold text-slate-500 uppercase tracking-wider block">Localizations</span>
                                        <div class="space-y-1 pr-0.5">
                                            ${paramLocsHtml ? paramLocsHtml : '<div class="text-[8px] text-slate-500 italic">No overrides added.</div>'}
                                        </div>
                                        ${paramAddLocHtml}
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-1.5 items-center">
                                        <div class="relative custom-select" id="select-param-type-${node.id}-${index}">
                                            <button type="button" onclick="toggleCustomSelect('param-type-${node.id}-${index}')" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[9px] flex justify-between items-center cursor-pointer outline-none">
                                                <span>${param.type === 'string' ? 'Text' : param.type === 'integer' ? 'Number' : param.type === 'user' ? 'User' : param.type === 'role' ? 'Role' : 'Channel'}</span>
                                                <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400"></i>
                                            </button>
                                            <div id="menu-param-type-${node.id}-${index}" class="custom-select-menu custom-scrollbar hidden absolute left-0 right-0 mt-1 border border-white/15 rounded-lg py-1 z-[999] shadow-2xl bg-[#080914] text-[9px]">
                                                <div onclick="updateCommandParam('${node.id}', ${index}, 'type', 'string'); renderActiveFileCanvas();" class="px-2 py-1 hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left">Text</div>
                                                <div onclick="updateCommandParam('${node.id}', ${index}, 'type', 'integer'); renderActiveFileCanvas();" class="px-2 py-1 hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left">Number</div>
                                                <div onclick="updateCommandParam('${node.id}', ${index}, 'type', 'user'); renderActiveFileCanvas();" class="px-2 py-1 hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left">User</div>
                                                <div onclick="updateCommandParam('${node.id}', ${index}, 'type', 'role'); renderActiveFileCanvas();" class="px-2 py-1 hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left">Role</div>
                                                <div onclick="updateCommandParam('${node.id}', ${index}, 'type', 'channel'); renderActiveFileCanvas();" class="px-2 py-1 hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left">Channel</div>
                                            </div>
                                        </div>
                                        <label class="flex items-center gap-1 cursor-pointer select-none">
                                            <input type="checkbox" 
                                                   ${param.required ? 'checked' : ''} 
                                                   onchange="updateCommandParam('${node.id}', ${index}, 'required', this.checked)"
                                                   class="rounded bg-slate-950 border-white/10 text-purple-600 focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                            <span class="text-[9px] text-slate-400">Required</span>
                                        </label>
                                    </div>
                                </div>
                            `;
                        });

                        inputHtml = `
                            <div class="space-y-3">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Slash Command Name</label>
                                    <input type="text" 
                                           value="${node.trigger_val || ''}" 
                                           placeholder="e.g. /ping"
                                           oninput="updateTriggerVal('${node.id}', this.value)"
                                           class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-purple-500/50 font-mono font-bold">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Slash Command Description</label>
                                    <input type="text" 
                                           value="${node.adv_desc || ''}" 
                                           placeholder="e.g. Responses with pong"
                                           oninput="updateAdvancedField('${node.id}', 'adv_desc', this.value)"
                                           class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-purple-500/50">
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Parameters (${params.length})</label>
                                        <button type="button" onclick="addCommandParam('${node.id}')" class="px-2 py-0.5 rounded border border-purple-500/30 bg-purple-500/10 hover:bg-purple-500/20 text-[9px] font-bold text-purple-300 transition flex items-center gap-0.5 cursor-pointer">
                                            <i data-lucide="plus" class="w-2.5 h-2.5"></i> Add
                                        </button>
                                    </div>
                                    <div class="space-y-2 pr-0.5">
                                        ${paramsHtml}
                                    </div>
                                </div>
                            </div>
                        `;

                        node.localizations = node.localizations || {};
                        let cmdLocsHtml = '';
                        Object.keys(node.localizations).forEach(locale => {
                            const localeName = DISCORD_LOCALES[locale] || locale;
                            cmdLocsHtml += `
                                <div class="border border-white/5 bg-slate-950/30 p-2 rounded-lg space-y-1.5 relative">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[8px] font-bold text-purple-400 block">${localeName}</span>
                                        <button type="button" onclick="removeCommandLocalization('${node.id}', '${locale}')" class="text-slate-500 hover:text-rose-400 transition cursor-pointer p-0.5 rounded hover:bg-white/5">
                                            <i data-lucide="x" class="w-3 h-3"></i>
                                        </button>
                                    </div>
                                    <input type="text" value="${node.localizations[locale].name || ''}" placeholder="Localized Name" oninput="updateCommandLocalization('${node.id}', '${locale}', 'name', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded px-2 py-0.5 text-[9px] focus:border-purple-500/50 font-mono">
                                    <input type="text" value="${node.localizations[locale].desc || ''}" placeholder="Localized Description" oninput="updateCommandLocalization('${node.id}', '${locale}', 'desc', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded px-2 py-0.5 text-[9px] focus:border-purple-500/50">
                                </div>
                            `;
                        });

                        let cmdLocaleItems = '';
                        Object.keys(DISCORD_LOCALES).forEach(locale => {
                            if (!node.localizations[locale]) {
                                cmdLocaleItems += `<div onclick="addCommandLocalization('${node.id}', '${locale}')" class="px-2 py-1.5 hover:bg-[#5865F2]/20 hover:text-white cursor-pointer transition text-left">${DISCORD_LOCALES[locale]}</div>`;
                            }
                        });

                        let cmdAddLocHtml = '';
                        if (cmdLocaleItems) {
                            cmdAddLocHtml = `
                                <div class="relative custom-select mt-2" id="select-cmd-loc-${node.id}">
                                    <button type="button" onclick="toggleCustomSelect('cmd-loc-${node.id}')" class="w-full bg-slate-950/60 border border-white/10 text-white rounded px-2 py-1 text-[9px] flex justify-between items-center cursor-pointer outline-none">
                                        <span class="truncate text-slate-400">Add Locale...</span>
                                        <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400"></i>
                                    </button>
                                    <div id="menu-cmd-loc-${node.id}" class="custom-select-menu custom-scrollbar hidden absolute left-0 right-0 mt-1 border border-white/15 rounded-lg pt-0 pb-1 z-[999] shadow-2xl bg-[#080914] text-[9px] max-h-[150px] overflow-y-auto w-[180px]">
                                        <div class="p-1.5 border-b border-white/10 sticky top-0 bg-[#080914] z-50 rounded-t-lg">
                                            <input type="text" placeholder="Search locale..." oninput="filterMenu(this, 'menu-cmd-loc-${node.id}')" class="w-full bg-slate-950/70 border border-white/10 text-white rounded px-2 py-0.5 text-[9px] outline-none">
                                        </div>
                                        ${cmdLocaleItems}
                                    </div>
                                </div>
                            `;
                        }

                        advancedHtml = `
                            <div class="space-y-2.5">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider block">Localizations (Name & Desc)</label>
                                    <div class="space-y-1.5 pr-0.5">
                                        ${cmdLocsHtml ? cmdLocsHtml : '<div class="text-[8px] text-slate-500 italic">No overrides added.</div>'}
                                    </div>
                                    ${cmdAddLocHtml}
                                </div>
                            </div>
                        `;
                    } else if (node.event === 'reaction') {
                        inputHtml = `
                            <div class="py-1 text-[10px] text-slate-500 italic leading-relaxed">
                                Triggers when a user reacts with an emoji.
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Emoji Filter</label>
                                    <input type="text" value="${node.adv_emoji || ''}" placeholder="e.g. 👍 or :smile:" oninput="updateAdvancedField('${node.id}', 'adv_emoji', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-purple-500/50">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Channel Filter</label>
                                    <input type="text" value="${node.adv_channel || ''}" placeholder="e.g. #general" oninput="updateAdvancedField('${node.id}', 'adv_channel', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-purple-500/50">
                                </div>
                            </div>
                        `;
                    } else if (node.event === 'voice_update') {
                        inputHtml = `
                            <div class="py-1 text-[10px] text-slate-500 italic leading-relaxed">
                                Triggers on member voice connection states.
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Voice Event Filter</label>
                                    <select onchange="updateAdvancedField('${node.id}', 'adv_voice_filter', this.value)"
                                            class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] outline-none cursor-pointer focus:border-purple-500/50">
                                        <option value="join" ${node.adv_voice_filter === 'join' || !node.adv_voice_filter ? 'selected' : ''}>Joined Voice</option>
                                        <option value="leave" ${node.adv_voice_filter === 'leave' ? 'selected' : ''}>Left Voice</option>
                                        <option value="switch" ${node.adv_voice_filter === 'switch' ? 'selected' : ''}>Switched Channel</option>
                                        <option value="mute" ${node.adv_voice_filter === 'mute' ? 'selected' : ''}>Muted / Deafened</option>
                                    </select>
                                </div>
                            </div>
                        `;
                    } else if (node.event === 'boost_event') {
                        inputHtml = `
                            <div class="py-1 text-[10px] text-purple-300 italic leading-relaxed">
                                Triggers when a user boosts this Discord server.
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider block">Minimum Boost Tier</label>
                                <select onchange="updateAdvancedField('${node.id}', 'adv_boost_tier', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-purple-500/50">
                                    <option value="any" ${node.adv_boost_tier === 'any' || !node.adv_boost_tier ? 'selected' : ''}>Any Tier Boost</option>
                                    <option value="tier1" ${node.adv_boost_tier === 'tier1' ? 'selected' : ''}>Tier 1</option>
                                    <option value="tier2" ${node.adv_boost_tier === 'tier2' ? 'selected' : ''}>Tier 2</option>
                                    <option value="tier3" ${node.adv_boost_tier === 'tier3' ? 'selected' : ''}>Tier 3</option>
                                </select>
                            </div>
                        `;
                    } else if (node.event === 'poll_vote') {
                        inputHtml = `
                            <div class="py-1 text-[10px] text-purple-300 italic leading-relaxed">
                                Triggers when a member casts a vote in a message poll.
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider block font-mono">Answer Index (1-10)</label>
                                <input type="text" value="${node.adv_poll_answer_index || ''}" placeholder="e.g. 1" oninput="updateAdvancedField('${node.id}', 'adv_poll_answer_index', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-purple-500/50">
                            </div>
                        `;
                    } else if (node.event === 'auto_moderation_exec') {
                        inputHtml = `
                            <div class="py-1 text-[10px] text-purple-300 italic leading-relaxed">
                                Triggers when an AutoMod action is executed.
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Trigger Type</label>
                                    <select onchange="updateAdvancedField('${node.id}', 'adv_automod_trigger_type', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-purple-500/50">
                                        <option value="any" ${node.adv_automod_trigger_type === 'any' || !node.adv_automod_trigger_type ? 'selected' : ''}>Any Trigger</option>
                                        <option value="keyword" ${node.adv_automod_trigger_type === 'keyword' ? 'selected' : ''}>Keyword Match</option>
                                        <option value="harmful_link" ${node.adv_automod_trigger_type === 'harmful_link' ? 'selected' : ''}>Harmful Link</option>
                                        <option value="spam" ${node.adv_automod_trigger_type === 'spam' ? 'selected' : ''}>Spam</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Action Type</label>
                                    <select onchange="updateAdvancedField('${node.id}', 'adv_automod_action_type', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-purple-500/50">
                                        <option value="any" ${node.adv_automod_action_type === 'any' || !node.adv_automod_action_type ? 'selected' : ''}>Any Action</option>
                                        <option value="block" ${node.adv_automod_action_type === 'block' ? 'selected' : ''}>Block Message</option>
                                        <option value="alert" ${node.adv_automod_action_type === 'alert' ? 'selected' : ''}>Send Alert</option>
                                        <option value="timeout" ${node.adv_automod_action_type === 'timeout' ? 'selected' : ''}>Timeout User</option>
                                    </select>
                                </div>
                            </div>
                        `;
                    } else if (node.event === 'audit_log_create') {
                        inputHtml = `
                            <div class="py-1 text-[10px] text-purple-300 italic leading-relaxed">
                                Triggers when an Audit Log entry is created.
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Action Type Filter</label>
                                <input type="text" value="${node.adv_audit_action || ''}" placeholder="e.g. MemberKick or ChannelDelete" oninput="updateAdvancedField('${node.id}', 'adv_audit_action', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-purple-500/50">
                            </div>
                        `;
                    } else if (node.event === 'scheduled_event_update') {
                        inputHtml = `
                            <div class="py-1 text-[10px] text-purple-300 italic leading-relaxed">
                                Triggers on Scheduled Event state changes.
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Event State Filter</label>
                                <select onchange="updateAdvancedField('${node.id}', 'adv_event_state', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-purple-500/50">
                                    <option value="any" ${node.adv_event_state === 'any' || !node.adv_event_state ? 'selected' : ''}>Any State Change</option>
                                    <option value="create" ${node.adv_event_state === 'create' ? 'selected' : ''}>Created</option>
                                    <option value="update" ${node.adv_event_state === 'update' ? 'selected' : ''}>Updated / Status Changed</option>
                                    <option value="delete" ${node.adv_event_state === 'delete' ? 'selected' : ''}>Cancelled / Deleted</option>
                                </select>
                            </div>
                        `;
                    } else if (node.event === 'stage_instance_update') {
                        inputHtml = `
                            <div class="py-1 text-[10px] text-purple-300 italic leading-relaxed">
                                Triggers on Stage Instance state changes.
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Stage State Filter</label>
                                <select onchange="updateAdvancedField('${node.id}', 'adv_stage_state', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-purple-500/50">
                                    <option value="any" ${node.adv_stage_state === 'any' || !node.adv_stage_state ? 'selected' : ''}>Any State Change</option>
                                    <option value="create" ${node.adv_stage_state === 'create' ? 'selected' : ''}>Live / Started</option>
                                    <option value="update" ${node.adv_stage_state === 'update' ? 'selected' : ''}>Topic Updated</option>
                                    <option value="delete" ${node.adv_stage_state === 'delete' ? 'selected' : ''}>Ended / Deleted</option>
                                </select>
                            </div>
                        `;
                    } else if (node.event === 'ready') {
                        inputHtml = `
                            <div class="py-1 text-[10px] text-emerald-400 italic leading-relaxed">
                                Triggers as soon as the Bot starts up.
                            </div>
                        `;
                        advancedHtml = `
                            <div class="text-[9px] text-slate-500 italic">No advanced configuration options available for client startup trigger.</div>
                        `;
                    } else if (node.event === 'typing_start') {
                        inputHtml = `
                            <div class="py-1 text-[10px] text-slate-400 italic leading-relaxed">
                                Triggers when a user begins typing in a channel.
                            </div>
                        `;
                        advancedHtml = `
                            <div class="text-[9px] text-slate-500 italic">No advanced configuration options available for typing start.</div>
                        `;
                    } else {
                        inputHtml = `
                            <div class="py-1 text-[10px] text-slate-500 italic leading-relaxed">
                                Triggers on server event.
                            </div>
                        `;
                        advancedHtml = `
                            <div class="text-[9px] text-slate-500 italic">No advanced configuration options available for this event.</div>
                        `;
                    }

                    nodeHtml = `
                        <div class="node-block absolute bg-[#0c0e22]/95 border border-purple-500/30 rounded-2xl p-4 shadow-2xl transition-shadow w-[260px] select-none pointer-events-auto" 
                             style="left: ${node.x || 100}px; top: ${node.y || 100}px;" 
                             id="node-${node.id}">

                            <div class="flex justify-between items-center border-b border-white/5 pb-2 mb-3 cursor-grab active:cursor-grabbing" 
                                 onmousedown="startDragBlock(event, '${node.id}')">
                                <span class="text-[9px] font-bold text-purple-400 uppercase tracking-widest flex items-center gap-1.5">
                                    <i data-lucide="${eventIcon}" class="w-3.5 h-3.5 text-purple-400 shrink-0"></i> ${eventTitle}
                                </span>
                                <div class="flex items-center gap-1">
                                    <button type="button" onclick="toggleAdvancedSettings('${node.id}')" class="text-slate-500 hover:text-purple-400 transition cursor-pointer p-1 hover:bg-white/5 rounded-lg">
                                        <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <button type="button" onclick="deleteBlock('${node.id}')" class="text-slate-500 hover:text-rose-400 transition cursor-pointer p-1 hover:bg-white/5 rounded-lg">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-3 relative">
                                ${inputHtml}

                                <div id="advanced-settings-${node.id}" class="${openAdvancedSettings[node.id] ? '' : 'hidden'} border-t border-white/5 pt-2 mt-2 space-y-2">
                                    <span class="text-[8px] font-bold text-slate-500 uppercase tracking-wider block">Advanced Settings</span>
                                    ${advancedHtml}
                                </div>

                                <div class="absolute right-[-22px] top-1/2 -translate-y-1/2 w-4.5 h-4.5 rounded-full bg-emerald-500 border-[3px] border-[#030307] cursor-pointer hover:scale-125 transition-transform flex items-center justify-center shadow-lg"
                                     onmousedown="startConnecting(event, '${node.id}')"
                                     data-port-type="output"
                                     data-node-id="${node.id}">
                                     <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                </div>
                            </div>
                        </div>
                    `;
                } else if (node.type === 'action') {
                    const actionTitle = getActionLabel(node.action_type);
                    let actionIcon = 'play';
                    if (node.action_type === 'reply') actionIcon = 'message-square';
                    if (node.action_type === 'reply_direct') actionIcon = 'reply';
                    if (node.action_type === 'dm') actionIcon = 'mail';
                    if (node.action_type === 'role') actionIcon = 'shield';
                    if (node.action_type === 'remove_role') actionIcon = 'shield-alert';
                    if (node.action_type === 'create_channel') actionIcon = 'plus-circle';
                    if (node.action_type === 'delete_channel') actionIcon = 'minus-circle';
                    if (node.action_type === 'kick_member') actionIcon = 'user-minus';
                    if (node.action_type === 'ban_member') actionIcon = 'user-x';
                    if (node.action_type === 'timeout_member') actionIcon = 'timer';
                    if (node.action_type === 'add_reaction') actionIcon = 'smile';
                    if (node.action_type === 'pin_message') actionIcon = 'pin';
                    if (node.action_type === 'unpin_message') actionIcon = 'pin-off';
                    if (node.action_type === 'create_thread') actionIcon = 'message-square-plus';
                    if (node.action_type === 'send_webhook') actionIcon = 'webhook';
                    if (node.action_type === 'send_console') actionIcon = 'terminal';
                    if (node.action_type === 'wait_delay') actionIcon = 'hourglass';
                    if (node.action_type === 'send_embed') actionIcon = 'layout-template';
                    if (node.action_type === 'set_nickname') actionIcon = 'user-circle';
                    if (node.action_type === 'lock_channel') actionIcon = 'lock';
                    if (node.action_type === 'unlock_channel') actionIcon = 'unlock';

                    let inputHtml = '';
                    let advancedHtml = '';

                    if (node.action_type === 'send_console') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Console Message Content</label>
                                <input type="text" 
                                       value="${node.action_val || ''}" 
                                       placeholder="e.g. Bot executed command successfully!" 
                                       oninput="updateActionVal('${node.id}', this.value)"
                                       class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                            </div>
                        `;
                    } else if (node.action_type === 'reply') {
                        inputHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Channel Name / ID</label>
                                    <input type="text" 
                                           value="${node.channel_val || ''}" 
                                           placeholder="e.g. #general or 123456..." 
                                           oninput="updateChannelVal('${node.id}', this.value)"
                                           class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Message Content</label>
                                    <input type="text" 
                                           value="${node.action_val || ''}" 
                                           placeholder="Hello!" 
                                           oninput="updateActionVal('${node.id}', this.value)"
                                           class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                                </div>
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1 cursor-pointer select-none">
                                        <input type="checkbox" ${node.adv_ping_reply ? 'checked' : ''} onchange="updateAdvancedField('${node.id}', 'adv_ping_reply', this.checked)" class="rounded bg-slate-950 border-white/10 text-[#5865F2] focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                        <span class="text-[9px] text-slate-400">Ping Reply</span>
                                    </label>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Auto-Delete After (Secs)</label>
                                    <input type="text" value="${node.adv_delete_after || ''}" placeholder="e.g. 10" oninput="updateAdvancedField('${node.id}', 'adv_delete_after', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                                </div>
                            </div>
                        `;
                    } else if (node.action_type === 'reply_direct') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Message Content</label>
                                <input type="text" 
                                       value="${node.action_val || ''}" 
                                       placeholder="Hello! (Replies to trigger)" 
                                       oninput="updateActionVal('${node.id}', this.value)"
                                       class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-1 cursor-pointer select-none">
                                        <input type="checkbox" ${node.adv_ping_reply ? 'checked' : ''} onchange="updateAdvancedField('${node.id}', 'adv_ping_reply', this.checked)" class="rounded bg-slate-950 border-white/10 text-[#5865F2] focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                        <span class="text-[9px] text-slate-400">Ping Reply</span>
                                    </label>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Auto-Delete After (Secs)</label>
                                    <input type="text" value="${node.adv_delete_after || ''}" placeholder="e.g. 10" oninput="updateAdvancedField('${node.id}', 'adv_delete_after', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                                </div>
                            </div>
                        `;
                    } else if (node.action_type === 'dm') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Direct Message Content</label>
                                <input type="text" 
                                        value="${node.action_val || ''}" 
                                        placeholder="Welcome to our server!" 
                                        oninput="updateActionVal('${node.id}', this.value)"
                                        class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                            </div>
                        `;
                        advancedHtml = '';
                    } else if (node.action_type === 'role' || node.action_type === 'remove_role' || node.action_type === 'kick_member' || node.action_type === 'ban_member' || node.action_type === 'timeout_member') {
                        let innerInput = '';
                        let extraFieldHtml = '';
                        if (node.action_type === 'role' || node.action_type === 'remove_role') {
                            innerInput = `
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Role Name / ID</label>
                                    <input type="text" 
                                            value="${node.action_val || ''}" 
                                            placeholder="e.g. Member" 
                                            oninput="updateActionVal('${node.id}', this.value)"
                                            class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                                </div>
                            `;
                        } else if (node.action_type === 'kick_member') {
                            innerInput = `
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Kick Target User</label>
                                    <input type="text" 
                                            value="${node.action_val || ''}" 
                                            placeholder="e.g. {user}" 
                                            oninput="updateActionVal('${node.id}', this.value)"
                                            class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                                </div>
                            `;
                        } else if (node.action_type === 'ban_member') {
                            innerInput = `
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Ban Target User</label>
                                    <input type="text" 
                                            value="${node.action_val || ''}" 
                                            placeholder="e.g. {user}" 
                                            oninput="updateActionVal('${node.id}', this.value)"
                                            class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                                </div>
                            `;
                            extraFieldHtml = `
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Delete History (Days)</label>
                                    <select onchange="updateAdvancedField('${node.id}', 'adv_delete_days', this.value)"
                                            class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] outline-none cursor-pointer focus:border-blue-500/50 font-sans">
                                        <option value="0" ${node.adv_delete_days === '0' || !node.adv_delete_days ? 'selected' : ''}>Don't Delete</option>
                                        <option value="1" ${node.adv_delete_days === '1' ? 'selected' : ''}>1 Day</option>
                                        <option value="7" ${node.adv_delete_days === '7' ? 'selected' : ''}>7 Days</option>
                                    </select>
                                </div>
                            `;
                        } else if (node.action_type === 'timeout_member') {
                            innerInput = `
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Duration (Minutes)</label>
                                    <input type="text" 
                                            value="${node.action_val || ''}" 
                                            placeholder="e.g. 60" 
                                            oninput="updateActionVal('${node.id}', this.value)"
                                            class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                                </div>
                            `;
                        }

                        inputHtml = innerInput;
                        advancedHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Audit Log Reason</label>
                                    <input type="text" value="${node.adv_reason || ''}" placeholder="Rule action reason..." oninput="updateAdvancedField('${node.id}', 'adv_reason', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                                </div>
                                ${extraFieldHtml}
                            </div>
                        `;
                    } else if (node.action_type === 'create_channel') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">New Channel Name</label>
                                <input type="text" 
                                        value="${node.action_val || ''}" 
                                        placeholder="e.g. custom-channel" 
                                        oninput="updateActionVal('${node.id}', this.value)"
                                        class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Channel Type</label>
                                    <select onchange="updateAdvancedField('${node.id}', 'adv_channel_type', this.value)"
                                            class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] outline-none cursor-pointer focus:border-blue-500/50">
                                        <option value="text" ${node.adv_channel_type === 'text' || !node.adv_channel_type ? 'selected' : ''}>Text Channel</option>
                                        <option value="voice" ${node.adv_channel_type === 'voice' ? 'selected' : ''}>Voice Channel</option>
                                        <option value="category" ${node.adv_channel_type === 'category' ? 'selected' : ''}>Category</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Topic / Description</label>
                                    <input type="text" value="${node.adv_channel_topic || ''}" placeholder="Channel description topic..." oninput="updateAdvancedField('${node.id}', 'adv_channel_topic', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                                </div>
                                <label class="flex items-center gap-1 cursor-pointer select-none">
                                    <input type="checkbox" ${node.adv_nsfw ? 'checked' : ''} onchange="updateAdvancedField('${node.id}', 'adv_nsfw', this.checked)" class="rounded bg-slate-950 border-white/10 text-[#5865F2] focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                    <span class="text-[9px] text-slate-400">NSFW Channel</span>
                                </label>
                            </div>
                        `;
                    } else if (node.action_type === 'delete_channel') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Channel Name / ID to Delete</label>
                                <input type="text" 
                                        value="${node.channel_val || ''}" 
                                        placeholder="e.g. #temp-channel" 
                                        oninput="updateChannelVal('${node.id}', this.value)"
                                        class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Audit Log Reason</label>
                                <input type="text" value="${node.adv_reason || ''}" placeholder="Channel removal reason..." oninput="updateAdvancedField('${node.id}', 'adv_reason', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                            </div>
                        `;
                    } else if (node.action_type === 'add_reaction') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Emoji to React</label>
                                <input type="text" 
                                        value="${node.action_val || ''}" 
                                        placeholder="e.g. 👍 or :smile:" 
                                        oninput="updateActionVal('${node.id}', this.value)"
                                        class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50 font-mono">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="text-[9px] text-slate-500 italic">No advanced configuration options available for reaction actions.</div>
                        `;
                    } else if (node.action_type === 'pin_message' || node.action_type === 'unpin_message') {
                        inputHtml = `
                            <div class="py-1 text-[10px] text-slate-500 italic leading-relaxed">
                                Pins or unpins the triggering message.
                            </div>
                        `;
                        advancedHtml = `
                            <div class="text-[9px] text-slate-500 italic">No advanced configuration options available for message pin.</div>
                        `;
                    } else if (node.action_type === 'create_thread') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Thread Name</label>
                                <input type="text" 
                                       value="${node.action_val || ''}" 
                                       placeholder="e.g. ticket-thread" 
                                       oninput="updateActionVal('${node.id}', this.value)"
                                       class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="text-[9px] text-slate-500 italic">No advanced configuration options available for thread creation.</div>
                        `;
                    } else if (node.action_type === 'send_webhook') {
                        inputHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Webhook URL</label>
                                    <input type="text" 
                                           value="${node.channel_val || ''}" 
                                           placeholder="https://discord.com/api/webhooks/..." 
                                           oninput="updateChannelVal('${node.id}', this.value)"
                                           class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">JSON Payload</label>
                                    <input type="text" 
                                           value="${node.action_val || ''}" 
                                           placeholder='e.g. {"content": "Hello!"}' 
                                           oninput="updateActionVal('${node.id}', this.value)"
                                           class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50 font-mono">
                                </div>
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">HTTP Request Method</label>
                                    <select onchange="updateAdvancedField('${node.id}', 'adv_webhook_method', this.value)"
                                            class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] outline-none cursor-pointer focus:border-blue-500/50">
                                        <option value="POST" ${node.adv_webhook_method === 'POST' || !node.adv_webhook_method ? 'selected' : ''}>POST</option>
                                        <option value="PUT" ${node.adv_webhook_method === 'PUT' ? 'selected' : ''}>PUT</option>
                                        <option value="PATCH" ${node.adv_webhook_method === 'PATCH' ? 'selected' : ''}>PATCH</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Custom Headers (JSON)</label>
                                    <input type="text" value="${node.adv_webhook_headers || ''}" placeholder='e.g. {"Authorization": "Bearer 123"}' oninput="updateAdvancedField('${node.id}', 'adv_webhook_headers', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50 font-mono">
                                </div>
                            </div>
                        `;
                    } else if (node.action_type === 'wait_delay') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Delay (Seconds)</label>
                                <input type="text" 
                                       value="${node.action_val || ''}" 
                                       placeholder="e.g. 5" 
                                       oninput="updateActionVal('${node.id}', this.value)"
                                       class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="text-[9px] text-slate-500 italic">Configure delay time directly above. No advanced settings needed.</div>
                        `;
                    } else if (node.action_type === 'send_embed') {
                        inputHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Embed Title</label>
                                    <input type="text" 
                                           value="${node.embed_title || ''}" 
                                           placeholder="e.g. Welcome Announcement" 
                                           oninput="updateEmbedField('${node.id}', 'embed_title', this.value)"
                                           class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Embed Description</label>
                                    <textarea placeholder="e.g. Welcome to our server!" 
                                              oninput="updateEmbedField('${node.id}', 'embed_desc', this.value)"
                                              class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50 h-16 resize-none">${node.embed_desc || ''}</textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="space-y-1">
                                        <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Color (Hex)</label>
                                        <input type="text" 
                                               value="${node.embed_color || ''}" 
                                               placeholder="e.g. #5865F2" 
                                               oninput="updateEmbedField('${node.id}', 'embed_color', this.value)"
                                               class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50 font-mono">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Image URL</label>
                                        <input type="text" 
                                               value="${node.embed_image || ''}" 
                                               placeholder="https://..." 
                                               oninput="updateEmbedField('${node.id}', 'embed_image', this.value)"
                                               class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                                    </div>
                                </div>
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Thumbnail Image URL</label>
                                    <input type="text" value="${node.adv_embed_thumbnail || ''}" placeholder="https://..." oninput="updateAdvancedField('${node.id}', 'adv_embed_thumbnail', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Author Name</label>
                                    <input type="text" value="${node.adv_embed_author || ''}" placeholder="Author Name" oninput="updateAdvancedField('${node.id}', 'adv_embed_author', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Footer Text</label>
                                    <input type="text" value="${node.adv_embed_footer || ''}" placeholder="Footer info text" oninput="updateAdvancedField('${node.id}', 'adv_embed_footer', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                                </div>
                                <label class="flex items-center gap-1 cursor-pointer select-none">
                                    <input type="checkbox" ${node.adv_embed_timestamp ? 'checked' : ''} onchange="updateAdvancedField('${node.id}', 'adv_embed_timestamp', this.checked)" class="rounded bg-slate-950 border-white/10 text-[#5865F2] focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                    <span class="text-[9px] text-slate-400">Include Current Timestamp</span>
                                </label>
                            </div>
                        `;
                    } else if (node.action_type === 'set_nickname') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">New Nickname</label>
                                <input type="text" 
                                       value="${node.action_val || ''}" 
                                       placeholder="e.g. Arthur" 
                                       oninput="updateActionVal('${node.id}', this.value)"
                                       class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Audit Log Reason</label>
                                <input type="text" value="${node.adv_reason || ''}" placeholder="Nickname update reason..." oninput="updateAdvancedField('${node.id}', 'adv_reason', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                            </div>
                        `;
                    } else if (node.action_type === 'lock_channel' || node.action_type === 'unlock_channel') {
                        inputHtml = `
                            <div class="py-1 text-[10px] text-slate-500 italic leading-relaxed">
                                Modifies send permissions for @everyone.
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Audit Log Reason</label>
                                <input type="text" value="${node.adv_reason || ''}" placeholder="Channel permissions reason..." oninput="updateAdvancedField('${node.id}', 'adv_reason', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                            </div>
                        `;



                    } else if (node.action_type === 'delete_message') {
                        inputHtml = `
                            <div class="py-1 text-[10px] text-slate-500 italic leading-relaxed">
                                Deletes the triggering message automatically.
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Audit Log Reason</label>
                                <input type="text" value="${node.adv_reason || ''}" placeholder="Message cleanup reason" oninput="updateAdvancedField('${node.id}', 'adv_reason', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                            </div>
                        `;
                    } else if (node.action_type === 'create_event') {
                        inputHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Event Title / Name</label>
                                    <input type="text" value="${node.action_val || ''}" placeholder="e.g. Community Hangout" oninput="updateActionVal('${node.id}', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                                </div>
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Event Description</label>
                                    <input type="text" value="${node.adv_event_desc || ''}" placeholder="Hangout details..." oninput="updateAdvancedField('${node.id}', 'adv_event_desc', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Voice/Stage Channel ID</label>
                                    <input type="text" value="${node.adv_event_channel || ''}" placeholder="e.g. 1234567..." oninput="updateAdvancedField('${node.id}', 'adv_event_channel', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                                </div>
                            </div>
                        `;
                    } else if (node.action_type === 'stage_start') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Stage Channel ID / Name</label>
                                <input type="text" value="${node.action_val || ''}" placeholder="e.g. #stage or 12345..." oninput="updateActionVal('${node.id}', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Stage Topic</label>
                                <input type="text" value="${node.adv_topic || ''}" placeholder="Stage topic talk..." oninput="updateAdvancedField('${node.id}', 'adv_topic', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                            </div>
                        `;
                    } else if (node.action_type === 'create_automod_rule') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">AutoMod Rule Name</label>
                                <input type="text" value="${node.action_val || ''}" placeholder="e.g. Block bad words" oninput="updateActionVal('${node.id}', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50 font-mono">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Blocked Keywords (comma-sep)</label>
                                <input type="text" value="${node.adv_keywords || ''}" placeholder="e.g. word1, word2" oninput="updateAdvancedField('${node.id}', 'adv_keywords', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                            </div>
                        `;
                    } else if (node.action_type === 'send_poll') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Poll Question</label>
                                <input type="text" value="${node.action_val || ''}" placeholder="e.g. What is your favorite color?" oninput="updateActionVal('${node.id}', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-blue-500/50">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Answers (comma-separated, max 10)</label>
                                <input type="text" value="${node.adv_answers || ''}" placeholder="e.g. Blue, Red, Green" oninput="updateAdvancedField('${node.id}', 'adv_answers', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-blue-500/50">
                            </div>
                        `;
                    }

                    nodeHtml = `
                        <div class="node-block absolute bg-[#0c0e22]/95 border border-blue-500/30 rounded-2xl p-4 shadow-2xl transition-shadow w-[270px] select-none pointer-events-auto" 
                             style="left: ${node.x || 480}px; top: ${node.y || 100}px;" 
                             id="node-${node.id}">

                            <div class="flex justify-between items-center border-b border-white/5 pb-2 mb-3 cursor-grab active:cursor-grabbing" 
                                 onmousedown="startDragBlock(event, '${node.id}')">
                                <span class="text-[9px] font-bold text-blue-400 uppercase tracking-widest flex items-center gap-1.5">
                                    <i data-lucide="${actionIcon}" class="w-3.5 h-3.5 text-blue-400 shrink-0"></i> ${actionTitle}
                                </span>
                                <div class="flex items-center gap-1">
                                    <button type="button" onclick="toggleAdvancedSettings('${node.id}')" class="text-slate-500 hover:text-blue-400 transition cursor-pointer p-1 hover:bg-white/5 rounded-lg">
                                        <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <button type="button" onclick="deleteBlock('${node.id}')" class="text-slate-500 hover:text-rose-400 transition cursor-pointer p-1 hover:bg-white/5 rounded-lg">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </div>
 
                            <div class="space-y-3 relative">
                                ${inputHtml}

                                <div id="advanced-settings-${node.id}" class="${openAdvancedSettings[node.id] ? '' : 'hidden'} border-t border-white/5 pt-2 mt-2 space-y-2">
                                    <span class="text-[8px] font-bold text-slate-500 uppercase tracking-wider block">Advanced Settings</span>
                                    ${advancedHtml}
                                </div>

                                <div class="absolute left-[-22px] top-1/2 -translate-y-1/2 w-4.5 h-4.5 rounded-full bg-[#5865F2] border-[3px] border-[#030307] flex items-center justify-center shadow-lg"
                                     data-port-type="input"
                                     data-node-id="${node.id}">
                                     <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                </div>
                            </div>
                        </div>
                    `;
                } else if (node.type === 'condition') {
                    const conditionTitle = getConditionLabel(node.condition_type);
                    let conditionIcon = 'git-commit';
                    if (node.condition_type === 'cond_has_role') conditionIcon = 'shield-check';
                    if (node.condition_type === 'cond_channel_name') conditionIcon = 'hash';
                    if (node.condition_type === 'cond_random') conditionIcon = 'dices';
                    if (node.condition_type === 'cond_is_bot') conditionIcon = 'bot';
                    if (node.condition_type === 'cond_nsfw') conditionIcon = 'alert-octagon';
                    if (node.condition_type === 'cond_has_link') conditionIcon = 'external-link';
                    if (node.condition_type === 'cond_has_attachments') conditionIcon = 'paperclip';
                    if (node.condition_type === 'cond_user_perm') conditionIcon = 'key';
                    if (node.condition_type === 'cond_msg_len') conditionIcon = 'ruler';
                    if (node.condition_type === 'cond_time_range') conditionIcon = 'clock';

                    let inputHtml = '';
                    let advancedHtml = '';

                    if (node.condition_type === 'cond_has_role') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Role Name / ID</label>
                                <input type="text" 
                                       value="${node.action_val || ''}" 
                                       placeholder="e.g. Moderator" 
                                       oninput="updateActionVal('${node.id}', this.value)"
                                       class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-amber-500/50">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <label class="flex items-center gap-1 cursor-pointer select-none">
                                    <input type="checkbox" ${node.adv_invert_cond ? 'checked' : ''} onchange="updateAdvancedField('${node.id}', 'adv_invert_cond', this.checked)" class="rounded bg-slate-950 border-white/10 text-amber-600 focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                    <span class="text-[9px] text-slate-400">Invert Condition (NOT check)</span>
                                </label>
                            </div>
                        `;
                    } else if (node.condition_type === 'cond_channel_name') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Channel Name Matches</label>
                                <input type="text" 
                                       value="${node.action_val || ''}" 
                                       placeholder="e.g. general" 
                                       oninput="updateActionVal('${node.id}', this.value)"
                                       class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-amber-500/50">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <label class="flex items-center gap-1 cursor-pointer select-none">
                                    <input type="checkbox" ${node.adv_invert_cond ? 'checked' : ''} onchange="updateAdvancedField('${node.id}', 'adv_invert_cond', this.checked)" class="rounded bg-slate-950 border-white/10 text-amber-600 focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                    <span class="text-[9px] text-slate-400">Invert Condition (NOT check)</span>
                                </label>
                            </div>
                        `;
                    } else if (node.condition_type === 'cond_random') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Chance % (1-100)</label>
                                <input type="text" 
                                       value="${node.action_val || ''}" 
                                       placeholder="e.g. 50" 
                                       oninput="updateActionVal('${node.id}', this.value)"
                                       class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-amber-500/50">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="text-[9px] text-slate-500 italic">No advanced configuration options available for random chance checks.</div>
                        `;
                    } else if (['cond_is_bot', 'cond_nsfw', 'cond_has_link', 'cond_has_attachments'].includes(node.condition_type)) {
                        inputHtml = `
                            <div class="py-1 text-[10px] text-slate-500 italic leading-relaxed">
                                Evaluates event attributes dynamically.
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <label class="flex items-center gap-1 cursor-pointer select-none">
                                    <input type="checkbox" ${node.adv_invert_cond ? 'checked' : ''} onchange="updateAdvancedField('${node.id}', 'adv_invert_cond', this.checked)" class="rounded bg-slate-950 border-white/10 text-amber-600 focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                    <span class="text-[9px] text-slate-400">Invert Condition (NOT check)</span>
                                </label>
                            </div>
                        `;
                    } else if (node.condition_type === 'cond_user_perm') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Required Permission</label>
                                <input type="text" 
                                       value="${node.action_val || ''}" 
                                       placeholder="e.g. Administrator" 
                                       oninput="updateActionVal('${node.id}', this.value)"
                                       class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-amber-500/50">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <label class="flex items-center gap-1 cursor-pointer select-none">
                                    <input type="checkbox" ${node.adv_invert_cond ? 'checked' : ''} onchange="updateAdvancedField('${node.id}', 'adv_invert_cond', this.checked)" class="rounded bg-slate-950 border-white/10 text-amber-600 focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                    <span class="text-[9px] text-slate-400">Invert Condition (NOT check)</span>
                                </label>
                            </div>
                        `;
                    } else if (node.condition_type === 'cond_msg_len') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Length Constraint (e.g. 10)</label>
                                <input type="text" 
                                       value="${node.action_val || ''}" 
                                       placeholder="e.g. 100" 
                                       oninput="updateActionVal('${node.id}', this.value)"
                                       class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-amber-500/50 font-mono">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Comparison Operator</label>
                                    <select onchange="updateAdvancedField('${node.id}', 'adv_operator', this.value)"
                                            class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] outline-none cursor-pointer focus:border-amber-500/50">
                                        <option value=">" ${node.adv_operator === '>' || !node.adv_operator ? 'selected' : ''}>Greater Than (&gt;)</option>
                                        <option value="<" ${node.adv_operator === '<' ? 'selected' : ''}>Less Than (&lt;)</option>
                                        <option value="==" ${node.adv_operator === '==' ? 'selected' : ''}>Exactly Equal (==)</option>
                                    </select>
                                </div>
                            </div>
                        `;
                    } else if (node.condition_type === 'cond_account_age') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Minimum Account Age (Days)</label>
                                <input type="number" 
                                       value="${node.action_val || ''}" 
                                       placeholder="e.g. 30" 
                                       oninput="updateActionVal('${node.id}', this.value)"
                                       class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-amber-500/50">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <label class="flex items-center gap-1 cursor-pointer select-none">
                                    <input type="checkbox" ${node.adv_invert_cond ? 'checked' : ''} onchange="updateAdvancedField('${node.id}', 'adv_invert_cond', this.checked)" class="rounded bg-slate-950 border-white/10 text-amber-600 focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                    <span class="text-[9px] text-slate-400">Invert Condition (NOT check)</span>
                                </label>
                            </div>
                        `;
                    } else if (node.condition_type === 'cond_has_role_hierarchy') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Role hierarchy comparison</label>
                                <input type="text" 
                                       value="${node.action_val || ''}" 
                                       placeholder="Role name to compare" 
                                       oninput="updateActionVal('${node.id}', this.value)"
                                       class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-amber-500/50">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Comparison Mode</label>
                                    <select onchange="updateAdvancedField('${node.id}', 'adv_hierarchy_operator', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-amber-500/50">
                                        <option value="higher" ${node.adv_hierarchy_operator === 'higher' || !node.adv_hierarchy_operator ? 'selected' : ''}>Target is Higher Rank</option>
                                        <option value="lower" ${node.adv_hierarchy_operator === 'lower' ? 'selected' : ''}>Target is Lower Rank</option>
                                    </select>
                                </div>
                            </div>
                        `;
                    } else if (node.condition_type === 'cond_user_id') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Specific User ID</label>
                                <input type="text" 
                                       value="${node.action_val || ''}" 
                                       placeholder="e.g. 1234567890..." 
                                       oninput="updateActionVal('${node.id}', this.value)"
                                       class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-amber-500/50 font-mono">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <label class="flex items-center gap-1 cursor-pointer select-none">
                                    <input type="checkbox" ${node.adv_invert_cond ? 'checked' : ''} onchange="updateAdvancedField('${node.id}', 'adv_invert_cond', this.checked)" class="rounded bg-slate-950 border-white/10 text-amber-600 focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                    <span class="text-[9px] text-slate-400">Invert Condition (NOT check)</span>
                                </label>
                            </div>
                        `;
                    } else if (node.condition_type === 'cond_channel_type') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Required Channel Type</label>
                                <select onchange="updateActionVal('${node.id}', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-amber-500/50">
                                    <option value="text" ${node.action_val === 'text' || !node.action_val ? 'selected' : ''}>Text Channel</option>
                                    <option value="voice" ${node.action_val === 'voice' ? 'selected' : ''}>Voice Channel</option>
                                    <option value="category" ${node.action_val === 'category' ? 'selected' : ''}>Category</option>
                                    <option value="announcement" ${node.action_val === 'announcement' ? 'selected' : ''}>Announcement / News</option>
                                    <option value="stage" ${node.action_val === 'stage' ? 'selected' : ''}>Stage Channel</option>
                                    <option value="thread" ${node.action_val === 'thread' ? 'selected' : ''}>Thread Channel</option>
                                </select>
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <label class="flex items-center gap-1 cursor-pointer select-none">
                                    <input type="checkbox" ${node.adv_invert_cond ? 'checked' : ''} onchange="updateAdvancedField('${node.id}', 'adv_invert_cond', this.checked)" class="rounded bg-slate-950 border-white/10 text-amber-600 focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                    <span class="text-[9px] text-slate-400">Invert Condition (NOT check)</span>
                                </label>
                            </div>
                        `;
                    } else if (node.condition_type === 'cond_boost_tier') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Boost Tier Compare</label>
                                <select onchange="updateActionVal('${node.id}', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-purple-500/50">
                                    <option value="0" ${node.action_val === '0' || !node.action_val ? 'selected' : ''}>Tier 0</option>
                                    <option value="1" ${node.action_val === '1' ? 'selected' : ''}>Tier 1</option>
                                    <option value="2" ${node.action_val === '2' ? 'selected' : ''}>Tier 2</option>
                                    <option value="3" ${node.action_val === '3' ? 'selected' : ''}>Tier 3</option>
                                </select>
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Comparison Operator</label>
                                    <select onchange="updateAdvancedField('${node.id}', 'adv_operator', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-purple-500/50">
                                        <option value=">=" ${node.adv_operator === '>=' || !node.adv_operator ? 'selected' : ''}>Greater Than or Equal (>=)</option>
                                        <option value="==" ${node.adv_operator === '==' ? 'selected' : ''}>Exactly Equal (==)</option>
                                        <option value="<=" ${node.adv_operator === '<=' ? 'selected' : ''}>Less Than or Equal (<=)</option>
                                    </select>
                                </div>
                            </div>
                        `;
                    } else if (node.condition_type === 'cond_bot_perm') {
                        inputHtml = `
                            <div class="space-y-1">
                                <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Bot Permission Needed</label>
                                <input type="text" 
                                       value="${node.action_val || ''}" 
                                       placeholder="e.g. ManageMessages" 
                                       oninput="updateActionVal('${node.id}', this.value)"
                                       class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-amber-500/50 font-mono">
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <label class="flex items-center gap-1 cursor-pointer select-none">
                                    <input type="checkbox" ${node.adv_invert_cond ? 'checked' : ''} onchange="updateAdvancedField('${node.id}', 'adv_invert_cond', this.checked)" class="rounded bg-slate-950 border-white/10 text-amber-600 focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                    <span class="text-[9px] text-slate-400">Invert (Checks if Bot lacks perm)</span>
                                </label>
                            </div>
                        `;
                    } else if (node.condition_type === 'cond_time_range') {
                        inputHtml = `
                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Start Time (HH:MM)</label>
                                    <input type="text" 
                                           value="${node.action_val || ''}" 
                                           placeholder="e.g. 09:00" 
                                           oninput="updateActionVal('${node.id}', this.value)"
                                           class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-amber-500/50 font-mono">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">End Time (HH:MM)</label>
                                    <input type="text" 
                                           value="${node.adv_end_time || ''}" 
                                           placeholder="e.g. 17:00" 
                                           oninput="updateAdvancedField('${node.id}', 'adv_end_time', this.value)"
                                           class="w-full bg-slate-950/70 border border-white/10 text-white rounded-xl px-3 py-1.5 text-xs outline-none focus:border-amber-500/50 font-mono">
                                </div>
                            </div>
                        `;
                        advancedHtml = `
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-wider">Days of Week (comma-separated)</label>
                                    <input type="text" value="${node.adv_days || 'Mon,Tue,Wed,Thu,Fri,Sat,Sun'}" oninput="updateAdvancedField('${node.id}', 'adv_days', this.value)" class="w-full bg-slate-950/70 border border-white/10 text-white rounded-lg px-2 py-1 text-[10px] focus:border-amber-500/50" placeholder="e.g. Mon,Wed,Fri">
                                </div>
                                <label class="flex items-center gap-1 cursor-pointer select-none">
                                    <input type="checkbox" ${node.adv_invert_cond ? 'checked' : ''} onchange="updateAdvancedField('${node.id}', 'adv_invert_cond', this.checked)" class="rounded bg-slate-950 border-white/10 text-amber-600 focus:ring-0 focus:ring-offset-0 w-3 h-3 cursor-pointer">
                                    <span class="text-[9px] text-slate-400">Invert Condition (NOT check)</span>
                                </label>
                            </div>
                        `;
                    }

                    nodeHtml = `
                        <div class="node-block absolute bg-[#0c0e22]/95 border border-amber-500/30 rounded-2xl p-4 shadow-2xl transition-shadow w-[250px] select-none pointer-events-auto" 
                             style="left: ${node.x || 300}px; top: ${node.y || 100}px;" 
                             id="node-${node.id}">

                            <div class="flex justify-between items-center border-b border-white/5 pb-2 mb-3 cursor-grab active:cursor-grabbing" 
                                 onmousedown="startDragBlock(event, '${node.id}')">
                                <span class="text-[9px] font-bold text-amber-400 uppercase tracking-widest flex items-center gap-1.5">
                                    <i data-lucide="${conditionIcon}" class="w-3.5 h-3.5 text-amber-400 shrink-0"></i> ${conditionTitle}
                                </span>
                                <div class="flex items-center gap-1">
                                    <button type="button" onclick="toggleAdvancedSettings('${node.id}')" class="text-slate-500 hover:text-amber-400 transition cursor-pointer p-1 hover:bg-white/5 rounded-lg">
                                        <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <button type="button" onclick="deleteBlock('${node.id}')" class="text-slate-500 hover:text-rose-400 transition cursor-pointer p-1 hover:bg-white/5 rounded-lg">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </div>
 
                            <div class="space-y-3 relative">
                                ${inputHtml}

                                <div id="advanced-settings-${node.id}" class="${openAdvancedSettings[node.id] ? '' : 'hidden'} border-t border-white/5 pt-2 mt-2 space-y-2">
                                    <span class="text-[8px] font-bold text-slate-500 uppercase tracking-wider block">Advanced Settings</span>
                                    ${advancedHtml}
                                </div>

                                <div class="absolute left-[-22px] top-1/2 -translate-y-1/2 w-4.5 h-4.5 rounded-full bg-amber-500 border-[3px] border-[#030307] flex items-center justify-center shadow-lg"
                                     data-port-type="input"
                                     data-node-id="${node.id}">
                                     <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                </div>

                                <div class="absolute right-[-22px] top-1/2 -translate-y-1/2 w-4.5 h-4.5 rounded-full bg-emerald-500 border-[3px] border-[#030307] cursor-pointer hover:scale-125 transition-transform flex items-center justify-center shadow-lg"
                                     onmousedown="startConnecting(event, '${node.id}')"
                                     data-port-type="output"
                                     data-node-id="${node.id}">
                                     <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                container.insertAdjacentHTML('beforeend', nodeHtml);
            });

            lucide.createIcons();
            requestAnimationFrame(drawConnections);
        }

        let currentDragNodeId = null;
        let dragStartX = 0;
        let dragStartY = 0;
        let nodeStartX = 0;
        let nodeStartY = 0;

        function startDragBlock(e, nodeId) {
            
            if (e.target.closest('input') || e.target.closest('button') || e.target.closest('.custom-select-menu') || e.target.closest('.custom-select')) {
                return;
            }
            
            currentDragNodeId = nodeId;
            dragStartX = e.clientX;
            dragStartY = e.clientY;
            
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;
            
            const node = activeFile.logic.find(n => n.id === nodeId);
            if (node) {
                nodeStartX = node.x || 100;
                nodeStartY = node.y || 100;
            }
            
            document.addEventListener('mousemove', onDragBlock);
            document.addEventListener('mouseup', stopDragBlock);
            e.preventDefault();
            e.stopPropagation();
        }

        function onDragBlock(e) {
            if (!currentDragNodeId) return;
            
            const dx = e.clientX - dragStartX;
            const dy = e.clientY - dragStartY;
            
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;
            
            const node = activeFile.logic.find(n => n.id === currentDragNodeId);
            if (node) {
                
                node.x = Math.max(10, nodeStartX + dx / zoomScale);
                node.y = Math.max(10, nodeStartY + dy / zoomScale);
                
                const nodeEl = document.getElementById(`node-${currentDragNodeId}`);
                if (nodeEl) {
                    nodeEl.style.left = `${node.x}px`;
                    nodeEl.style.top = `${node.y}px`;
                }

                drawConnections();
            }
        }

        function stopDragBlock() {
            currentDragNodeId = null;
            document.removeEventListener('mousemove', onDragBlock);
            document.removeEventListener('mouseup', stopDragBlock);
        }

        function drawConnections() {
            const svg = document.getElementById('canvas-svg');
            if (!svg) return;

            const oldPaths = svg.querySelectorAll('path:not(#temp-connection-path)');
            oldPaths.forEach(p => p.remove());
            
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;
            
            const canvasContent = document.getElementById('canvas-content');
            if (!canvasContent) return;
            const canvasRect = canvasContent.getBoundingClientRect();
            
            activeFile.logic.forEach(node => {
                if (node.type === 'trigger' || node.type === 'condition') {
                    const targetIds = node.targetActionIds || (node.targetActionId ? [node.targetActionId] : []);
                    targetIds.forEach(targetId => {
                        const fromPort = document.querySelector(`[data-port-type="output"][data-node-id="${node.id}"]`);
                        const toPort = document.querySelector(`[data-port-type="input"][data-node-id="${targetId}"]`);
                        
                        if (fromPort && toPort) {
                            const fromRect = fromPort.getBoundingClientRect();
                            const toRect = toPort.getBoundingClientRect();

                            const x1 = ((fromRect.left + fromRect.width / 2) - canvasRect.left) / zoomScale;
                            const y1 = ((fromRect.top + fromRect.height / 2) - canvasRect.top) / zoomScale;
                            const x2 = ((toRect.left + toRect.width / 2) - canvasRect.left) / zoomScale;
                            const y2 = ((toRect.top + toRect.height / 2) - canvasRect.top) / zoomScale;

                            const dx = Math.abs(x2 - x1) * 0.5;
                            const pathData = `M ${x1} ${y1} C ${x1 + dx} ${y1}, ${x2 - dx} ${y2}, ${x2} ${y2}`;
                            
                            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                            path.setAttribute('d', pathData);
                            path.setAttribute('stroke', '#5865F2');
                            path.setAttribute('stroke-width', '2.5');
                            path.setAttribute('fill', 'none');
                            path.setAttribute('marker-end', 'url(#arrow)');

                            path.style.cursor = 'pointer';
                            path.style.pointerEvents = 'auto';
                            path.addEventListener('mouseenter', () => path.setAttribute('stroke', '#ef4444'));
                            path.addEventListener('mouseleave', () => path.setAttribute('stroke', '#5865F2'));
                            path.addEventListener('dblclick', async (e) => {
                                e.stopPropagation();
                                const confirmed = await showCustomConfirm("Disconnect Blocks", "Are you sure you want to disconnect these blocks?", false);
                                if (confirmed) {
                                    if (node.targetActionIds) {
                                        node.targetActionIds = node.targetActionIds.filter(id => id !== targetId);
                                    } else {
                                        node.targetActionId = null;
                                    }
                                    renderActiveFileCanvas();
                                }
                            });
                            
                            svg.appendChild(path);
                        }
                    });
                }
            });
        }

        let connectingFromNodeId = null;
        let tempPath = null;

        function startConnecting(e, nodeId) {
            connectingFromNodeId = nodeId;
            tempPath = document.getElementById('temp-connection-path');
            if (tempPath) {
                tempPath.classList.remove('hidden');
            }
            document.addEventListener('mousemove', onConnectingMove);
            document.addEventListener('mouseup', stopConnecting);
            e.preventDefault();
            e.stopPropagation();
        }

        function onConnectingMove(e) {
            if (!connectingFromNodeId || !tempPath) return;
            
            const fromPort = document.querySelector(`[data-port-type="output"][data-node-id="${connectingFromNodeId}"]`);
            const canvasContent = document.getElementById('canvas-content');
            if (!fromPort || !canvasContent) return;
            
            const canvasRect = canvasContent.getBoundingClientRect();
            const fromRect = fromPort.getBoundingClientRect();
            
            const x1 = ((fromRect.left + fromRect.width / 2) - canvasRect.left) / zoomScale;
            const y1 = ((fromRect.top + fromRect.height / 2) - canvasRect.top) / zoomScale;

            const x2 = (e.clientX - canvasRect.left) / zoomScale;
            const y2 = (e.clientY - canvasRect.top) / zoomScale;
            
            const dx = Math.abs(x2 - x1) * 0.5;
            const pathData = `M ${x1} ${y1} C ${x1 + dx} ${y1}, ${x2 - dx} ${y2}, ${x2} ${y2}`;
            tempPath.setAttribute('d', pathData);
        }

        function stopConnecting(e) {
            document.removeEventListener('mousemove', onConnectingMove);
            document.removeEventListener('mouseup', stopConnecting);
            
            if (tempPath) {
                tempPath.classList.add('hidden');
                tempPath.setAttribute('d', '');
            }

            const target = e.target.closest('[data-port-type="input"]');
            if (target && connectingFromNodeId) {
                const targetNodeId = target.getAttribute('data-node-id');
                const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
                if (activeFile && activeFile.logic) {
                    const sourceNode = activeFile.logic.find(n => n.id === connectingFromNodeId);
                    if (sourceNode && (sourceNode.type === 'trigger' || sourceNode.type === 'condition')) {
                        if (!sourceNode.targetActionIds) {
                            sourceNode.targetActionIds = sourceNode.targetActionId ? [sourceNode.targetActionId] : [];
                            delete sourceNode.targetActionId;
                        }
                        if (!sourceNode.targetActionIds.includes(targetNodeId)) {
                            sourceNode.targetActionIds.push(targetNodeId);
                        }
                    }
                }
            }
            
            connectingFromNodeId = null;
            renderActiveFileCanvas();
        }

        function addNewBlock(blockType, subType, x = null, y = null) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || activeFile.type !== 'file') return;

            if (!activeFile.logic) activeFile.logic = [];
            
            const id = (blockType === 'trigger' ? 'trg_' : (blockType === 'condition' ? 'cnd_' : 'act_')) + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            
            let cx, cy;
            if (x !== null && y !== null) {
                cx = x;
                cy = y;
            } else {
                
                const viewport = document.getElementById('canvas-viewport');
                cx = 100;
                cy = 100;
                if (viewport) {
                    const rect = viewport.getBoundingClientRect();
                    cx = (rect.width / 2 - panX - 100) / zoomScale;
                    cy = (rect.height / 2 - panY - 50) / zoomScale;
                }
            }

            let newBlock;
            if (blockType === 'trigger') {
                newBlock = {
                    id: id,
                    type: 'trigger',
                    event: subType || 'message',
                    trigger_val: '',
                    targetActionId: null,
                    x: Math.max(10, cx),
                    y: Math.max(10, cy)
                };
            } else if (blockType === 'condition') {
                newBlock = {
                    id: id,
                    type: 'condition',
                    condition_type: subType || 'cond_has_role',
                    action_val: '',
                    targetActionId: null,
                    x: Math.max(10, cx),
                    y: Math.max(10, cy)
                };
            } else {
                newBlock = {
                    id: id,
                    type: 'action',
                    action_type: subType || 'reply',
                    action_val: '',
                    channel_val: subType === 'reply' ? '#general' : '',
                    x: Math.max(10, cx),
                    y: Math.max(10, cy)
                };
            }

            activeFile.logic.push(newBlock);
            renderActiveFileCanvas();

            document.querySelectorAll('.custom-select-menu').forEach(el => el.classList.add('hidden'));
        }

        function initializeToolboxDragAndDrop() {
            const menus = ['menu-add-event', 'menu-add-condition', 'menu-add-action', 'menu-add-premium-action'];
            menus.forEach(menuId => {
                const menuEl = document.getElementById(menuId);
                if (!menuEl) return;
                
                const items = menuEl.querySelectorAll('div[onclick]');
                items.forEach(item => {
                    const clickAttr = item.getAttribute('onclick');
                    if (!clickAttr) return;

                    const match = clickAttr.match(/addNewBlock\(\s*'([^'\s]+)'\s*,\s*'([^'\s]+)'\s*\)/);
                    if (match) {
                        const blockType = match[1];
                        const subType = match[2];
                        
                        item.setAttribute('draggable', 'true');
                        item.classList.add('select-none', 'cursor-grab', 'active:cursor-grabbing');
                        
                        item.addEventListener('dragstart', (e) => {
                            e.dataTransfer.setData('text/blockType', blockType);
                            e.dataTransfer.setData('text/subType', subType);
                            e.dataTransfer.effectAllowed = 'copy';

                            item.classList.add('opacity-40');
                        });
                        item.addEventListener('dragend', () => {
                            item.classList.remove('opacity-40');
                        });
                    }
                });
            });
        }

        async function deleteBlock(id) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;

            const confirmed = await showCustomConfirm("Delete Block", "Are you sure you want to delete this block?", true);
            if (confirmed) {
                activeFile.logic = activeFile.logic.filter(n => n.id !== id);

                activeFile.logic.forEach(n => {
                    if (n.type === 'trigger' || n.type === 'condition') {
                        if (n.targetActionIds) {
                            n.targetActionIds = n.targetActionIds.filter(tid => tid !== id);
                        }
                        if (n.targetActionId === id) {
                            n.targetActionId = null;
                        }
                    }
                });
                
                renderActiveFileCanvas();
            }
        }

        function updateTriggerType(id, type) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;

            const trg = activeFile.logic.find(t => t.id === id);
            if (trg) {
                trg.event = type;
                if (['join', 'reaction'].includes(type)) {
                    trg.trigger_val = '';
                }
                renderActiveFileCanvas();
            }
        }

        function updateTriggerVal(id, val) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;

            const trg = activeFile.logic.find(t => t.id === id);
            if (trg) {
                trg.trigger_val = val;
            }
        }

        function updateActionType(id, actionType) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;

            const trg = activeFile.logic.find(t => t.id === id);
            if (trg) {
                trg.action_type = actionType;
                if (actionType === 'role') {
                    trg.channel_val = '';
                }
                renderActiveFileCanvas();
            }
        }

        function updateChannelVal(id, val) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;

            const trg = activeFile.logic.find(t => t.id === id);
            if (trg) {
                trg.channel_val = val;
            }
        }

        function updateActionVal(id, val) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;

            const trg = activeFile.logic.find(t => t.id === id);
            if (trg) {
                trg.action_val = val;
            }
        }

        function updateEmbedField(id, field, val) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;

            const trg = activeFile.logic.find(t => t.id === id);
            if (trg) {
                trg[field] = val;
            }
        }

        function addCommandParam(nodeId) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;

            const trg = activeFile.logic.find(t => t.id === nodeId);
            if (trg) {
                if (!trg.command_params) trg.command_params = [];
                trg.command_params.push({ name: '', type: 'string', required: false });
                renderActiveFileCanvas();
            }
        }

        function updateCommandParam(nodeId, index, key, value) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;

            const trg = activeFile.logic.find(t => t.id === nodeId);
            if (trg && trg.command_params && trg.command_params[index]) {
                trg.command_params[index][key] = value;
            }
        }

        function deleteCommandParam(nodeId, index) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;

            const trg = activeFile.logic.find(t => t.id === nodeId);
            if (trg && trg.command_params) {
                trg.command_params.splice(index, 1);
                renderActiveFileCanvas();
            }
        }

        function updateAdvancedField(nodeId, key, value) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;

            const node = activeFile.logic.find(n => n.id === nodeId);
            if (node) {
                node[key] = value;
            }
        }

        function addCommandLocalization(nodeId, locale) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;
            const node = activeFile.logic.find(n => n.id === nodeId);
            if (node) {
                node.localizations = node.localizations || {};
                node.localizations[locale] = { name: '', desc: '' };
                renderActiveFileCanvas();
            }
        }

        function removeCommandLocalization(nodeId, locale) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;
            const node = activeFile.logic.find(n => n.id === nodeId);
            if (node && node.localizations) {
                delete node.localizations[locale];
                renderActiveFileCanvas();
            }
        }

        function updateCommandLocalization(nodeId, locale, field, value) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;
            const node = activeFile.logic.find(n => n.id === nodeId);
            if (node && node.localizations && node.localizations[locale]) {
                node.localizations[locale][field] = value;
            }
        }

        function addParamLocalization(nodeId, paramIndex, locale) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;
            const node = activeFile.logic.find(n => n.id === nodeId);
            if (node && node.command_params && node.command_params[paramIndex]) {
                const param = node.command_params[paramIndex];
                param.localizations = param.localizations || {};
                param.localizations[locale] = { name: '', desc: '' };
                renderActiveFileCanvas();
            }
        }

        function removeParamLocalization(nodeId, paramIndex, locale) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;
            const node = activeFile.logic.find(n => n.id === nodeId);
            if (node && node.command_params && node.command_params[paramIndex]) {
                const param = node.command_params[paramIndex];
                if (param.localizations) {
                    delete param.localizations[locale];
                    renderActiveFileCanvas();
                }
            }
        }

        function updateParamLocalization(nodeId, paramIndex, locale, field, value) {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || !activeFile.logic) return;
            const node = activeFile.logic.find(n => n.id === nodeId);
            if (node && node.command_params && node.command_params[paramIndex]) {
                const param = node.command_params[paramIndex];
                if (param.localizations && param.localizations[locale]) {
                    param.localizations[locale][field] = value;
                }
            }
        }

        async function clearActiveFileCanvas() {
            const activeFile = activeFileId ? findNodeById(workspaceState.tree, activeFileId) : null;
            if (!activeFile || activeFile.type !== 'file') return;
            
            const confirmed = await showCustomConfirm(
                "Clear Workspace",
                "Are you sure you want to delete all blocks in this file? This cannot be undone.",
                true
            );
            if (confirmed) {
                activeFile.logic = [];
                renderActiveFileCanvas();
            }
        }

        async function confirmDeleteBot() {
            const confirmed = await showCustomConfirm(
                "Delete Bot",
                "Are you sure you want to delete this bot? This action is permanent and cannot be undone.",
                true
            );
            if (confirmed) {
                document.getElementById('delete-bot-form').submit();
            }
        }
    </script>
    
    <div id="custom-prompt-modal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/80 backdrop-blur-md transition-opacity duration-300 opacity-0">
        <div class="cyber-card w-full max-w-md p-6 rounded-2xl border border-purple-500/30 shadow-2xl transform scale-95 transition-transform duration-300 space-y-6">
            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                <h3 id="prompt-modal-title" class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4 text-purple-400"></i> Create Node
                </h3>
                <button type="button" onclick="closePromptModal()" class="text-slate-500 hover:text-white transition">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label id="prompt-modal-label" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Name</label>
                    <input type="text" id="prompt-modal-input" placeholder="e.g. commands.bot" class="w-full bg-slate-950/70 border border-white/10 focus:border-purple-500/50 text-white rounded-xl px-4 py-3 text-xs outline-none transition font-mono">
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closePromptModal()" class="px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 text-xs font-bold text-slate-300 hover:text-white transition cursor-pointer">
                    Cancel
                </button>
                <button type="button" id="prompt-modal-submit" class="neon-glow-btn text-white text-xs font-bold px-5 py-2.5 rounded-xl cursor-pointer">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <div id="custom-confirm-modal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/80 backdrop-blur-md transition-opacity duration-300 opacity-0">
        <div class="cyber-card w-full max-w-md p-6 rounded-2xl border border-rose-500/30 shadow-2xl transform scale-95 transition-transform duration-300 space-y-6">
            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                <h3 id="confirm-modal-title" class="text-xs font-bold text-rose-400 uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-400"></i> Warning
                </h3>
                <button type="button" onclick="closeConfirmModal()" class="text-slate-500 hover:text-white transition">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <p id="confirm-modal-message" class="text-xs text-slate-300 leading-relaxed"></p>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeConfirmModal()" class="px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 text-xs font-bold text-slate-300 hover:text-white transition cursor-pointer">
                    Cancel
                </button>
                <button type="button" id="confirm-modal-submit" class="px-5 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold cursor-pointer transition shadow-lg shadow-rose-500/25">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <div id="premium-gate-modal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/85 backdrop-blur-md transition-opacity duration-300 opacity-0">
        <div class="cyber-card w-full max-w-md p-6 rounded-2xl border border-purple-500/40 shadow-2xl transform scale-95 transition-transform duration-300 space-y-6">
            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                <h3 id="premium-gate-title" class="text-xs font-bold text-purple-400 uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="crown" class="w-4 h-4 text-purple-400"></i> Premium Upgrade
                </h3>
                <button type="button" onclick="closePremiumGate()" class="text-slate-500 hover:text-white transition">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="space-y-4">
                <p id="premium-gate-message" class="text-xs text-slate-300 leading-relaxed"></p>
                <div class="bg-purple-950/20 border border-purple-500/15 rounded-xl p-3.5 flex items-center gap-3">
                    <i data-lucide="sparkles" class="w-5 h-5 text-purple-400 shrink-0"></i>
                    <p class="text-[10px] text-purple-300 leading-normal">Unlocks all voice music features, larger logic trees, and 24/7 dedicated hosting!</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closePremiumGate()" class="px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 text-xs font-bold text-slate-300 hover:text-white transition cursor-pointer">
                    Close
                </button>
                <a href="{{ route('pricing') }}" class="neon-glow-btn text-white text-xs font-bold px-5 py-2.5 rounded-xl cursor-pointer flex items-center gap-1.5 shadow-lg shadow-purple-500/25">
                    <i data-lucide="crown" class="w-3.5 h-3.5"></i> Upgrade Now
                </a>
            </div>
        </div>
    </div>

    <div id="bot-tutorial-modal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/85 backdrop-blur-md transition-opacity duration-300 opacity-0">
        <div class="cyber-card w-full max-w-2xl p-6 rounded-2xl border border-[#5865F2]/40 shadow-2xl transform scale-95 transition-transform duration-300 space-y-6 max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                <h3 class="text-xs font-bold text-white uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="help-circle" class="w-4 h-4 text-[#5865F2]"></i> How to create & invite your Discord Bot
                </h3>
                <button type="button" onclick="closeTutorialModal()" class="text-slate-500 hover:text-white transition">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            
            <div class="space-y-6 text-xs text-slate-300 leading-relaxed">
                
                <div class="flex gap-4">
                    <span class="w-6 h-6 rounded-full bg-[#5865F2]/20 border border-[#5865F2]/40 text-[#5865F2] flex items-center justify-center font-bold shrink-0">1</span>
                    <div class="space-y-1">
                        <h4 class="font-bold text-white uppercase tracking-wider text-[10px]">Create an Application</h4>
                        <p>Go to the <a href="https://discord.com/developers/applications" target="_blank" class="text-[#5865F2] hover:underline font-bold font-sans">Discord Developer Portal</a>, log in, and click the <strong class="text-white">"New Application"</strong> button. Give it a name (e.g., "My HublyBot").</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <span class="w-6 h-6 rounded-full bg-[#5865F2]/20 border border-[#5865F2]/40 text-[#5865F2] flex items-center justify-center font-bold shrink-0">2</span>
                    <div class="space-y-1">
                        <h4 class="font-bold text-white uppercase tracking-wider text-[10px]">Create a Bot & Get Token</h4>
                        <p>Navigate to the <strong class="text-white">"Bot"</strong> tab in the left menu. Click <strong class="text-white">"Add Bot"</strong>. Click <strong class="text-white">"Reset Token"</strong>, copy the secret token, and paste it into the "Discord Bot Token" input field in HublyBot.</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <span class="w-6 h-6 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center font-bold shrink-0">3</span>
                    <div class="space-y-1">
                        <h4 class="font-bold text-amber-400 uppercase tracking-wider text-[10px]">Enable Privileged Gateway Intents</h4>
                        <p>In the same <strong class="text-white">"Bot"</strong> tab, scroll down to <strong class="text-white">"Privileged Gateway Intents"</strong>. You must enable <strong class="text-amber-400 font-bold">"Server Members Intent"</strong> and <strong class="text-amber-400 font-bold">"Message Content Intent"</strong> so HublyBot can read command keywords and welcome members.</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <span class="w-6 h-6 rounded-full bg-[#5865F2]/20 border border-[#5865F2]/40 text-[#5865F2] flex items-center justify-center font-bold shrink-0">4</span>
                    <div class="space-y-1">
                        <h4 class="font-bold text-white uppercase tracking-wider text-[10px]">Generate Invite Link</h4>
                        <p>Go to the <strong class="text-white">"OAuth2"</strong> tab, click <strong class="text-white">"URL Generator"</strong>. Under scopes, select <strong class="text-white">"bot"</strong> and <strong class="text-white">"applications.commands"</strong>. Under bot permissions, check <strong class="text-white">"Administrator"</strong> or permissions you need.</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <span class="w-6 h-6 rounded-full bg-[#5865F2]/20 border border-[#5865F2]/40 text-[#5865F2] flex items-center justify-center font-bold shrink-0">5</span>
                    <div class="space-y-1">
                        <h4 class="font-bold text-white uppercase tracking-wider text-[10px]">Invite the Bot</h4>
                        <p>Copy the generated URL at the bottom, paste it into a new browser tab, select your Discord server, and click "Authorize" to add the bot!</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="button" onclick="closeTutorialModal()" class="px-5 py-2.5 rounded-xl bg-[#5865F2] hover:bg-indigo-600 text-white text-xs font-bold transition shadow-lg shadow-purple-500/25">
                    Got it, thanks!
                </button>
            </div>
        </div>
    </div>

    <script>
        function openTutorialModal() {
            const modal = document.getElementById('bot-tutorial-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('.cyber-card').classList.remove('scale-95');
            }, 10);
            lucide.createIcons();
        }

        function closeTutorialModal() {
            const modal = document.getElementById('bot-tutorial-modal');
            modal.classList.add('opacity-0');
            modal.querySelector('.cyber-card').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        function showPremiumGate(featureName, description) {
            const modal = document.getElementById('premium-gate-modal');
            const messageEl = document.getElementById('premium-gate-message');
            messageEl.innerHTML = `<strong>${featureName}</strong> is a Premium feature.<br><br>${description}`;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('.cyber-card').classList.remove('scale-95');
            }, 10);
            
            lucide.createIcons();
        }

        function closePremiumGate() {
            const modal = document.getElementById('premium-gate-modal');
            modal.classList.add('opacity-0');
            modal.querySelector('.cyber-card').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }
    </script>

    @if($tab === 'hosting')
    <script>
        let botStatus = '{{ isset($isRunning) && $isRunning ? "online" : "offline" }}';
        let uptimeSeconds = {{ isset($uptimeSeconds) ? $uptimeSeconds : 0 }};
        let uptimeInterval = null;
        let logsArray = [];
        let lastLogContent = '';

        function formatTime(sec) {
            const days = Math.floor(sec / (3600*24));
            const hours = Math.floor((sec % (3600*24)) / 3600);
            const minutes = Math.floor((sec % 3600) / 60);
            const seconds = sec % 60;
            return `${days}d ${hours}h ${minutes}m ${seconds}s`;
        }

        function parseAndRenderLogs(logText) {
            if (logText === lastLogContent) return;
            lastLogContent = logText;

            const terminal = document.getElementById('terminal-body');
            if (!terminal) return;

            terminal.innerHTML = '';
            logsArray = logText.split('\n').filter(line => line.trim() !== '');

            let lastKnownTime = '00:00:00';
            logsArray.forEach(line => {
                
                if (line.includes('DiscordPHP.DEBUG') || line.includes('http not checking') || line.includes('BUCKET') || line.includes('REQ GET') || line.includes('queued REQ')) {
                    return;
                }

                if (line.includes('wmic') || line.includes('n’est pas reconnu') || line.includes('preg_match_all') || line.includes('PHP Deprecated')) {
                    return;
                }

                let time = '00:00:00';
                const timeMatch = line.match(/(\d{2}):(\d{2}):(\d{2})/);
                if (timeMatch) {
                    time = timeMatch[0];
                    lastKnownTime = time;
                } else {
                    time = lastKnownTime;
                }

                let type = 'SYSTEM';
                let message = line;

                const customMatch = line.match(/^\[.*?\] \[(.*?)\] (.*)$/);
                if (customMatch) {
                    type = customMatch[1];
                    message = customMatch[2];
                } else {
                    
                    const dpMatch = line.match(/DiscordPHP\.(.*?):\s*(.*)$/);
                    if (dpMatch) {
                        type = dpMatch[1];
                        message = dpMatch[2];
                    }
                }

                type = type.toUpperCase();
                if (type === 'INFO') type = 'SYSTEM';

                let colorClass = 'text-slate-400';
                if (type === 'SYSTEM') colorClass = 'text-emerald-400';
                else if (type === 'DISCORD') colorClass = 'text-indigo-400';
                else if (type === 'EVENT') colorClass = 'text-blue-400';
                else if (type === 'LOGIC') colorClass = 'text-amber-400';
                else if (type === 'WARNING') colorClass = 'text-amber-500 font-semibold';
                else if (type === 'ERROR') colorClass = 'text-rose-400 font-bold';

                const div = document.createElement('div');
                div.className = 'flex items-start gap-2 border-l border-white/5 pl-2 hover:bg-white/5 py-0.5 rounded transition';
                div.innerHTML = `
                    <span class="text-slate-600 select-none shrink-0 font-mono text-[9px] mt-0.5">${time}</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider ${colorClass} bg-slate-950/80 border border-white/10 shrink-0 font-mono">${type}</span>
                    <span class="text-slate-300 font-mono break-all">${escapeHtml(message)}</span>
                `;
                terminal.appendChild(div);
            });

            terminal.scrollTop = terminal.scrollHeight;
        }

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function clearConsole() {
            const terminal = document.getElementById('terminal-body');
            if (terminal) terminal.innerHTML = '';
        }

        function downloadLogs() {
            if (logsArray.length === 0) return;
            const text = logsArray.join('\n');
            const blob = new Blob([text], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `bot-logs-${new Date().toISOString().substring(0,10)}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function updateTelemetry() {
            const cpuValEl = document.getElementById('metric-cpu');
            const cpuBarEl = document.getElementById('bar-cpu');
            const ramValEl = document.getElementById('metric-ram');
            const ramBarEl = document.getElementById('bar-ram');
            const uptimeEl = document.getElementById('metric-uptime');

            const freeSessionTimeEl = document.getElementById('free-session-time');
            const freeSessionBarEl = document.getElementById('bar-free-session');

            if (botStatus === 'online') {
                const maxCpu = isPremiumUser ? 1.00 : 0.10;
                const maxRam = isPremiumUser ? 1024 : 128;
                
                const cpu = (isPremiumUser ? (0.05 + Math.random() * 0.15) : (0.01 + Math.random() * 0.04)).toFixed(3);
                const cpuPercent = Math.round((cpu / maxCpu) * 100);
                const ram = (isPremiumUser ? (85.2 + Math.random() * 12.4) : (28.4 + Math.random() * 4.2)).toFixed(1);
                const ramPercent = Math.round((ram / maxRam) * 100);

                if (cpuValEl) cpuValEl.innerText = `${cpu} / ${maxCpu.toFixed(2)} vCPU (${cpuPercent}%)`;
                if (cpuBarEl) cpuBarEl.style.width = `${cpuPercent}%`;
                if (ramValEl) ramValEl.innerText = `${ram} MB / ${maxRam} MB (${ramPercent}%)`;
                if (ramBarEl) ramBarEl.style.width = `${ramPercent}%`;
                
                uptimeSeconds++;
                if (uptimeEl) uptimeEl.innerText = formatTime(uptimeSeconds);

                if (freeSessionTimeEl && freeSessionBarEl) {
                    const remaining = Math.max(0, 7200 - uptimeSeconds);
                    const hours = Math.floor(remaining / 3600);
                    const minutes = Math.floor((remaining % 3600) / 60);
                    const seconds = remaining % 60;
                    freeSessionTimeEl.innerText = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    
                    const pct = (remaining / 7200) * 100;
                    freeSessionBarEl.style.width = `${pct}%`;

                    if (remaining <= 0) {
                        botStatus = 'offline';
                        setStatusUI('offline');
                        checkStatus(); 
                    }
                }
            } else if (botStatus === 'offline') {
                const maxCpu = isPremiumUser ? 1.00 : 0.10;
                const maxRam = isPremiumUser ? 1024 : 128;
                if (cpuValEl) cpuValEl.innerText = `0.00 / ${maxCpu.toFixed(2)} vCPU (0%)`;
                if (cpuBarEl) cpuBarEl.style.width = `0%`;
                if (ramValEl) ramValEl.innerText = `0.0 MB / ${maxRam} MB (0%)`;
                if (ramBarEl) ramBarEl.style.width = `0%`;
                if (uptimeEl) uptimeEl.innerText = `0d 0h 0m 0s`;

                if (freeSessionTimeEl && freeSessionBarEl) {
                    freeSessionTimeEl.innerText = "02:00:00";
                    freeSessionBarEl.style.width = "100%";
                }
            } else if (botStatus === 'restarting') {
                const maxCpu = isPremiumUser ? 1.00 : 0.10;
                const maxRam = isPremiumUser ? 1024 : 128;
                const cpu = isPremiumUser ? 0.32 : 0.08;
                const cpuPercent = Math.round((cpu / maxCpu) * 100);
                const ram = isPremiumUser ? 145.2 : 45.2;
                const ramPercent = Math.round((ram / maxRam) * 100);
                if (cpuValEl) cpuValEl.innerText = `${cpu.toFixed(2)} / ${maxCpu.toFixed(2)} vCPU (${cpuPercent}%)`;
                if (cpuBarEl) cpuBarEl.style.width = `${cpuPercent}%`;
                if (ramValEl) ramValEl.innerText = `${ram} MB / ${maxRam} MB (${ramPercent}%)`;
                if (ramBarEl) ramBarEl.style.width = `${ramPercent}%`;
            }
        }

        function selectRegion(regionName) {
            if (regionName.includes('Premium') && !isPremiumUser) {
                showPremiumGate('Premium Regions', 'Dedicated European and American cluster nodes are reserved for premium plan subscribers.');
                return;
            }
            const label = document.getElementById('label-server-region');
            if (label) label.innerText = regionName.split('(')[0].trim();
            const menu = document.getElementById('menu-server-region');
            if (menu) menu.classList.add('hidden');
        }

        function toggleAutoRestart(checkbox) {
            if (!isPremiumUser) {
                checkbox.checked = true;
                showPremiumGate('Advanced Process Recovery', 'Auto-recovery configurations, resource capping restarts, and clustering are premium benefits.');
            }
        }

        function setStatusUI(status, pidVal = 'N/A') {
            const dot = document.getElementById('hosting-status-ping');
            const text = document.getElementById('hosting-status-text');
            const btnStart = document.getElementById('btn-start');
            const btnStop = document.getElementById('btn-stop');
            const btnRestart = document.getElementById('btn-restart');
            const pidEl = document.getElementById('hosting-pid');

            if (pidEl) pidEl.innerText = pidVal;

            if (status === 'online') {
                dot.innerHTML = `
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                `;
                text.className = 'text-xs font-bold text-emerald-400 tracking-wide uppercase';
                text.innerText = 'Online / Running';
                
                btnStart.disabled = true;
                btnStop.disabled = false;
                btnRestart.disabled = false;
            } else if (status === 'offline') {
                dot.innerHTML = `
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                `;
                text.className = 'text-xs font-bold text-rose-500 tracking-wide uppercase';
                text.innerText = 'Offline / Stopped';
                
                btnStart.disabled = false;
                btnStop.disabled = true;
                btnRestart.disabled = true;
            } else if (status === 'restarting') {
                dot.innerHTML = `
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                `;
                text.className = 'text-xs font-bold text-amber-400 tracking-wide uppercase';
                text.innerText = 'Restarting Container';
                
                btnStart.disabled = true;
                btnStop.disabled = true;
                btnRestart.disabled = true;
            }
        }

        async function controlBot(action) {
            const botId = "{{ $bot->id }}";
            setStatusUI('restarting');
            botStatus = 'restarting';

            try {
                const response = await fetch(`/dashboard/bots/${botId}/${action}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    await checkStatus();
                    await fetchLogs();
                }
            } catch (err) {
                console.error("Failed to control bot:", err);
            }
        }

        async function checkStatus() {
            const botId = "{{ $bot->id }}";
            try {
                const response = await fetch(`/dashboard/bots/${botId}/status`);
                const data = await response.json();
                
                if (botStatus !== 'restarting') {
                    botStatus = data.status;
                    uptimeSeconds = data.uptime;
                    setStatusUI(data.status, data.pid);
                }
            } catch (err) {
                console.error("Failed to check bot status:", err);
            }
        }

        async function fetchLogs() {
            const botId = "{{ $bot->id }}";
            try {
                const response = await fetch(`/dashboard/bots/${botId}/logs`);
                const data = await response.json();
                parseAndRenderLogs(data.logs || '');
            } catch (err) {
                console.error("Failed to fetch logs:", err);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            checkStatus();
            fetchLogs();

            uptimeInterval = setInterval(updateTelemetry, 1000);

            setInterval(checkStatus, 2500);

            setInterval(() => {
                if (botStatus === 'online' || botStatus === 'restarting') {
                    fetchLogs();
                }
            }, 1500);
        });
    </script>
    @endif
</body>
</html>
