@extends('layouts.app')

@section('title', 'Visual Bot Creator - HublyBot')

@section('content')
<style>
    @keyframes triggerPop {
        0% { opacity: 0; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1); }
    }
    @keyframes drawLine {
        0% { height: 0; opacity: 0; }
        100% { height: 48px; opacity: 1; }
    }
    @keyframes actionPop {
        0% { opacity: 0; transform: translateY(15px) scale(0.95); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-trigger-card {
        animation: triggerPop 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .animate-connector-line {
        height: 0;
        opacity: 0;
        overflow: visible;
        animation: drawLine 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.4s forwards;
    }
    .animate-action-card {
        opacity: 0;
        animation: actionPop 0.4s cubic-bezier(0.16, 1, 0.3, 1) 0.9s forwards;
    }
</style>

<div class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
        <h1 class="text-xs font-semibold uppercase tracking-widest text-[#5865F2]">Visual Feature Showcase</h1>
        <p class="text-4xl sm:text-5xl font-extrabold text-white">Visual Command Builder</p>
        <p class="text-slate-400">Design your bot logic without code. Drag triggers, link conditions, and stack actions in seconds.</p>
    </div>

    <!-- Visual Interactive Workbench -->
    <div class="bg-[#080914] border border-[#5865F2]/20 rounded-3xl p-6 sm:p-8 relative overflow-hidden shadow-2xl mb-16">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#14142b_1px,transparent_1px),linear-gradient(to_bottom,#14142b_1px,transparent_1px)] bg-[size:32px_32px] opacity-20 pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            
            <!-- Left panel: Drag-and-drop toolbox simulator -->
            <div class="lg:col-span-4 bg-[#05060c] border border-white/5 rounded-2xl p-5 space-y-6">
                <div>
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Select Trigger Event</h4>
                    <div class="space-y-2">
                        <button onclick="selectBuilderNode('join')" id="btn-node-join" class="cursor-pointer w-full flex items-center gap-3 p-3 rounded-xl border border-purple-500/30 bg-purple-500/10 text-left text-xs font-bold text-white transition duration-200">
                            <i data-lucide="user-plus" class="w-4 h-4 text-purple-400"></i>
                            New Member Joins
                        </button>
                        <button onclick="selectBuilderNode('message')" id="btn-node-message" class="cursor-pointer w-full flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-[#121424]/40 hover:bg-[#121424]/60 text-left text-xs font-bold text-slate-400 hover:text-white transition duration-200">
                            <i data-lucide="message-square" class="w-4 h-4 text-slate-400"></i>
                            Message Contains Keyword
                        </button>
                        <button onclick="selectBuilderNode('command')" id="btn-node-command" class="cursor-pointer w-full flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-[#121424]/40 hover:bg-[#121424]/60 text-left text-xs font-bold text-slate-400 hover:text-white transition duration-200">
                            <i data-lucide="hash" class="w-4 h-4 text-slate-400"></i>
                            Slash Command "/apply"
                        </button>
                        <button onclick="selectBuilderNode('reaction')" id="btn-node-reaction" class="cursor-pointer w-full flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-[#121424]/40 hover:bg-[#121424]/60 text-left text-xs font-bold text-slate-400 hover:text-white transition duration-200">
                            <i data-lucide="star" class="w-4 h-4 text-slate-400"></i>
                            User Reacts with ⭐
                        </button>
                        <button onclick="selectBuilderNode('leave')" id="btn-node-leave" class="cursor-pointer w-full flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-[#121424]/40 hover:bg-[#121424]/60 text-left text-xs font-bold text-slate-400 hover:text-white transition duration-200">
                            <i data-lucide="user-minus" class="w-4 h-4 text-slate-400"></i>
                            Member Leaves Server
                        </button>
                        <button onclick="selectBuilderNode('mention')" id="btn-node-mention" class="cursor-pointer w-full flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-[#121424]/40 hover:bg-[#121424]/60 text-left text-xs font-bold text-slate-400 hover:text-white transition duration-200">
                            <i data-lucide="at-sign" class="w-4 h-4 text-slate-400"></i>
                            Bot is Mentioned
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right panel: Visual workspace simulation -->
            <div class="lg:col-span-8 bg-[#030307]/80 border border-white/5 rounded-2xl p-6 min-h-[380px] flex flex-col justify-between relative">
                <!-- Node graph grid lines -->
                <div class="flex flex-col items-center w-full pt-4">
                    
                    <!-- Event Block Node -->
                    <div class="w-80 p-4 rounded-2xl bg-[#0e101f] border border-purple-500/20 shadow-lg shadow-purple-900/5 relative z-10 animate-trigger-card">
                        <div class="absolute -top-2.5 left-6 bg-purple-600 text-white text-[8px] font-bold uppercase tracking-wider px-2 py-0.5 rounded">Trigger</div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="bg-purple-600/20 p-2 rounded-lg text-purple-400">
                                    <i data-lucide="user-plus" class="w-4 h-4" id="visual-trigger-icon"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-white" id="visual-trigger-title">New Member Joins</h4>
                                    <p class="text-[9px] text-slate-500" id="visual-trigger-sub">Discord server activity</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Glowing connector line -->
                    <div class="w-[2px] bg-gradient-to-b from-purple-500 to-[#5865F2] relative z-20 animate-connector-line">
                        <!-- Top connector circle -->
                        <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full bg-purple-500 shadow-[0_0_8px_rgba(168,85,247,0.8)]"></div>
                        <!-- Bottom arrowhead -->
                        <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-2.5 h-2.5 rotate-45 border-r-2 border-b-2 border-[#5865F2]"></div>
                    </div>

                    <!-- Action Block Node -->
                    <div id="visual-action-card" class="w-80 p-4 rounded-2xl bg-[#0e101f] border border-[#5865F2]/20 shadow-lg shadow-[#5865F2]/5 relative z-10 animate-action-card">
                        <div class="absolute -top-2.5 left-6 bg-[#5865F2] text-white text-[8px] font-bold uppercase tracking-wider px-2 py-0.5 rounded">Action</div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="bg-[#5865F2]/20 p-2 rounded-lg text-[#5865F2]">
                                    <i data-lucide="message-circle" class="w-4 h-4" id="visual-action-icon"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-white" id="visual-action-title">Send Welcome Message</h4>
                                    <p class="text-[9px] text-slate-500 font-normal" id="visual-action-desc">Welcomes new users automatically</p>
                                </div>
                            </div>
                            <i data-lucide="settings-2" class="w-4 h-4 text-slate-500 cursor-pointer hover:text-white transition duration-200"></i>
                        </div>
                        
                        <div class="bg-[#05060c] border border-white/5 rounded-xl p-3 text-[10px] font-mono text-slate-300 animate-fade-in" id="visual-action-preview">
                            <span class="text-[#5865F2]">Where:</span> #welcome-channel<br>
                            <span class="text-[#5865F2]">Message:</span> Welcome to our server, {user}! 🎉
                        </div>
                    </div>

                </div>

                <div class="pt-6 border-t border-white/5 flex items-center justify-between text-[10px] text-slate-500 font-semibold uppercase tracking-wider">
                    <span>Try clicking the left events to see logic updates</span>
                    <a href="{{ route('pricing') }}" class="text-[#5865F2] hover:underline flex items-center gap-1">Start Building <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i></a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const nodeConfigs = {
        join: {
            btnId: 'btn-node-join',
            triggerIcon: 'user-plus',
            triggerTitle: 'New Member Joins',
            triggerSub: 'Discord server activity',
            actionIcon: 'message-circle',
            actionTitle: 'Send Welcome Message',
            actionDesc: 'Welcomes new users automatically',
            actionPreview: '<span class="text-[#5865F2]">Where:</span> #welcome-channel<br><span class="text-[#5865F2]">Message:</span> Welcome to our server, {user}! 🎉'
        },
        message: {
            btnId: 'btn-node-message',
            triggerIcon: 'message-square',
            triggerTitle: 'Message Contains Keyword',
            triggerSub: 'Chat message posted',
            actionIcon: 'shield-alert',
            actionTitle: 'Remove Spam & Alert User',
            actionDesc: 'Deletes links and sends a friendly warning',
            actionPreview: '<span class="text-[#5865F2]">Blocked word:</span> "discord.gg/"<br><span class="text-[#5865F2]">Action:</span> Delete post + send a warning message'
        },
        command: {
            btnId: 'btn-node-command',
            triggerIcon: 'hash',
            triggerTitle: 'Slash Command "/apply"',
            triggerSub: 'User types command',
            actionIcon: 'user-check',
            actionTitle: 'Give "Member" Role',
            actionDesc: 'Gives roles automatically to users',
            actionPreview: '<span class="text-[#5865F2]">Role:</span> @Member<br><span class="text-[#5865F2]">Requirement:</span> No prior roles'
        },
        reaction: {
            btnId: 'btn-node-reaction',
            triggerIcon: 'star',
            triggerTitle: 'User Reacts with ⭐',
            triggerSub: 'Emoji reaction added',
            actionIcon: 'award',
            actionTitle: 'Give Star Role',
            actionDesc: 'Reward active reaction engagement',
            actionPreview: '<span class="text-[#5865F2]">Role:</span> @Star Member<br><span class="text-[#5865F2]">Emoji:</span> ⭐ Star'
        },
        leave: {
            btnId: 'btn-node-leave',
            triggerIcon: 'user-minus',
            triggerTitle: 'Member Leaves Server',
            triggerSub: 'Discord server activity',
            actionIcon: 'alert-triangle',
            actionTitle: 'Notify Moderators',
            actionDesc: 'Logs server departure in admin chat',
            actionPreview: '<span class="text-[#5865F2]">Where:</span> #mod-logs<br><span class="text-[#5865F2]">Log:</span> {user} has left the server.'
        },
        mention: {
            btnId: 'btn-node-mention',
            triggerIcon: 'at-sign',
            triggerTitle: 'Bot is Mentioned',
            triggerSub: 'User types @HublyBot',
            actionIcon: 'help-circle',
            actionTitle: 'Send Help Guide',
            actionDesc: 'Displays menu list of features',
            actionPreview: '<span class="text-[#5865F2]">Reply:</span> In same channel<br><span class="text-[#5865F2]">Guide:</span> List all slash commands'
        }
    };

    function selectBuilderNode(nodeKey) {
        const config = nodeConfigs[nodeKey];

        // Reset buttons styling
        ['join', 'message', 'command', 'reaction', 'leave', 'mention'].forEach(k => {
            const btn = document.getElementById('btn-node-' + k);
            btn.className = "cursor-pointer w-full flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-[#121424]/40 hover:bg-[#121424]/60 text-left text-xs font-bold text-slate-400 hover:text-white transition duration-200";
        });

        // Set active button
        const activeBtn = document.getElementById(config.btnId);
        activeBtn.className = "cursor-pointer w-full flex items-center gap-3 p-3 rounded-xl border border-purple-500/30 bg-purple-500/10 text-left text-xs font-bold text-white transition duration-200";

        // Update visual nodes content
        document.getElementById('visual-trigger-title').innerText = config.triggerTitle;
        document.getElementById('visual-trigger-sub').innerText = config.triggerSub;
        document.getElementById('visual-action-title').innerText = config.actionTitle;
        document.getElementById('visual-action-desc').innerText = config.actionDesc;
        document.getElementById('visual-action-preview').innerHTML = config.actionPreview;

        // Re-trigger animation sequence for feedback
        const actionCard = document.getElementById('visual-action-card');
        const connectorLine = document.querySelector('.animate-connector-line');
        
        if (actionCard && connectorLine) {
            // Reset animation
            actionCard.style.animation = 'none';
            connectorLine.style.animation = 'none';
            
            actionCard.offsetHeight; /* trigger reflow */
            connectorLine.offsetHeight; /* trigger reflow */
            
            // Reapply
            connectorLine.style.animation = 'drawLine 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards';
            actionCard.style.animation = 'actionPop 0.4s cubic-bezier(0.16, 1, 0.3, 1) 0.4s forwards';
        }

        // Re-init lucide icons on updates
        lucide.createIcons();
    }
</script>
@endsection
