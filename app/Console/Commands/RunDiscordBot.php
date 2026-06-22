<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bot;
use Discord\Discord;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Discord\Parts\Channel\Message;
use Discord\Parts\Channel\Channel;
use Discord\Parts\User\Member;
use Discord\Parts\Channel\Reaction;
use Discord\Parts\Interactions\Interaction;
use Illuminate\Support\Facades\File;
use Discord\Parts\Guild\ScheduledEvent;
use Discord\Parts\Guild\AutoModeration\Rule;

class RunDiscordBot extends Command
{
    protected $signature = 'bot:run {id}';
    protected $description = 'Runs a Discord Bot instance based on its Logic Builder configuration';

    protected $bot;
    protected $pidFile;
    protected $logFile;
    protected $discord;
    protected $logicBlocks = [];

    public function handle()
    {
        $botId = $this->argument('id');
        $this->bot = Bot::find($botId);

        if (!$this->bot) {
            $this->error("Bot with ID {$botId} not found.");
            return 1;
        }

        if (empty($this->bot->token)) {
            $this->log("ERROR", "No Discord Bot Token configured. Setup your token in Bot Setup.");
            return 1;
        }

        $this->pidFile = storage_path("app/bot_{$this->bot->id}.pid");
        $this->logFile = storage_path("logs/bot_{$this->bot->id}.log");

        // Track running process using local PID file
        File::put($this->pidFile, getmypid());

        $this->log("SYSTEM", "Initializing bot instance container...");
        $this->log("SYSTEM", "Loading configurations...");

        $this->loadLogicBlocks();

        $this->log("SYSTEM", "Loaded " . count($this->logicBlocks) . " logic builder blocks.");

        // Dynamically compute required gateway intents based on active logic triggers
        $intents = Intents::getDefaultIntents();
        $needMembers = false;
        $needMessageContent = false;
        $needVoiceStates = false;
        $needInvites = false;
        $needEmojis = false;
        $needEvents = false;
        $needTyping = false;

        foreach ($this->logicBlocks as $block) {
            if ($block['type'] === 'trigger') {
                if (in_array($block['event'], ['join', 'leave', 'member_update', 'boost_event'])) {
                    $needMembers = true;
                }
                if (in_array($block['event'], ['message', 'message_edit'])) {
                    $needMessageContent = true;
                }
                if ($block['event'] === 'voice_update') {
                    $needVoiceStates = true;
                }
                if ($block['event'] === 'invite_update') {
                    $needInvites = true;
                }
                if ($block['event'] === 'emoji_update') {
                    $needEmojis = true;
                }
                if ($block['event'] === 'scheduled_event_update') {
                    $needEvents = true;
                }
                if ($block['event'] === 'typing_start') {
                    $needTyping = true;
                }
            }
        }

        if ($needMembers) {
            $intents |= Intents::GUILD_MEMBERS;
        }
        if ($needMessageContent) {
            $intents |= Intents::MESSAGE_CONTENT;
        }
        if ($needVoiceStates) {
            $intents |= Intents::GUILD_VOICE_STATES;
        }
        if ($needInvites) {
            $intents |= Intents::GUILD_INVITES;
        }
        if ($needEmojis) {
            $intents |= Intents::GUILD_EMOJIS_AND_STICKERS;
        }
        if ($needEvents) {
            $intents |= Intents::GUILD_SCHEDULED_EVENTS;
        }
        if ($needTyping) {
            $intents |= Intents::GUILD_MESSAGE_TYPING;
        }

        // Initialize Discord WebSocket client connection
        try {
            $this->discord = new Discord([
                'token' => $this->bot->token,
                'intents' => $intents,
            ]);

            $this->discord->on('close', function () use ($needMembers, $needMessageContent) {
                if ($needMembers || $needMessageContent) {
                    $this->log("ERROR", "Disallowed Intent(s) detected. Please ensure you have enabled the 'SERVER MEMBERS INTENT' and 'MESSAGE CONTENT INTENT' under the 'Bot' tab of your application in the Discord Developer Portal (https://discord.com/developers/applications).");
                }
            });
        } catch (\Exception $e) {
            $this->log("ERROR", "Failed to initialize Discord client: " . $e->getMessage());
            return 1;
        }

        // Reclaim local resource files during shutdown
        register_shutdown_function(function() {
            if (File::exists($this->pidFile)) {
                File::delete($this->pidFile);
            }
            $this->log("SYSTEM", "Bot container process shut down.");
        });

        // Register Discord client ready and event listeners
        $this->discord->on('ready', function (Discord $discord) {
            $this->log("SYSTEM", "Bot is READY! Running as {$discord->user->username}#{$discord->user->discriminator}");
            $this->log("DISCORD", "Gateway connection established cleanly.");

            $this->executeTriggers('ready', null, null);

            $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
                if ($message->author->bot) return;
                $this->log("EVENT", "Message received from @{$message->author->username} in #{$message->channel->name}: \"{$message->content}\"");
                $this->executeTriggers('message', $message, $message->channel);
            });

            $discord->on(Event::GUILD_MEMBER_ADD, function (Member $member, Discord $discord) {
                $this->log("EVENT", "Member joined server: @{$member->user->username}");

                $channel = $member->guild->system_channel ?? $member->guild->channels->first();
                $this->executeTriggers('join', $member, $channel);
            });

            $discord->on(Event::GUILD_MEMBER_REMOVE, function ($member, Discord $discord) {
                $username = ($member instanceof Member) ? $member->user->username : 'Unknown User';
                $this->log("EVENT", "Member left server: @{$username}");
                
                $channel = null;
                if ($member instanceof Member) {
                    $channel = $member->guild->system_channel ?? $member->guild->channels->first();
                }
                $this->executeTriggers('leave', $member, $channel);
            });

            $discord->on(Event::MESSAGE_REACTION_ADD, function (Reaction $reaction, Discord $discord) {
                $this->log("EVENT", "Reaction added on message in #{$reaction->channel->name}");
                $this->executeTriggers('reaction', $reaction, $reaction->channel);
            });

            $discord->on(Event::CHANNEL_CREATE, function (Channel $channel, Discord $discord) {
                $this->log("EVENT", "Channel created: #{$channel->name}");
                $this->executeTriggers('channel_create', $channel, $channel);
            });

            $discord->on(Event::GUILD_ROLE_CREATE, function ($role, Discord $discord) {
                $this->log("EVENT", "Role created: {$role->name}");
                $guild = $role->guild ?? null;
                $channel = $guild ? ($guild->system_channel ?? $guild->channels->first()) : null;
                $this->executeTriggers('role_create', $role, $channel);
            });

            $discord->on(Event::MESSAGE_UPDATE, function ($message, Discord $discord, $oldMessage) {
                if ($message->author && $message->author->bot) return;
                $this->log("EVENT", "Message edited in #{$message->channel->name}");
                $this->executeTriggers('message_edit', $message, $message->channel);
            });

            $discord->on(Event::MESSAGE_DELETE, function ($message, Discord $discord) {
                $channel = ($message instanceof Message) ? $message->channel : null;
                $channelName = $channel ? "#{$channel->name}" : "unknown channel";
                $this->log("EVENT", "Message deleted in {$channelName}");
                $this->executeTriggers('message_delete', $message, $channel);
            });

            $discord->on(Event::GUILD_BAN_ADD, function ($ban, Discord $discord) {
                $username = ($ban->user) ? $ban->user->username : 'Unknown User';
                $this->log("EVENT", "Member banned: @{$username}");
                $guild = $ban->guild ?? null;
                $channel = $guild ? ($guild->system_channel ?? $guild->channels->first()) : null;
                $this->executeTriggers('member_ban', $ban, $channel);
            });

            $discord->on(Event::GUILD_BAN_REMOVE, function ($ban, Discord $discord) {
                $username = ($ban->user) ? $ban->user->username : 'Unknown User';
                $this->log("EVENT", "Member unbanned: @{$username}");
                $guild = $ban->guild ?? null;
                $channel = $guild ? ($guild->system_channel ?? $guild->channels->first()) : null;
                $this->executeTriggers('member_unban', $ban, $channel);
            });

            $discord->on(Event::VOICE_STATE_UPDATE, function ($voiceState, Discord $discord, $oldVoiceState) {
                $this->log("EVENT", "Voice state updated for user ID: {$voiceState->user_id}");
                $channel = $voiceState->channel;
                $this->executeTriggers('voice_update', $voiceState, $channel);
            });

            $discord->on(Event::INVITE_CREATE, function ($invite, Discord $discord) {
                $this->log("EVENT", "Invite created: {$invite->code}");
                $channel = $invite->channel;
                $this->executeTriggers('invite_update', $invite, $channel);
            });

            $discord->on(Event::THREAD_CREATE, function (Channel $thread, Discord $discord) {
                $this->log("EVENT", "Thread created: {$thread->name}");
                $this->executeTriggers('thread_update', $thread, $thread);
            });

            $discord->on(Event::GUILD_MEMBER_UPDATE, function (Member $member, Discord $discord, ?Member $oldMember) {
                $this->log("EVENT", "Member profile updated: @{$member->user->username}");
                $guild = $member->guild ?? null;
                $channel = $guild ? ($guild->system_channel ?? $guild->channels->first()) : null;
                $this->executeTriggers('member_update', $member, $channel);
            });

            $discord->on(Event::GUILD_EMOJIS_UPDATE, function ($guild, $emojis, $oldEmojis, Discord $discord) {
                $this->log("EVENT", "Guild emojis updated");
                $channel = $guild ? ($guild->system_channel ?? $guild->channels->first()) : null;
                $this->executeTriggers('emoji_update', $guild, $channel);
            });

            $discord->on(Event::AUTO_MODERATION_ACTION_EXECUTION, function ($actionExecution, Discord $discord) {
                $this->log("EVENT", "AutoMod rule execution triggered");
                $guild = $actionExecution->guild ?? null;
                $channel = $guild ? $guild->channels->get('id', $actionExecution->channel_id) : null;
                $this->executeTriggers('auto_moderation_exec', $actionExecution, $channel);
            });

            $discord->on(Event::GUILD_AUDIT_LOG_ENTRY_CREATE, function ($entry, Discord $discord) {
                $this->log("EVENT", "Audit log entry created: ID {$entry->id}");
                $guild = $entry->guild ?? null;
                $channel = $guild ? ($guild->system_channel ?? $guild->channels->first()) : null;
                $this->executeTriggers('audit_log_create', $entry, $channel);
            });

            $discord->on(Event::GUILD_SCHEDULED_EVENT_UPDATE, function ($event, Discord $discord, $oldEvent) {
                $this->log("EVENT", "Scheduled event updated: {$event->name}");
                $guild = $event->guild ?? null;
                $channel = $guild ? ($guild->system_channel ?? $guild->channels->first()) : null;
                $this->executeTriggers('scheduled_event_update', $event, $channel);
            });

            $discord->on(Event::GUILD_UPDATE, function ($guild, Discord $discord, $oldGuild) {
                if ($oldGuild && $guild && $guild->premium_tier !== $oldGuild->premium_tier) {
                    $this->log("EVENT", "Guild premium boost tier updated to Tier {$guild->premium_tier}");
                    $channel = $guild->system_channel ?? $guild->channels->first();
                    $this->executeTriggers('boost_event', $guild, $channel);
                }
            });

            $discord->on(Event::MESSAGE_POLL_VOTE_ADD, function ($vote, Discord $discord) {
                $this->log("EVENT", "Vote added to poll");
                $guild = $vote->guild ?? null;
                $channel = $guild ? $guild->channels->get('id', $vote->channel_id) : null;
                $this->executeTriggers('poll_vote', $vote, $channel);
            });

            $discord->on(Event::STAGE_INSTANCE_CREATE, function ($stageInstance, Discord $discord) {
                $this->log("EVENT", "Stage instance live: topic {$stageInstance->topic}");
                $guild = $stageInstance->guild ?? null;
                $channel = $guild ? $guild->channels->get('id', $stageInstance->channel_id) : null;
                $this->executeTriggers('stage_instance_update', $stageInstance, $channel);
            });

            $discord->on(Event::TYPING_START, function ($typing, Discord $discord) {
                $channel = $typing->channel;
                $this->executeTriggers('typing_start', $typing, $channel);
            });

            foreach ($this->logicBlocks as $block) {
                if ($block['type'] === 'trigger' && $block['event'] === 'command') {
                    $commandName = $block['trigger_val'] ?? '';
                    if (empty($commandName)) continue;

                    $options = [];
                    if (isset($block['command_params']) && is_array($block['command_params'])) {
                        foreach ($block['command_params'] as $param) {
                            $typeMap = [
                                'string' => 3,
                                'number' => 10,
                                'integer' => 4,
                                'boolean' => 5,
                                'user' => 6,
                                'channel' => 7,
                                'role' => 8,
                                'mentionable' => 9,
                                'attachment' => 11
                            ];
                            $paramType = $typeMap[$param['type'] ?? 'string'] ?? 3;
                            $options[] = [
                                'name' => strtolower($param['name']),
                                'description' => $param['desc'] ?? "Parameter {$param['name']}",
                                'type' => $paramType,
                                'required' => (bool)($param['required'] ?? false),
                            ];
                        }
                    }

                    try {
                        $commandDesc = !empty($block['adv_desc']) ? $block['adv_desc'] : "Custom command created with HublyBot";
                        $discord->application->commands->save(
                            $discord->application->commands->create([
                                'name' => strtolower($commandName),
                                'description' => $commandDesc,
                                'options' => $options
                            ])
                        );
                        $this->log("SYSTEM", "Registered Slash Command: /" . strtolower($commandName));
                    } catch (\Exception $e) {
                        $this->log("WARNING", "Failed to register command /{$commandName}: " . $e->getMessage());
                    }

                    $discord->listenCommand(strtolower($commandName), function (Interaction $interaction) use ($block) {
                        
                        $args = [];
                        if ($interaction->data && $interaction->data->options) {
                            foreach ($interaction->data->options as $option) {
                                $args[$option->name] = $option->value;
                            }
                        }

                        $user = $interaction->member ? $interaction->member->user : $interaction->user;
                        $username = $user ? $user->username : 'Unknown';
                        $this->log("EVENT", "Slash command used: /{$block['trigger_val']} by @{$username}");

                        $actionIds = $block['targetActionIds'] ?? ($block['targetActionId'] ? [$block['targetActionId']] : []);
                        
                        $hasResponded = false;
                        foreach ($actionIds as $actionId) {
                            if (isset($this->logicBlocks[$actionId])) {
                                $action = $this->logicBlocks[$actionId];
                                if ($action['type'] === 'action') {
                                    $text = $action['action_val'] ?? '';
                                    $text = $this->replacePlaceholders($text, $interaction, $interaction->channel, $args);

                                    if (($action['action_type'] === 'reply_direct' || $action['action_type'] === 'reply') && !$hasResponded) {
                                        $targetChannelNameOrId = $action['channel_val'] ?? '';
                                        if ($action['action_type'] === 'reply_direct' || empty($targetChannelNameOrId) || $targetChannelNameOrId === '{same}') {
                                            try {
                                                $interaction->respondWithMessage($text);
                                                $this->log("LOGIC", "Executing Trigger -> Responded to interaction slash command");
                                                $hasResponded = true;
                                                continue;
                                            } catch (\Exception $e) {}
                                        }
                                    }

                                    $this->executeAction($actionId, $interaction, $interaction->channel, $args);
                                }
                            }
                        }

                        if (!$hasResponded) {
                            try {
                                $interaction->acknowledge();
                            } catch (\Exception $e) {}
                        }
                    });
                }
            }
        });

        $this->discord->run();
    }

    protected function loadLogicBlocks()
    {
        $settings = $this->bot->settings;
        if (!$settings || !isset($settings['tree'])) {
            return;
        }

        $this->flattenTree($settings['tree']);
    }

    protected function flattenTree($nodes)
    {
        foreach ($nodes as $node) {
            if ($node['type'] === 'file' && isset($node['logic'])) {
                foreach ($node['logic'] as $block) {
                    $this->logicBlocks[$block['id']] = $block;
                }
            } elseif ($node['type'] === 'folder' && isset($node['children'])) {
                $this->flattenTree($node['children']);
            }
        }
    }

    protected function executeTriggers($event, $context, ?Channel $channel, array $args = [])
    {
        foreach ($this->logicBlocks as $block) {
            if ($block['type'] === 'trigger' && $block['event'] === $event) {
                
                if ($event === 'message' && !empty($block['trigger_val'])) {
                    $keyword = strtolower($block['trigger_val']);
                    $content = strtolower($context->content);
                    if (strpos($content, $keyword) === false) {
                        continue;
                    }
                }

                $actionIds = $block['targetActionIds'] ?? ($block['targetActionId'] ? [$block['targetActionId']] : []);
                foreach ($actionIds as $actionId) {
                    $this->executeAction($actionId, $context, $channel, $args);
                }
            }
        }
    }

    protected function executeAction($actionId, $context, ?Channel $channel, array $args = [])
    {
        if (!isset($this->logicBlocks[$actionId])) {
            return;
        }

        $block = $this->logicBlocks[$actionId];

        if ($block['type'] === 'condition') {
            if ($this->evaluateCondition($block, $context, $channel, $args)) {
                $nextIds = $block['targetActionIds'] ?? (isset($block['targetActionId']) ? [$block['targetActionId']] : []);
                foreach ($nextIds as $nextId) {
                    $this->executeAction($nextId, $context, $channel, $args);
                }
            }
            return;
        }

        if ($block['type'] !== 'action') {
            return;
        }

        $actionType = $block['action_type'] ?? '';
        $actionVal = $block['action_val'] ?? '';
        $channelVal = $block['channel_val'] ?? '';

        $text = $this->replacePlaceholders($actionVal, $context, $channel, $args);
        $targetChannelNameOrId = $this->replacePlaceholders($channelVal, $context, $channel, $args);

        $guild = null;
        $member = null;
        $user = null;

        if ($context instanceof Message) {
            $guild = $context->guild;
            $member = $context->member;
            $user = $context->author;
        } elseif ($context instanceof Member) {
            $guild = $context->guild;
            $member = $context;
            $user = $context->user;
        } elseif ($context instanceof Interaction) {
            $guild = $context->guild;
            $member = $context->member;
            $user = $context->user;
        } elseif ($context instanceof Reaction) {
            $guild = $context->guild;
            $member = $context->member;
            $user = $context->user;
        }

        switch ($actionType) {
            case 'reply':
                if (empty($targetChannelNameOrId) || $targetChannelNameOrId === '{same}') {
                    if ($channel) {
                        $channel->sendMessage($text);
                        $this->log("LOGIC", "Sent message in #{$channel->name}");
                    }
                } else {
                    $targetChannel = $this->findChannel($guild, $targetChannelNameOrId);
                    if ($targetChannel) {
                        $targetChannel->sendMessage($text);
                        $this->log("LOGIC", "Sent message in #{$targetChannel->name}");
                    } else {
                        $this->log("WARNING", "Channel \"{$targetChannelNameOrId}\" not found.");
                    }
                }
                break;

            case 'reply_direct':
                if ($context instanceof Message) {
                    $context->reply($text);
                    $this->log("LOGIC", "Replied to message");
                } elseif ($context instanceof Interaction) {
                    try {
                        $context->respondWithMessage($text);
                        $this->log("LOGIC", "Responded to interaction");
                    } catch (\Exception $e) {
                        if ($channel) $channel->sendMessage($text);
                    }
                } else {
                    if ($channel) $channel->sendMessage($text);
                }
                break;

            case 'send_console':
                $this->log("LOGIC", "Console: {$text}");
                break;

            case 'dm':
                if ($user) {
                    $user->sendMessage($text)->then(function() {
                        $this->log("LOGIC", "Sent Direct Message");
                    }, function($e) {
                        $this->log("WARNING", "Failed to send DM: " . $e->getMessage());
                    });
                }
                break;

            case 'role':
            case 'remove_role':
                if ($guild && $member) {
                    $cleanRole = str_replace(['<@&', '>'], '', $text);
                    $roleObj = $guild->roles->get('id', $cleanRole) ?: $guild->roles->get('name', $text);
                    if ($roleObj) {
                        if ($actionType === 'role') {
                            $member->addRole($roleObj)->then(function() use ($roleObj) {
                                $this->log("LOGIC", "Assigned role: {$roleObj->name}");
                            });
                        } else {
                            $member->removeRole($roleObj)->then(function() use ($roleObj) {
                                $this->log("LOGIC", "Removed role: {$roleObj->name}");
                            });
                        }
                    } else {
                        $this->log("WARNING", "Role \"{$text}\" not found.");
                    }
                }
                break;

            case 'kick_member':
                if ($guild && $member) {
                    $reason = $block['adv_reason'] ?? "Kicked by HublyBot logic flow";
                    $member->kick($reason)->then(function() {
                        $this->log("LOGIC", "Kicked member");
                    });
                }
                break;

            case 'ban_member':
                if ($guild && $member) {
                    $reason = $block['adv_reason'] ?? "Banned by HublyBot logic flow";
                    $deleteDays = (int)($block['adv_delete_days'] ?? 0);
                    $guild->members->ban($member, $deleteDays, $reason)->then(function() {
                        $this->log("LOGIC", "Banned member");
                    });
                }
                break;

            case 'timeout_member':
                if ($member) {
                    $minutes = (int)$text;
                    if ($minutes > 0) {
                        $communicationDisabledUntil = new \DateTime("+$minutes minutes");
                        $member->fill(['communication_disabled_until' => $communicationDisabledUntil->format(\DateTime::ISO8601)]);
                        $member->save()->then(function() use ($minutes) {
                            $this->log("LOGIC", "Timed out member for {$minutes} minutes");
                        });
                    }
                }
                break;

            case 'create_channel':
                if ($guild) {
                    $typeMap = [
                        'text' => Channel::TYPE_TEXT,
                        'voice' => Channel::TYPE_VOICE,
                        'category' => Channel::TYPE_CATEGORY
                    ];
                    $cType = $typeMap[$block['adv_channel_type'] ?? 'text'] ?? Channel::TYPE_TEXT;
                    $cTopic = $block['adv_channel_topic'] ?? '';
                    $cNsfw = (bool)($block['adv_nsfw'] ?? false);

                    $guild->channels->save($guild->channels->create([
                        'name' => $text,
                        'type' => $cType,
                        'topic' => $cTopic,
                        'nsfw' => $cNsfw
                    ]))->then(function($newChan) {
                        $this->log("LOGIC", "Created channel: #{$newChan->name}");
                    });
                }
                break;

            case 'delete_channel':
                $targetChannel = $this->findChannel($guild, $targetChannelNameOrId);
                if ($targetChannel) {
                    $guild->channels->delete($targetChannel)->then(function() {
                        $this->log("LOGIC", "Deleted channel");
                    });
                }
                break;

            case 'add_reaction':
                if ($context instanceof Message) {
                    $context->react($text)->then(function() {
                        $this->log("LOGIC", "Reacted with {$text}");
                    });
                }
                break;

            case 'pin_message':
                if ($context instanceof Message) {
                    $context->pin()->then(function() {
                        $this->log("LOGIC", "Pinned message");
                    });
                }
                break;

            case 'unpin_message':
                if ($context instanceof Message) {
                    $context->unpin()->then(function() {
                        $this->log("LOGIC", "Unpinned message");
                    });
                }
                break;

            case 'create_thread':
                if ($context instanceof Message) {
                    $context->startThread([
                        'name' => $text,
                        'auto_archive_duration' => 60
                    ])->then(function($thread) {
                        $this->log("LOGIC", "Created thread: {$thread->name}");
                    });
                }
                break;

            case 'send_webhook':
                $method = $block['adv_webhook_method'] ?? 'POST';
                $headers = json_decode($block['adv_webhook_headers'] ?? '{}', true) ?: [];
                
                $client = new \GuzzleHttp\Client();
                try {
                    $options = [
                        'headers' => array_merge(['Content-Type' => 'application/json'], $headers)
                    ];
                    if ($method === 'GET') {
                        
                    } else {
                        $options['body'] = $text;
                    }
                    $client->requestAsync($method, $targetChannelNameOrId, $options)->then(function($res) {
                        $this->log("LOGIC", "Webhook request sent: Status " . $res->getStatusCode());
                    });
                } catch (\Exception $e) {
                    $this->log("WARNING", "Webhook error: " . $e->getMessage());
                }
                break;

            case 'wait_delay':
                $delaySecs = (float)$text;
                if ($delaySecs > 0) {
                    $this->log("LOGIC", "Flow paused for {$delaySecs} seconds...");
                    $nextIds = $block['targetActionIds'] ?? (isset($block['targetActionId']) ? [$block['targetActionId']] : []);

                    $this->discord->getLoop()->addTimer($delaySecs, function() use ($nextIds, $context, $channel, $args) {
                        foreach ($nextIds as $nextId) {
                            $this->executeAction($nextId, $context, $channel, $args);
                        }
                    });
                    return; 
                }
                break;

            case 'send_embed':
                if ($channel) {
                    $embed = new \Discord\Parts\Embed\Embed($this->discord);
                    if (!empty($block['embed_title'])) $embed->setTitle($this->replacePlaceholders($block['embed_title'], $context, $channel, $args));
                    if (!empty($block['embed_desc'])) $embed->setDescription($this->replacePlaceholders($block['embed_desc'], $context, $channel, $args));
                    if (!empty($block['embed_color'])) {
                        $cleanColor = str_replace('#', '', $block['embed_color']);
                        $embed->setColor(hexdec($cleanColor));
                    }
                    if (!empty($block['embed_image'])) {
                        $embed->setImage($block['embed_image']);
                    }
                    $channel->sendEmbed($embed)->then(function() {
                        $this->log("LOGIC", "Sent Rich Embed announcement");
                    });
                }
                break;

            case 'set_nickname':
                if ($member) {
                    $member->setNickname($text)->then(function() {
                        $this->log("LOGIC", "Updated member nickname");
                    });
                }
                break;

            case 'lock_channel':
            case 'unlock_channel':
                $targetChannel = $this->findChannel($guild, $targetChannelNameOrId) ?: $channel;
                if ($targetChannel && $guild) {
                    
                    $everyoneRole = $guild->roles->get('id', $guild->id); 
                    if ($everyoneRole) {
                        $overwrite = $targetChannel->permission_overwrites->get('id', $everyoneRole->id);
                        if (!$overwrite) {
                            $overwrite = $targetChannel->permission_overwrites->create([
                                'id' => $everyoneRole->id,
                                'type' => 0 
                            ]);
                        }
                        
                        $permissions = $overwrite->deny;
                        if ($actionType === 'lock_channel') {
                            $permissions->send_messages = true;
                        } else {
                            $permissions->send_messages = false; 
                        }
                        
                        $overwrite->fill(['deny' => $permissions]);
                        $targetChannel->permission_overwrites->save($overwrite)->then(function() use ($actionType) {
                            $this->log("LOGIC", "Channel permission updated -> " . $actionType);
                        });
                    }
                }
                break;

            case 'delete_message':
                if ($context instanceof Message) {
                    $context->delete()->then(function() {
                        $this->log("LOGIC", "Deleted triggering message");
                    });
                }
                break;



            case 'create_event':
                if ($guild) {
                    $event = $guild->guild_scheduled_events->create([
                        'channel_id' => $block['adv_event_channel'] ?? null,
                        'name' => $text,
                        'description' => $block['adv_event_desc'] ?? '',
                        'scheduled_start_time' => new \DateTime('+1 day'),
                        'privacy_level' => ScheduledEvent::PRIVACY_LEVEL_GUILD_ONLY,
                        'entity_type' => ScheduledEvent::ENTITY_TYPE_VOICE
                    ]);
                    $guild->guild_scheduled_events->save($event)->then(function($newEvent) {
                        $this->log("LOGIC", "Created Scheduled Event: {$newEvent->name}");
                    }, function($e) {
                        $this->log("WARNING", "Failed to create scheduled event: " . $e->getMessage());
                    });
                }
                break;

            case 'stage_start':
                if ($guild) {
                    $stageChannel = $this->findChannel($guild, $text);
                    if ($stageChannel && $stageChannel->type === Channel::TYPE_STAGE) {
                        $stageInstance = $guild->stage_instances->create([
                            'channel_id' => $stageChannel->id,
                            'topic' => $block['adv_topic'] ?? 'Topic Talk',
                            'privacy_level' => 1 
                        ]);
                        $guild->stage_instances->save($stageInstance)->then(function() {
                            $this->log("LOGIC", "Started Stage Instance topic: " . ($block['adv_topic'] ?? 'Topic Talk'));
                        }, function($e) {
                            $this->log("WARNING", "Failed to start Stage Instance: " . $e->getMessage());
                        });
                    }
                }
                break;

            case 'create_automod_rule':
                if ($guild) {
                    $keywords = array_map('trim', explode(',', $block['adv_keywords'] ?? ''));
                    $rule = $guild->auto_moderation_rules->create([
                        'name' => $text,
                        'event_type' => Rule::EVENT_TYPE_MESSAGE_SEND,
                        'trigger_type' => Rule::TRIGGER_TYPE_KEYWORD,
                        'trigger_metadata' => [
                            'keyword_filter' => $keywords
                        ],
                        'actions' => [
                            [
                                'type' => 1 
                            ]
                        ],
                        'enabled' => true
                    ]);
                    $guild->auto_moderation_rules->save($rule)->then(function($newRule) {
                        $this->log("LOGIC", "Created AutoMod Rule: {$newRule->name}");
                    }, function($e) {
                        $this->log("WARNING", "Failed to create AutoMod rule: " . $e->getMessage());
                    });
                }
                break;

            case 'send_poll':
                if ($channel) {
                    $answers = array_map('trim', explode(',', $block['adv_answers'] ?? ''));
                    $poll = new \Discord\Parts\Channel\Poll\Poll($this->discord);
                    $poll->setQuestion($text);
                    foreach ($answers as $ans) {
                        if (!empty($ans)) {
                            $poll->addAnswer($ans);
                        }
                    }
                    $poll->setDuration(24);
                    $builder = \Discord\Builders\MessageBuilder::new()->setPoll($poll);
                    
                    $channel->sendMessage($builder)->then(function() {
                        $this->log("LOGIC", "Sent Message Poll");
                    }, function($e) {
                        $this->log("WARNING", "Failed to send poll: " . $e->getMessage());
                    });
                }
                break;

        }

        if ($actionType !== 'wait_delay') {
            $nextIds = $block['targetActionIds'] ?? (isset($block['targetActionId']) ? [$block['targetActionId']] : []);
            foreach ($nextIds as $nextId) {
                $this->executeAction($nextId, $context, $channel, $args);
            }
        }
    }

    protected function evaluateCondition($block, $context, ?Channel $channel, array $args = [])
    {
        $condType = $block['condition_type'] ?? '';
        $condVal = $block['condition_val'] ?? '';
        $channelVal = $block['channel_val'] ?? '';

        $text = $this->replacePlaceholders($condVal, $context, $channel, $args);
        $targetChannelNameOrId = $this->replacePlaceholders($channelVal, $context, $channel, $args);

        $guild = null;
        $member = null;
        $user = null;
        $message = null;

        if ($context instanceof Message) {
            $guild = $context->guild;
            $member = $context->member;
            $user = $context->author;
            $message = $context;
        } elseif ($context instanceof Member) {
            $guild = $context->guild;
            $member = $context;
            $user = $context->user;
        } elseif ($context instanceof Interaction) {
            $guild = $context->guild;
            $member = $context->member;
            $user = $context->user;
        } elseif ($context instanceof Reaction) {
            $guild = $context->guild;
            $member = $context->member;
            $user = $context->user;
        }

        switch ($condType) {
            case 'cond_has_role':
                if ($guild && $member) {
                    $cleanRole = str_replace(['<@&', '>', '&'], '', $text);
                    $roleObj = $guild->roles->get('id', $cleanRole) ?: $guild->roles->get('name', $text);
                    if ($roleObj) {
                        return $member->roles->has($roleObj->id);
                    }
                }
                return false;

            case 'cond_channel_name':
                if ($channel) {
                    return strtolower($channel->name) === strtolower(str_replace('#', '', $text));
                }
                return false;

            case 'cond_random':
                $chance = (float)str_replace('%', '', $text);
                if ($chance <= 0) return false;
                if ($chance >= 100) return true;
                return (mt_rand(1, 100) <= $chance);

            case 'cond_is_bot':
                if ($user) {
                    return (bool)$user->bot;
                }
                return false;

            case 'cond_nsfw':
                if ($channel) {
                    return (bool)($channel->nsfw ?? false);
                }
                return false;

            case 'cond_has_link':
                if ($message) {
                    return (preg_match('/https?:\/\/[^\s]+/i', $message->content) === 1);
                }
                return false;

            case 'cond_has_attachments':
                if ($message) {
                    return count($message->attachments) > 0;
                }
                return false;

            case 'cond_user_perm':
                if ($member) {
                    $cleanPerm = strtolower(str_replace(' ', '_', $text));
                    if (isset($member->permissions)) {
                        return (bool)($member->permissions->$cleanPerm ?? false);
                    }
                }
                return false;

            case 'cond_msg_len':
                if ($message) {
                    $len = strlen($message->content);
                    $targetLen = (int)preg_replace('/[^0-9]/', '', $text);
                    if (strpos($text, '>') !== false) return $len > $targetLen;
                    if (strpos($text, '<') !== false) return $len < $targetLen;
                    return $len === $targetLen;
                }
                return false;

            case 'cond_user_id':
                if ($user) {
                    return $user->id === trim($text);
                }
                return false;

            case 'cond_channel_type':
                if ($channel) {
                    $targetType = strtolower($text);
                    if ($targetType === 'text' || $targetType === 'text channel') return $channel->type === Channel::TYPE_TEXT;
                    if ($targetType === 'voice' || $targetType === 'voice channel') return $channel->type === Channel::TYPE_VOICE;
                }
                return false;

            case 'cond_boost_tier':
                if ($guild) {
                    $tier = (int)$guild->premium_tier;
                    $targetTier = (int)preg_replace('/[^0-9]/', '', $text);
                    return $tier >= $targetTier;
                }
                return false;

            case 'cond_bot_perm':
                if ($guild && $guild->me) {
                    $cleanPerm = strtolower(str_replace(' ', '_', $text));
                    if (isset($guild->me->permissions)) {
                        return (bool)($guild->me->permissions->$cleanPerm ?? false);
                    }
                }
                return false;

            case 'cond_time_range':
                $startTime = trim($text); 
                $endTime = trim($block['adv_end_time'] ?? ''); 
                $allowedDays = array_map('trim', explode(',', strtolower($block['adv_days'] ?? 'mon,tue,wed,thu,fri,sat,sun')));
                
                $currentDay = strtolower(date('D'));
                $currentTime = date('H:i');
                
                $isInDay = in_array($currentDay, $allowedDays);
                $isInTime = true;
                
                if (!empty($startTime) && !empty($endTime)) {
                    if ($startTime <= $endTime) {
                        $isInTime = ($currentTime >= $startTime && $currentTime <= $endTime);
                    } else {
                        $isInTime = ($currentTime >= $startTime || $currentTime <= $endTime);
                    }
                }
                
                $result = ($isInDay && $isInTime);
                if (!empty($block['adv_invert_cond'])) {
                    return !$result;
                }
                return $result;

            case 'cond_account_age':
                if ($user) {
                    $days = (int)preg_replace('/[^0-9]/', '', $text);
                    $created = \Discord\getSnowflakeTimestamp($user->id);
                    $ageDays = (time() - $created) / 86400;
                    $result = ($ageDays >= $days);
                    if (!empty($block['adv_invert_cond'])) {
                        return !$result;
                    }
                    return $result;
                }
                return false;

            case 'cond_has_role_hierarchy':
                if ($guild && $member) {
                    $targetRole = $guild->roles->get('name', $text) ?: $guild->roles->get('id', $text);
                    if ($targetRole) {
                        $highestRolePosition = 0;
                        foreach ($member->roles as $r) {
                            if ($r->position > $highestRolePosition) {
                                $highestRolePosition = $r->position;
                            }
                        }
                        $operator = $block['adv_hierarchy_operator'] ?? 'higher';
                        if ($operator === 'higher') {
                            $result = ($highestRolePosition > $targetRole->position);
                        } else {
                            $result = ($highestRolePosition < $targetRole->position);
                        }
                        if (!empty($block['adv_invert_cond'])) {
                            return !$result;
                        }
                        return $result;
                    }
                }
                return false;
        }

        return false;
    }

    protected function findChannel($guild, $targetChannelNameOrId)
    {
        if (!$guild || empty($targetChannelNameOrId)) {
            return null;
        }
        $cleanTarget = str_replace('#', '', $targetChannelNameOrId);
        $targetChannel = $guild->channels->get('id', $cleanTarget);
        if (!$targetChannel) {
            $targetChannel = $guild->channels->get('name', $cleanTarget);
        }
        return $targetChannel;
    }

    protected function replacePlaceholders($text, $context, ?Channel $channel, array $args = [])
    {
        $userMention = '';
        $userName = '';
        $userId = '';
        $serverName = '';
        $channelName = $channel ? $channel->name : '';

        if ($context instanceof Message) {
            $userMention = "<@{$context->author->id}>";
            $userName = $context->author->username;
            $userId = $context->author->id;
            if ($context->guild) {
                $serverName = $context->guild->name;
            }
        } elseif ($context instanceof Member) {
            $userMention = "<@{$context->id}>";
            $userName = $context->user->username;
            $userId = $context->id;
            $serverName = $context->guild->name;
        } elseif ($context instanceof Interaction) {
            $user = $context->member ? $context->member->user : $context->user;
            if ($user) {
                $userMention = "<@{$user->id}>";
                $userName = $user->username;
                $userId = $user->id;
            }
            if ($context->guild) {
                $serverName = $context->guild->name;
            }
        }

        $placeholders = [
            '{user}' => $userMention,
            '{username}' => $userName,
            '{user.id}' => $userId,
            '{server}' => $serverName,
            '{channel}' => $channelName,
            '{date}' => date('Y-m-d'),
            '{time}' => date('H:i:s'),
        ];

        foreach ($args as $key => $val) {
            $placeholders["{arg.{$key}}"] = $val;
        }

        return str_replace(array_keys($placeholders), array_values($placeholders), $text);
    }

    protected function log($type, $message)
    {
        $now = date('Y-m-d H:i:s');
        $this->line("[{$now}] [{$type}] {$message}");
    }
}
