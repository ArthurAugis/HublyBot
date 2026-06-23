<h1 align="center">HublyBot</h1>

<p align="center">
  <strong>No-code Discord bot builder & SaaS hosting platform</strong><br/>
  Build, configure, and host Discord bots visually — no programming required.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat-square&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat-square&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/Stripe-Integrated-635BFF?style=flat-square&logo=stripe&logoColor=white"/>
  <img src="https://img.shields.io/badge/Discord-OAuth2-5865F2?style=flat-square&logo=discord&logoColor=white"/>
  <img src="https://img.shields.io/badge/License-MIT-green?style=flat-square"/>
</p>

---

## Overview

HublyBot is a full-stack SaaS platform that lets users create and host Discord bots through a visual no-code logic builder. Users authenticate via Discord OAuth2, design bot behaviors using a drag-and-drop node canvas, and deploy them instantly from the dashboard. Monetization is handled through Stripe subscriptions with tiered plans, proration support, coupon codes, and tax-aware checkout.

---

## Features

### 🤖 Visual Logic Builder
- Drag-and-drop canvas with **Trigger → Condition → Action** flow design
- Logic flows saved as JSON and loaded at runtime by the bot engine
- Supports folders and files for organizing logic blocks
- Slash command registration with typed parameters (string, integer, boolean, user, role, channel…)

### ⚡ Supported Event Triggers
| Trigger | Description |
|---|---|
| `message` | Keyword match in messages |
| `command` | Custom slash command |
| `join` / `leave` | Member joins or leaves the server |
| `member_update` | Member profile change |
| `reaction` | Reaction added to a message |
| `voice_update` | Voice state change |
| `message_edit` / `message_delete` | Message edits or deletions |
| `member_ban` / `member_unban` | Ban and unban events |
| `channel_create` / `role_create` | Server structure changes |
| `boost_event` | Server boost tier change |
| `invite_update` | Invite creation |
| `thread_update` | Thread creation |
| `emoji_update` | Emoji list changes |
| `auto_moderation_exec` | AutoMod rule execution |
| `audit_log_create` | Audit log entry |
| `scheduled_event_update` | Scheduled event changes |
| `poll_vote` | Poll vote added |
| `stage_instance_update` | Stage instance created |
| `typing_start` | User starts typing |
| `ready` | Bot startup |

### 🎬 Supported Actions
| Action | Description |
|---|---|
| `reply` | Send a message to a channel |
| `reply_direct` | Direct reply to a message or interaction |
| `dm` | Send a direct message to the user |
| `send_embed` | Send a rich embed with title, description, color, image |
| `role` / `remove_role` | Assign or remove a role |
| `kick_member` | Kick a member with a reason |
| `ban_member` | Ban a member with a reason and message deletion |
| `timeout_member` | Temporarily mute a member (minutes) |
| `set_nickname` | Change a member's server nickname |
| `create_channel` | Create a text, voice, or category channel |
| `delete_channel` | Delete a channel |
| `lock_channel` / `unlock_channel` | Toggle send permissions for @everyone |
| `add_reaction` | React to a message with an emoji |
| `pin_message` / `unpin_message` | Pin or unpin a message |
| `delete_message` | Delete the triggering message |
| `create_thread` | Start a thread from a message |
| `wait_delay` | Pause the flow for N seconds (async) |
| `send_webhook` | HTTP webhook request (GET/POST) with custom headers |
| `send_console` | Print a message to the bot console |
| `create_event` | Create a guild scheduled event |
| `stage_start` | Start a Stage instance |
| `create_automod_rule` | Create an AutoMod rule with keyword list |

### 💳 Billing & Subscriptions
- Tiered plans: **Free** (1 bot), **Premium** (3 bots, $7.99/mo), **Pro** (10 bots, $19.99/mo)
- Billing cycles: monthly, 12-month, or 24-month with discounted rates
- **Proration** when upgrading from Premium → Pro
- **Coupon codes** with configurable discount percentages
- Country-aware **tax calculation** (VAT/GST rates per country)
- Full Stripe integration: subscriptions, payment methods, invoices, cancellations
- Stripe Customer Portal equivalent: subscription management, card update, invoice history

### 🖥️ Bot Process Manager
- Each bot runs as an isolated background process via `php artisan bot:run {id}`
- Process lifecycle tracked with `.pid` files in `storage/app/`
- Live stdout logs streamed to `storage/logs/bot_{id}.log`
- Start, stop, restart controls from the dashboard
- Real-time status polling (online/offline, PID, uptime)
- **Free plan session limit**: bots auto-stop after 2 hours

### 🔐 Authentication
- Discord OAuth2 login via Laravel Socialite
- Stores Discord ID, avatar URL, and OAuth tokens
- Bot ownership enforced on all dashboard routes (403 on mismatch)

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Framework** | Laravel 13.x |
| **Language** | PHP 8.3+ |
| **Database** | SQLite (default) or MySQL |
| **Bot Engine** | `team-reflex/discord-php` ^10.48 |
| **Auth** | Laravel Socialite + `socialiteproviders/discord` |
| **Payments** | `stripe/stripe-php` ^20.2 |
| **Frontend** | Blade, TailwindCSS v4, Vite 8, Vanilla JS |
| **Queue** | Laravel Queue (database driver) |
| **Assets** | Lucide Icons (CDN), Google Fonts |

---

## Requirements

