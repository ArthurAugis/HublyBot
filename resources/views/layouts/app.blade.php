<!DOCTYPE html>
<html lang="en" class="scroll-smooth bg-[#030307]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Create, configure, and host your Discord bot without code. HublyBot is the ultimate no-code platform for Discord communities.">
    <title>@yield('title', 'HublyBot - Create your Discord Bot Without Code')</title>
    
    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Extra styling for cyber grid, gradients & custom scrollbars -->
    <style>
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #030307;
        }
        ::-webkit-scrollbar-thumb {
            background: #141424;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #24243e;
        }
        
        .cyber-grid {
            background-image: linear-gradient(rgba(88, 101, 242, 0.05) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(88, 101, 242, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            background-position: center top;
        }
        
        .floating-nav {
            background: rgba(8, 9, 20, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(88, 101, 242, 0.15);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.4), 
                        inset 0 0 12px rgba(88, 101, 242, 0.05);
        }

        .cyber-card {
            background: rgba(10, 11, 22, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.03);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            transition: border-color 0.2s ease;
        }
        .cyber-card:hover {
            border-color: rgba(88, 101, 242, 0.2);
        }

        .neon-glow-btn {
            background: linear-gradient(135deg, #5865F2 0%, #7c3aed 100%);
            transition: opacity 0.2s ease;
        }
        .neon-glow-btn:hover {
            opacity: 0.9;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(1deg); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-[#030307] text-slate-100 font-sans min-h-screen flex flex-col antialiased selection:bg-purple-500 selection:text-white relative">

    <!-- Glowing cyber ambient blobs -->
    <div class="fixed top-[-20%] left-[-10%] w-[60vw] h-[60vw] rounded-full bg-purple-950/10 blur-[130px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-20%] right-[-10%] w-[60vw] h-[60vw] rounded-full bg-blue-950/10 blur-[130px] pointer-events-none z-0"></div>

    <!-- Cyber grid pattern overlay -->
    <div class="fixed inset-0 cyber-grid pointer-events-none z-0"></div>

    <!-- Floating Navigation Bar -->
    <div class="sticky top-4 z-50 w-full max-w-6xl mx-auto px-4">
        <nav class="floating-nav rounded-2xl transition-all duration-300">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                            <span class="text-lg font-bold tracking-tight text-white">Hubly<span class="text-[#5865F2]">Bot</span></span>
                        </a>
                    </div>

                    <!-- Desktop Nav Links -->
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('home') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition-all duration-200 {{ Route::is('home') ? 'text-white bg-white/5 border border-white/10' : 'text-slate-400 hover:text-white hover:bg-white/5 border border-transparent' }}">Home</a>
                        
                        <!-- Products group -->
                        <div class="relative group py-3">
                            <button class="cursor-pointer px-3.5 py-1.5 rounded-xl text-xs font-semibold uppercase tracking-wider text-slate-400 hover:text-white border border-transparent hover:bg-white/5 flex items-center gap-1">
                                Products <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                            </button>
                            <div class="absolute top-full left-0 pt-2 w-48 hidden group-hover:block transition-all duration-200">
                                <div class="rounded-xl bg-[#080914] border border-white/5 p-2 shadow-2xl">
                                    <a href="{{ route('products.builder') }}" class="block px-3 py-2 rounded-lg text-xs font-medium text-slate-300 hover:text-white hover:bg-white/5">Bot Creator</a>
                                    <a href="{{ route('products.hosting') }}" class="block px-3 py-2 rounded-lg text-xs font-medium text-slate-300 hover:text-white hover:bg-white/5">Bot Hosting</a>
                                    <a href="{{ route('products.stats') }}" class="block px-3 py-2 rounded-lg text-xs font-medium text-slate-300 hover:text-white hover:bg-white/5">Stats & Analytics</a>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('pricing') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition-all duration-200 {{ Route::is('pricing') ? 'text-white bg-white/5 border border-white/10' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">Pricing</a>
                    </div>

                    <!-- CTA Actions -->
                    <div class="hidden md:flex items-center gap-3">
                        @guest
                            <a href="{{ route('auth.redirect', ['redirect' => route('dashboard')]) }}" class="neon-glow-btn text-white text-xs font-bold px-4.5 py-2.5 rounded-xl flex items-center gap-1.5">
                                Login with Discord <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                            </a>
                        @else
                            <div class="relative group py-2">
                                <button class="cursor-pointer flex items-center gap-2.5 px-3.5 py-2 rounded-xl border border-white/5 bg-white/5 hover:bg-white/10 transition-colors">
                                    <img src="{{ Auth::user()->avatar ?? 'https://cdn.discordapp.com/embed/avatars/0.png' }}" alt="Avatar" class="w-6 h-6 rounded-full border border-[#5865F2]/30" style="width: 24px; height: 24px; object-fit: cover;">
                                    <span class="text-xs font-semibold text-slate-300">{{ Auth::user()->name }}</span>
                                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-500 transition-transform duration-200 group-hover:rotate-180"></i>
                                </button>
                                <div class="absolute top-full right-0 pt-2 w-48 hidden group-hover:block transition-all duration-200 z-50">
                                    <div class="rounded-xl bg-[#080914] border border-white/5 p-2 shadow-2xl space-y-1">
                                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-slate-300 hover:text-white hover:bg-white/5 transition">
                                            <i data-lucide="layout-dashboard" class="w-3.5 h-3.5 text-slate-400"></i> Go to Dashboard
                                        </a>
                                        <hr class="border-white/5 my-1">
                                        <form action="{{ route('logout') }}" method="POST" class="m-0 w-full">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-rose-400 hover:text-rose-300 hover:bg-rose-500/5 transition cursor-pointer text-left">
                                                <i data-lucide="log-out" class="w-3.5 h-3.5 text-rose-400"></i> Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endguest
                    </div>
 
                    <!-- Mobile Menu Button -->
                    <div class="flex md:hidden">
                        <button type="button" id="mobile-menu-toggle" class="text-slate-400 hover:text-white focus:outline-none p-2 rounded-lg transition-colors">
                            <i data-lucide="menu" class="w-5 h-5" id="menu-icon"></i>
                            <i data-lucide="x" class="w-5 h-5 hidden" id="close-icon"></i>
                        </button>
                    </div>
                </div>
            </div>
 
            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="hidden md:hidden border-t border-white/5 bg-[#080914]/95 px-4 pt-2 pb-6 space-y-2 rounded-b-2xl">
                <a href="{{ route('home') }}" class="block px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider {{ Route::is('home') ? 'text-white bg-white/5 border border-white/10' : 'text-slate-400' }}">Home</a>
                <div class="pl-4 py-1 space-y-1.5">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Products</p>
                    <a href="{{ route('products.builder') }}" class="block text-xs text-slate-300 hover:text-white">Bot Creator</a>
                    <a href="{{ route('products.hosting') }}" class="block text-xs text-slate-300 hover:text-white">Bot Hosting</a>
                    <a href="{{ route('products.stats') }}" class="block text-xs text-slate-300 hover:text-white">Stats & Analytics</a>
                </div>
                <a href="{{ route('pricing') }}" class="block px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider {{ Route::is('pricing') ? 'text-white bg-white/5 border border-white/10' : 'text-slate-400' }}">Pricing</a>
                <div class="pt-2">
                    @guest
                        <a href="{{ route('auth.redirect', ['redirect' => route('dashboard')]) }}" class="w-full neon-glow-btn text-white text-xs font-bold py-3 rounded-xl flex items-center justify-center gap-1.5">
                            Login with Discord <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                        </a>
                    @else
                        <div class="space-y-3">
                            <a href="{{ route('dashboard') }}" class="w-full px-4 py-2.5 rounded-xl bg-[#5865F2]/10 border border-[#5865F2]/25 text-xs font-bold text-white transition flex items-center justify-center gap-1.5">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Go to Dashboard
                            </a>
                            <div class="flex items-center justify-between p-3 rounded-xl bg-[#0a0b16] border border-white/5">
                                <div class="flex items-center gap-2">
                                    <img src="{{ Auth::user()->avatar ?? 'https://cdn.discordapp.com/embed/avatars/0.png' }}" alt="Avatar" class="w-8 h-8 rounded-full border border-[#5865F2]/30">
                                    <span class="text-xs font-semibold text-slate-300">{{ Auth::user()->name }}</span>
                                </div>
                                <form action="{{ route('logout') }}" method="POST" class="inline m-0">
                                    @csrf
                                    <button type="submit" class="cursor-pointer text-slate-400 hover:text-white transition-colors p-1.5 rounded-lg hover:bg-white/5 flex items-center gap-1.5">
                                        <span class="text-[10px] uppercase font-bold tracking-wider">Logout</span>
                                        <i data-lucide="log-out" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>
    </div>

    <!-- Main Content Area -->
    <main class="flex-grow z-10 relative">
        <div class="max-w-6xl mx-auto px-4 mt-4">
            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
        </div>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-white/5 bg-[#020204] py-12 sm:py-16 mt-20 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Info Section -->
                <div class="space-y-4 md:col-span-2">
                    <div class="flex items-center gap-2">
                        <span class="text-md font-bold text-white tracking-tight">HublyBot</span>
                    </div>
                    <p class="text-xs text-slate-400 max-w-sm leading-relaxed">
                        The visual platform to design, launch, and host custom Discord bots with dedicated containers. No programming language required.
                    </p>
                </div>

                <div>
                    <h3 class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-4">Products</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('products.builder') }}" class="text-slate-400 hover:text-white transition-colors">Bot Creator</a></li>
                        <li><a href="{{ route('products.hosting') }}" class="text-slate-400 hover:text-white transition-colors">Bot Hosting</a></li>
                        <li><a href="{{ route('products.stats') }}" class="text-slate-400 hover:text-white transition-colors">Stats & Analytics</a></li>
                    </ul>
                </div>

                <!-- Links Section 2 -->
                <div>
                    <h3 class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-4">Navigation</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('home') }}" class="text-slate-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="{{ route('pricing') }}" class="text-slate-400 hover:text-white transition-colors">Pricing Options</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Support Server</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-white/5 mt-12 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-[10px] text-slate-500">
                    &copy; 2026 HublyBot. All rights reserved. Discord API compliant.
                </p>
                <div class="flex space-x-6 text-slate-500 text-sm">
                    <a href="#" class="hover:text-white transition-colors"><i data-lucide="twitter" class="w-4 h-4"></i></a>
                    <a href="#" class="hover:text-white transition-colors"><i data-lucide="github" class="w-4 h-4"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Init Lucide Icons -->
    <script>
        lucide.createIcons();

        // Mobile menu toggle script
        const menuToggleBtn = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');

        if (menuToggleBtn && mobileMenu) {
            menuToggleBtn.addEventListener('click', () => {
                const isHidden = mobileMenu.classList.contains('hidden');
                if (isHidden) {
                    mobileMenu.classList.remove('hidden');
                    menuIcon.classList.add('hidden');
                    closeIcon.classList.remove('hidden');
                } else {
                    mobileMenu.classList.add('hidden');
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>
