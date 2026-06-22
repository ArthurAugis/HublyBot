@extends('layouts.app')

@section('title', 'Server Stats & Analytics - HublyBot')

@section('content')
<div class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
        <h1 class="text-xs font-semibold uppercase tracking-widest text-[#5865F2]">Understand Your Server</h1>
        <p class="text-4xl sm:text-5xl font-extrabold text-white">Visual Stats & Analytics</p>
        <p class="text-slate-400">Track how active your community is and see your server grow. Easily view messages sent and popular commands in real time.</p>
    </div>

    <!-- Visual Dashboard -->
    <div class="bg-[#080914] border border-[#5865F2]/20 rounded-3xl p-6 sm:p-8 relative overflow-hidden shadow-2xl mb-16">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#14142b_1px,transparent_1px),linear-gradient(to_bottom,#14142b_1px,transparent_1px)] bg-[size:32px_32px] opacity-20 pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            
            <!-- Left panel: Metrics buttons -->
            <div class="lg:col-span-4 bg-[#05060c] border border-white/5 rounded-2xl p-5 flex flex-col justify-between">
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Metrics Inspected</h4>
                    
                    <button onclick="changeStatsTab('members')" id="tab-stat-members" class="cursor-pointer w-full flex items-center justify-between p-4 rounded-xl border border-purple-500/30 bg-purple-500/10 text-left text-xs font-bold text-white transition duration-200">
                        <span class="flex items-center gap-2">
                            <i data-lucide="users" class="tab-icon w-4.5 h-4.5 text-purple-400"></i> New Server Members
                        </span>
                        <span class="text-emerald-400 font-mono font-bold">+142</span>
                    </button>

                    <button onclick="changeStatsTab('commands')" id="tab-stat-commands" class="cursor-pointer w-full flex items-center justify-between p-4 rounded-xl border border-white/5 bg-[#121424]/40 hover:bg-[#121424]/60 text-left text-xs font-bold text-slate-400 hover:text-white transition duration-200">
                        <span class="flex items-center gap-2">
                            <i data-lucide="message-square" class="tab-icon w-4.5 h-4.5 text-slate-400"></i> Chat Messages Sent
                        </span>
                        <span class="text-white font-mono font-bold">4,812</span>
                    </button>
                </div>

                <div class="pt-6 border-t border-white/5 text-[10px] text-slate-500 leading-relaxed">
                    <span class="block font-bold text-slate-400 mb-1">Free Tier limitation</span>
                    Standard plan keeps history for the last 7 days. Upgrade to Premium to keep 90 days.
                </div>
            </div>

            <!-- Right panel: Gorgeous Custom SVG Chart representation -->
            <div class="lg:col-span-8 bg-[#030307]/80 border border-white/5 rounded-2xl p-6 min-h-[380px] flex flex-col justify-between relative">
                
                <!-- Floating Glassmorphism Tooltip -->
                <div id="chart-tooltip" class="absolute pointer-events-none opacity-0 bg-[#080914]/95 border border-[#5865F2]/40 rounded-xl px-3 py-2 text-[10px] font-mono text-white shadow-2xl transition-all duration-150 z-30 flex flex-col gap-0.5 backdrop-filter backdrop-blur-md">
                    <span id="tooltip-day" class="font-bold text-slate-500"></span>
                    <span id="tooltip-val" class="font-extrabold"></span>
                </div>

                <div class="relative z-10 space-y-4 w-full">
                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-white/5 pb-2">
                        <span id="chart-title-lbl">New Server Members (Last 7 Days)</span>
                        <span id="chart-hover-val" class="text-slate-500 font-mono font-bold tracking-normal transition-all duration-200">Hover points to inspect</span>
                    </div>

                    <!-- Glowing SVG Chart (Line or Bar) -->
                    <div class="relative pt-6 flex items-center justify-center">
                        <svg viewBox="0 0 520 220" class="w-full h-56 transition-all duration-300" id="chart-svg">
                            <!-- Gradients -->
                            <defs>
                                <linearGradient id="members-gradient" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.3"/>
                                    <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0.0"/>
                                </linearGradient>
                                <linearGradient id="commands-gradient" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#5865F2" stop-opacity="0.8"/>
                                    <stop offset="100%" stop-color="#5865F2" stop-opacity="0.1"/>
                                </linearGradient>
                            </defs>
                            
                            <!-- Content Group (Rendered by JavaScript switch) -->
                            <g id="chart-data-group">
                                <!-- Default: Members Line Area Chart -->
                                <!-- Grid lines -->
                                <line x1="35" y1="40" x2="485" y2="40" stroke="rgba(255,255,255,0.03)" stroke-width="1"/>
                                <line x1="35" y1="90" x2="485" y2="90" stroke="rgba(255,255,255,0.03)" stroke-width="1"/>
                                <line x1="35" y1="140" x2="485" y2="140" stroke="rgba(255,255,255,0.03)" stroke-width="1"/>
                                
                                <path d="M 35 160 L 110 140 L 185 150 L 260 115 L 335 80 L 410 65 L 485 50 L 485 170 L 35 170 Z" fill="url(#members-gradient)"></path>
                                <path d="M 35 160 L 110 140 L 185 150 L 260 115 L 335 80 L 410 65 L 485 50" fill="none" stroke="#8b5cf6" stroke-width="3" stroke-linecap="round"></path>
                                <circle cx="35" cy="160" r="5" fill="#8b5cf6" class="cursor-pointer transition-all duration-200 hover:r-7 hover:fill-purple-300" onmouseover="showLineTooltip(event, 'Monday', '+10 members')" onmouseout="hideLineTooltip()"></circle>
                                <circle cx="110" cy="140" r="5" fill="#8b5cf6" class="cursor-pointer transition-all duration-200 hover:r-7 hover:fill-purple-300" onmouseover="showLineTooltip(event, 'Tuesday', '+25 members')" onmouseout="hideLineTooltip()"></circle>
                                <circle cx="185" cy="150" r="5" fill="#8b5cf6" class="cursor-pointer transition-all duration-200 hover:r-7 hover:fill-purple-300" onmouseover="showLineTooltip(event, 'Wednesday', '+18 members')" onmouseout="hideLineTooltip()"></circle>
                                <circle cx="260" cy="115" r="5" fill="#8b5cf6" class="cursor-pointer transition-all duration-200 hover:r-7 hover:fill-purple-300" onmouseover="showLineTooltip(event, 'Thursday', '+42 members')" onmouseout="hideLineTooltip()"></circle>
                                <circle cx="335" cy="80" r="5" fill="#8b5cf6" class="cursor-pointer transition-all duration-200 hover:r-7 hover:fill-purple-300" onmouseover="showLineTooltip(event, 'Friday', '+75 members')" onmouseout="hideLineTooltip()"></circle>
                                <circle cx="410" cy="65" r="5" fill="#8b5cf6" class="cursor-pointer transition-all duration-200 hover:r-7 hover:fill-purple-300" onmouseover="showLineTooltip(event, 'Saturday', '+95 members')" onmouseout="hideLineTooltip()"></circle>
                                <circle cx="485" cy="50" r="6" fill="#a855f7" class="cursor-pointer transition-all duration-200 hover:r-8 hover:fill-purple-300 animate-pulse" onmouseover="showLineTooltip(event, 'Sunday (Today)', '+142 members')" onmouseout="hideLineTooltip()"></circle>

                                <!-- Native Labels Aligned Perfectly under the X-Coordinates -->
                                <line x1="35" y1="170" x2="485" y2="170" stroke="rgba(255,255,255,0.1)" stroke-width="1"/>
                                
                                <text x="35" y="195" text-anchor="middle" fill="#64748b" class="text-[10px] font-bold tracking-wider">MON</text>
                                <text x="110" y="195" text-anchor="middle" fill="#64748b" class="text-[10px] font-bold tracking-wider">TUE</text>
                                <text x="185" y="195" text-anchor="middle" fill="#64748b" class="text-[10px] font-bold tracking-wider">WED</text>
                                <text x="260" y="195" text-anchor="middle" fill="#64748b" class="text-[10px] font-bold tracking-wider">THU</text>
                                <text x="335" y="195" text-anchor="middle" fill="#64748b" class="text-[10px] font-bold tracking-wider">FRI</text>
                                <text x="410" y="195" text-anchor="middle" fill="#64748b" class="text-[10px] font-bold tracking-wider">SAT</text>
                                <text x="485" y="195" text-anchor="middle" fill="#64748b" class="text-[10px] font-bold tracking-wider">SUN</text>
                            </g>
                        </svg>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function changeStatsTab(tab) {
        const titleLbl = document.getElementById('chart-title-lbl');
        const dataGroup = document.getElementById('chart-data-group');
        const hoverVal = document.getElementById('chart-hover-val');
        
        // Hide tooltip on tab change
        hideFloatingTooltip();
        
        // Reset buttons styling
        ['members', 'commands'].forEach(k => {
            const btn = document.getElementById('tab-stat-' + k);
            btn.className = "cursor-pointer w-full flex items-center justify-between p-4 rounded-xl border border-white/5 bg-[#121424]/40 hover:bg-[#121424]/60 text-left text-xs font-bold text-slate-400 hover:text-white transition duration-200";
            const icon = btn.querySelector('.tab-icon') || btn.querySelector('svg');
            if (icon) {
                icon.setAttribute('class', 'tab-icon w-4.5 h-4.5 text-slate-400');
            }
        });

        const activeBtn = document.getElementById('tab-stat-' + tab);
        const activeIcon = activeBtn.querySelector('.tab-icon') || activeBtn.querySelector('svg');

        if (tab === 'members') {
            titleLbl.innerText = "New Server Members (Last 7 Days)";
            if (hoverVal) {
                hoverVal.textContent = "Hover points to inspect";
                hoverVal.className = "text-slate-500 font-mono font-bold tracking-normal transition-all duration-200";
            }
            activeBtn.className = "cursor-pointer w-full flex items-center justify-between p-4 rounded-xl border border-purple-500/30 bg-purple-500/10 text-left text-xs font-bold text-white transition duration-200";
            if (activeIcon) {
                activeIcon.setAttribute('class', 'tab-icon w-4.5 h-4.5 text-purple-400');
            }

            // Render Area Line Chart + Grid + Labels
            dataGroup.innerHTML = `
                <!-- Grid lines -->
                <line x1="35" y1="40" x2="485" y2="40" stroke="rgba(255,255,255,0.03)" stroke-width="1"/>
                <line x1="35" y1="90" x2="485" y2="90" stroke="rgba(255,255,255,0.03)" stroke-width="1"/>
                <line x1="35" y1="140" x2="485" y2="140" stroke="rgba(255,255,255,0.03)" stroke-width="1"/>
                
                <path d="M 35 160 L 110 140 L 185 150 L 260 115 L 335 80 L 410 65 L 485 50 L 485 170 L 35 170 Z" fill="url(#members-gradient)"></path>
                <path d="M 35 160 L 110 140 L 185 150 L 260 115 L 335 80 L 410 65 L 485 50" fill="none" stroke="#8b5cf6" stroke-width="3" stroke-linecap="round"></path>
                <circle cx="35" cy="160" r="5" fill="#8b5cf6" class="cursor-pointer transition-all duration-200 hover:r-7 hover:fill-purple-300" onmouseover="showLineTooltip(event, 'Monday', '+10 members')" onmouseout="hideLineTooltip()"></circle>
                <circle cx="110" cy="140" r="5" fill="#8b5cf6" class="cursor-pointer transition-all duration-200 hover:r-7 hover:fill-purple-300" onmouseover="showLineTooltip(event, 'Tuesday', '+25 members')" onmouseout="hideLineTooltip()"></circle>
                <circle cx="185" cy="150" r="5" fill="#8b5cf6" class="cursor-pointer transition-all duration-200 hover:r-7 hover:fill-purple-300" onmouseover="showLineTooltip(event, 'Wednesday', '+18 members')" onmouseout="hideLineTooltip()"></circle>
                <circle cx="260" cy="115" r="5" fill="#8b5cf6" class="cursor-pointer transition-all duration-200 hover:r-7 hover:fill-purple-300" onmouseover="showLineTooltip(event, 'Thursday', '+42 members')" onmouseout="hideLineTooltip()"></circle>
                <circle cx="335" cy="80" r="5" fill="#8b5cf6" class="cursor-pointer transition-all duration-200 hover:r-7 hover:fill-purple-300" onmouseover="showLineTooltip(event, 'Friday', '+75 members')" onmouseout="hideLineTooltip()"></circle>
                <circle cx="410" cy="65" r="5" fill="#8b5cf6" class="cursor-pointer transition-all duration-200 hover:r-7 hover:fill-purple-300" onmouseover="showLineTooltip(event, 'Saturday', '+95 members')" onmouseout="hideLineTooltip()"></circle>
                <circle cx="485" cy="50" r="6" fill="#a855f7" class="cursor-pointer transition-all duration-200 hover:r-8 hover:fill-purple-300 animate-pulse" onmouseover="showLineTooltip(event, 'Sunday (Today)', '+142 members')" onmouseout="hideLineTooltip()"></circle>

                <!-- Native Labels -->
                <line x1="35" y1="170" x2="485" y2="170" stroke="rgba(255,255,255,0.1)" stroke-width="1"/>
                
                <text x="35" y="195" text-anchor="middle" fill="#64748b" class="text-[10px] font-bold tracking-wider">MON</text>
                <text x="110" y="195" text-anchor="middle" fill="#64748b" class="text-[10px] font-bold tracking-wider">TUE</text>
                <text x="185" y="195" text-anchor="middle" fill="#64748b" class="text-[10px] font-bold tracking-wider">WED</text>
                <text x="260" y="195" text-anchor="middle" fill="#64748b" class="text-[10px] font-bold tracking-wider">THU</text>
                <text x="335" y="195" text-anchor="middle" fill="#64748b" class="text-[10px] font-bold tracking-wider">FRI</text>
                <text x="410" y="195" text-anchor="middle" fill="#64748b" class="text-[10px] font-bold tracking-wider">SAT</text>
                <text x="485" y="195" text-anchor="middle" fill="#64748b" class="text-[10px] font-bold tracking-wider">SUN</text>
            `;
        } else {
            titleLbl.innerText = "Chat Messages Sent (Last 7 Days)";
            if (hoverVal) {
                hoverVal.textContent = "Hover segments to inspect";
                hoverVal.className = "text-slate-500 font-mono font-bold tracking-normal transition-all duration-200";
            }
            activeBtn.className = "cursor-pointer w-full flex items-center justify-between p-4 rounded-xl border-[#5865F2]/30 bg-[#5865F2]/10 text-left text-xs font-bold text-white transition duration-200";
            if (activeIcon) {
                activeIcon.setAttribute('class', 'tab-icon w-4.5 h-4.5 text-[#5865F2]');
            }

            // Render Premium SVG Donut Activity Chart
            dataGroup.innerHTML = `
                <!-- Background Base Ring -->
                <circle cx="260" cy="90" r="60" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="16"></circle>
                
                <!-- General Chat Segment (65%) -->
                <circle cx="260" cy="90" r="60" fill="none" stroke="#8b5cf6" stroke-width="16" stroke-dasharray="377" stroke-dashoffset="132" stroke-linecap="round" transform="rotate(-90 260 90)" class="cursor-pointer transition-all duration-300 hover:stroke-[20px]" onmouseover="updateDonutCenter(event, '3,128', 'General Chat', '#8b5cf6')" onmouseout="resetDonutCenter()"></circle>
                
                <!-- Commands Segment (25%) -->
                <circle cx="260" cy="90" r="60" fill="none" stroke="#5865F2" stroke-width="16" stroke-dasharray="377" stroke-dashoffset="283" stroke-linecap="round" transform="rotate(144 260 90)" class="cursor-pointer transition-all duration-300 hover:stroke-[20px]" onmouseover="updateDonutCenter(event, '1,203', 'Commands Used', '#5865F2')" onmouseout="resetDonutCenter()"></circle>
                
                <!-- Auto-Mod Segment (10%) -->
                <circle cx="260" cy="90" r="60" fill="none" stroke="#f43f5e" stroke-width="16" stroke-dasharray="377" stroke-dashoffset="339" stroke-linecap="round" transform="rotate(234 260 90)" class="cursor-pointer transition-all duration-300 hover:stroke-[20px]" onmouseover="updateDonutCenter(event, '481', 'Auto-Moderation', '#f43f5e')" onmouseout="resetDonutCenter()"></circle>

                <!-- Center Text info -->
                <text x="260" y="88" id="donut-val" text-anchor="middle" fill="#ffffff" class="text-lg font-extrabold font-mono transition-colors duration-200">4,812</text>
                <text x="260" y="103" id="donut-lbl" text-anchor="middle" fill="#64748b" class="text-[9px] font-bold uppercase tracking-wider transition-colors duration-200">Total Actions</text>

                <!-- Visual legend tags inside SVG workspace -->
                <g transform="translate(130, 190)">
                    <!-- Legend 1 -->
                    <g class="cursor-pointer" onmouseover="updateDonutCenter(event, '3,128', 'General Chat', '#8b5cf6')" onmouseout="resetDonutCenter()">
                        <circle cx="0" cy="0" r="4" fill="#8b5cf6"></circle>
                        <text x="10" y="3.5" fill="#94a3b8" class="text-[8px] font-bold">General Chat (65%)</text>
                    </g>
                    
                    <!-- Legend 2 -->
                    <g class="cursor-pointer" onmouseover="updateDonutCenter(event, '1,203', 'Commands Used', '#5865F2')" onmouseout="resetDonutCenter()">
                        <circle cx="110" cy="0" r="4" fill="#5865F2"></circle>
                        <text x="120" y="3.5" fill="#94a3b8" class="text-[8px] font-bold">Commands Used (25%)</text>
                    </g>
                    
                    <!-- Legend 3 -->
                    <g class="cursor-pointer" onmouseover="updateDonutCenter(event, '481', 'Auto-Moderation', '#f43f5e')" onmouseout="resetDonutCenter()">
                        <circle cx="225" cy="0" r="4" fill="#f43f5e"></circle>
                        <text x="235" y="3.5" fill="#94a3b8" class="text-[8px] font-bold">Auto-Moderation (10%)</text>
                    </g>
                </g>
            `;
        }

        // Re-init icons
        lucide.createIcons();
    }

    function showLineTooltip(e, day, value) {
        const hoverVal = document.getElementById('chart-hover-val');
        if (hoverVal) {
            hoverVal.textContent = day + ': ' + value;
            hoverVal.className = "text-[#8b5cf6] font-mono font-bold tracking-normal transition-all duration-200";
        }
        showFloatingTooltip(e, day, value, '#8b5cf6');
    }

    function hideLineTooltip() {
        const hoverVal = document.getElementById('chart-hover-val');
        if (hoverVal) {
            hoverVal.textContent = "Hover points to inspect";
            hoverVal.className = "text-slate-500 font-mono font-bold tracking-normal transition-all duration-200";
        }
        hideFloatingTooltip();
    }

    function updateDonutCenter(e, val, label, color) {
        const valEl = document.getElementById('donut-val');
        const lblEl = document.getElementById('donut-lbl');
        const hoverVal = document.getElementById('chart-hover-val');
        if (valEl && lblEl) {
            valEl.textContent = val;
            valEl.setAttribute('fill', color);
            lblEl.textContent = label;
            lblEl.setAttribute('fill', color);
        }
        if (hoverVal) {
            hoverVal.textContent = label + ': ' + val + ' actions';
            hoverVal.className = "text-[#5865F2] font-mono font-bold tracking-normal transition-all duration-200";
        }
        showFloatingTooltip(e, label, val + ' Actions', color);
    }

    function resetDonutCenter() {
        const valEl = document.getElementById('donut-val');
        const lblEl = document.getElementById('donut-lbl');
        const hoverVal = document.getElementById('chart-hover-val');
        if (valEl && lblEl) {
            valEl.textContent = '4,812';
            valEl.setAttribute('fill', '#ffffff');
            lblEl.textContent = 'Total Actions';
            lblEl.setAttribute('fill', '#64748b');
        }
        if (hoverVal) {
            hoverVal.textContent = "Hover segments to inspect";
            hoverVal.className = "text-slate-500 font-mono font-bold tracking-normal transition-all duration-200";
        }
        hideFloatingTooltip();
    }

    function showFloatingTooltip(e, title, desc, color) {
        const tooltip = document.getElementById('chart-tooltip');
        const tDay = document.getElementById('tooltip-day');
        const tVal = document.getElementById('tooltip-val');
        if (tooltip && tDay && tVal) {
            tDay.textContent = title;
            tVal.textContent = desc;
            tVal.style.color = color;
            
            // Coordinates relative to parent card
            const rect = e.target.getBoundingClientRect();
            const parentRect = tooltip.parentNode.getBoundingClientRect();
            
            const x = rect.left - parentRect.left + (rect.width / 2) - (tooltip.offsetWidth / 2);
            const y = rect.top - parentRect.top - tooltip.offsetHeight - 8;
            
            tooltip.style.left = x + 'px';
            tooltip.style.top = y + 'px';
            tooltip.style.opacity = '1';
        }
    }

    function hideFloatingTooltip() {
        const tooltip = document.getElementById('chart-tooltip');
        if (tooltip) {
            tooltip.style.opacity = '0';
        }
    }
</script>
@endsection