- **PHP 8.3+** with extensions: `curl`, `pcntl` (Linux) or process execution enabled (Windows)
- **Composer** 2.x
- **Node.js** 18+ and **npm**
- A [Discord Developer Application](https://discord.com/developers/applications) with OAuth2 configured
- A [Stripe account](https://stripe.com) with API keys (test or live)

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/ArthurAugis/HublyBot.git
cd HublyBot
```

### 2. One-command setup (recommended)

```bash
composer run setup
```

This single command will:
- Install all PHP dependencies (`composer install`)
- Copy `.env.example` → `.env` (if not already present)
- Generate the application key (`php artisan key:generate`)
- Run all database migrations (`php artisan migrate --force`)
- Install Node.js dependencies (`npm install`)
- Build frontend assets (`npm run build`)

> **Manual setup**: If you prefer step-by-step control, see the sections below.

### 3. Manual environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit your `.env` file with the following required values:

```env
# Application
APP_URL=http://localhost:8000

# Database (SQLite by default — no extra config needed)
DB_CONNECTION=sqlite

# Discord OAuth2 — from your Discord Developer Portal
DISCORD_CLIENT_ID=your_client_id
DISCORD_CLIENT_SECRET=your_client_secret
DISCORD_REDIRECT_URI=http://localhost:8000/auth/callback

# Stripe — from your Stripe Dashboard
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

### 4. Database & seed

```bash
php artisan migrate --seed
```

This creates all tables and seeds:
- **Plans**: `premium` ($7.99/mo) and `pro` ($19.99/mo)
- **Coupons**: `HUBLY20` (20%), `WELCOME10` (10%), and others
- **Countries**: full list of countries with applicable tax rates

### 5. Run the development environment

```bash
composer run dev
```

This starts all services concurrently:
- `php artisan serve` — Laravel web server on `http://localhost:8000`
- `php artisan queue:listen` — Background job worker
- `php artisan pail` — Real-time log viewer
- `npm run dev` — Vite asset hot-reload

---

## Application Routes

| Method | Route | Description |
|---|---|---|
| `GET` | `/` | Landing page |
| `GET` | `/pricing` | Public pricing page |
| `GET` | `/auth/redirect` | Discord OAuth2 redirect |
| `GET` | `/auth/callback` | Discord OAuth2 callback |
| `POST` | `/logout` | Logout |
| `GET` | `/dashboard` | User dashboard (auth) |
| `GET` | `/dashboard/bots` | Bot list (auth) |
| `GET` | `/dashboard/bots/new` | Create new bot (auth) |
| `GET` | `/dashboard/bots/{bot}/setup` | Bot setup tab (auth) |
| `GET` | `/dashboard/bots/{bot}/builder` | Logic builder tab (auth) |
| `GET` | `/dashboard/bots/{bot}/hosting` | Hosting/process tab (auth) |
| `POST` | `/dashboard/bots/{bot}/start` | Start bot process (auth) |
| `POST` | `/dashboard/bots/{bot}/stop` | Stop bot process (auth) |
| `POST` | `/dashboard/bots/{bot}/restart` | Restart bot process (auth) |
| `GET` | `/dashboard/bots/{bot}/logs` | Fetch live logs (auth) |
| `GET` | `/dashboard/bots/{bot}/status` | Process status/uptime (auth) |
| `GET` | `/checkout/start/{plan}` | Start subscription checkout (auth) |
| `GET` | `/billing/subscription` | Subscription management (auth) |
| `GET` | `/billing/card` | Payment method management (auth) |
| `GET` | `/billing/invoices` | Invoice history from Stripe (auth) |

---

## Data Models

| Model | Key Fields |
|---|---|
| `User` | `discord_id`, `avatar`, `discord_token`, relationships: `orders`, `bots` |
| `Bot` | `user_id`, `name`, `token`, `settings` (JSON logic tree), `status` |
| `Plan` | `slug`, `name`, `price_1`, `price_12`, `price_24`, `features` |
| `Order` | `custom_id`, `user_id`, `plan_id`, `months`, `status`, `stripe_session_id`, `subtotal`, `discount`, `tax`, `total`, `prorated_discount` |
| `Coupon` | `code`, `discount_percent`, `is_active` |
| `Country` | `code`, `name`, `tax_rate` |

---

## Subscription Plans

| Plan | Monthly | 12-month | 24-month | Max Bots |
|---|---|---|---|---|
| **Free** | — | — | — | 1 (2h sessions) |
| **Premium** | $7.99/mo | $6.39/mo | $5.59/mo | 3 |
| **Pro** | $19.99/mo | $15.99/mo | $13.99/mo | 10 |

---

## Architecture Notes

- **Bot runtime**: Each bot is a long-lived PHP process running DiscordPHP's ReactPHP event loop, started with `php artisan bot:run {id}`.
- **Intent system**: Required Gateway Intents are computed dynamically from the saved logic blocks — only the intents actually needed are requested.
- **PID management**: Process IDs are written to `storage/app/bot_{id}.pid` at startup and cleaned up on shutdown via a registered shutdown function.
- **Logic format**: Bot behaviors are stored as a JSON tree of `trigger`, `condition`, and `action` nodes inside the `bots.settings` column.
- **Free plan enforcement**: The `status` endpoint automatically stops processes that have run for ≥ 2 hours for users without an active paid plan.
- **Dynamic placeholder system**: Action values support context-aware variables like `{user}`, `{channel}`, `{content}` resolved at runtime.

---

## License

HublyBot is open-sourced software licensed under the [MIT license](LICENSE).
