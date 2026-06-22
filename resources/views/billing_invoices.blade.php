<!DOCTYPE html>
<html lang="en" class="scroll-smooth bg-[#030307]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing & Invoices - HublyBot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
    </style>
</head>
<body class="bg-[#030307] text-slate-100 font-sans min-h-screen flex relative overflow-x-hidden selection:bg-purple-500 selection:text-white">

    <!-- Glowing ambient blobs -->
    <div class="fixed top-[-20%] left-[-10%] w-[60vw] h-[60vw] rounded-full bg-purple-950/10 blur-[130px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-20%] right-[-10%] w-[60vw] h-[60vw] rounded-full bg-blue-950/10 blur-[130px] pointer-events-none z-0"></div>

    <!-- Cyber grid overlay -->
    <div class="fixed inset-0 cyber-grid pointer-events-none z-0"></div>

    <!-- Mobile Sidebar Toggle -->
    <button onclick="toggleSidebar()" class="fixed top-4 left-4 z-50 p-2.5 rounded-xl bg-[#090a14] border border-white/10 text-white lg:hidden cursor-pointer shadow-lg">
        <i data-lucide="menu" id="toggle-icon" class="w-5 h-5"></i>
    </button>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-[#080914]/90 border-r border-white/5 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col justify-between p-6 backdrop-blur-xl">
        <div class="space-y-8">
            <!-- Logo Section -->
            <div class="flex items-center gap-2.5 pt-4 lg:pt-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <span class="text-lg font-bold tracking-tight text-white">Hubly<span class="text-[#5865F2]">Bot</span></span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1.5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border {{ Request::is('dashboard') ? 'bg-[#5865F2]/10 border-[#5865F2]/25 text-white' : 'border-transparent text-slate-400 hover:text-white hover:bg-white/5' }} text-xs font-bold transition">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 {{ Request::is('dashboard') ? 'text-[#5865F2]' : '' }}"></i> Welcome
                </a>

                <!-- My Bots Dropdown Menu -->
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

                <!-- Billing Dropdown Menu -->
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

        <!-- User profile footer -->
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

    <!-- Sidebar Overlay (mobile) -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 z-30 hidden transition-opacity lg:hidden"></div>

    <!-- Main Content Area -->
    <main class="flex-grow lg:pl-64 min-h-screen flex flex-col z-10 relative">
        <div class="flex-grow p-6 lg:p-10 max-w-4xl w-full mx-auto space-y-8 pt-20 lg:pt-10">
            
            <!-- Success/Error Alerts -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs flex items-center gap-2 mb-4">
                    <i data-lucide="check-circle" class="w-4.5 h-4.5"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs flex items-center gap-2 mb-4">
                    <i data-lucide="alert-circle" class="w-4.5 h-4.5"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Premium Header with Stats -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-black text-white uppercase tracking-wider">Invoices</h2>
                    <p class="text-xs text-slate-400">View and download your past transaction receipts.</p>
                </div>
                @if (!empty($invoices) && count($invoices) > 0)
                    <div class="flex items-center gap-4 bg-[#0a0b16] border border-white/5 p-4 rounded-2xl">
                        <div class="w-10 h-10 rounded-xl bg-[#5865F2]/10 border border-[#5865F2]/25 flex items-center justify-center">
                            <i data-lucide="file-check" class="w-5 h-5 text-[#5865F2]"></i>
                        </div>
                        <div>
                            <p class="text-[9px] text-slate-400 uppercase font-black tracking-widest">Total Spent</p>
                            <p class="text-sm font-black text-white">${{ number_format(collect($invoices)->sum(function($i) { return floatval(str_replace(',', '', $i['amount'])); }), 2) }} <span class="text-[10px] text-slate-400">USD</span></p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Simplified Invoices List -->
            <div class="cyber-card rounded-2xl overflow-hidden">
                <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-400">All Transactions</h3>
                    <span class="text-[10px] text-slate-500 font-bold">Showing latest {{ count($invoices) }} receipts</span>
                </div>
                @if (!empty($invoices) && count($invoices) > 0)
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-white/5 text-slate-400 font-bold uppercase tracking-wider text-[9px] bg-white/[0.01]">
                                    <th style="padding: 10px 24px;">Invoice ID</th>
                                    <th style="padding: 10px 24px;">Billing Date</th>
                                    <th style="padding: 10px 24px;">Amount</th>
                                    <th style="padding: 10px 24px;">Status</th>
                                    <th style="padding: 10px 24px;" class="text-right">Receipt</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach ($invoices as $invoice)
                                    <tr class="hover:bg-white/[0.02] transition-colors duration-150">
                                        <td style="padding: 10px 24px;" class="font-semibold text-white">
                                            {{ $invoice['number'] }}
                                        </td>
                                        <td style="padding: 10px 24px;" class="text-slate-300">{{ $invoice['date'] }}</td>
                                        <td style="padding: 10px 24px;" class="font-black text-slate-100">${{ $invoice['amount'] }} <span class="text-[10px] text-slate-400">{{ $invoice['currency'] }}</span></td>
                                        <td style="padding: 10px 24px;">
                                            @if ($invoice['status'] === 'paid')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] font-extrabold uppercase tracking-wide">
                                                    Paid
                                                </span>
                                            @elseif ($invoice['status'] === 'open')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[9px] font-extrabold uppercase tracking-wide">
                                                    Unpaid
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-500/10 border border-slate-500/20 text-slate-400 text-[9px] font-extrabold uppercase tracking-wide">
                                                    {{ $invoice['status'] }}
                                                </span>
                                            @endif
                                        </td>
                                        <td style="padding: 10px 24px;" class="text-right">
                                            @if (!empty($invoice['pdf']))
                                                <a href="{{ $invoice['pdf'] }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white/5 border border-white/10 hover:bg-[#5865F2]/10 hover:border-[#5865F2]/25 text-[10px] font-bold text-slate-300 hover:text-white transition-all cursor-pointer">
                                                    Download PDF <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                                </a>
                                            @else
                                                <span class="text-[10px] text-slate-500 italic">Not available</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center py-12">
                        <div class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="file-x" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <p class="text-xs text-slate-300 font-bold mb-1">No invoices found</p>
                        <p class="text-[10px] text-slate-500">You do not have any transaction history yet.</p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Footer -->
        <footer class="w-full bg-[#020204]/50 border-t border-white/5 py-6 text-center text-[10px] text-slate-500">
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
</body>
</html>
