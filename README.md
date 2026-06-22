# HublyBot - Visual Discord Bot Builder & Hosting Platform

HublyBot is a premium no-code Discord bot building and hosting SaaS platform. Built on top of Laravel, it features a futuristic dark-themed dashboard, an interactive drag-and-drop visual logic canvas, real-time process monitoring (console logs/telemetry), and subscription monetization integrated via Stripe.

---

## Key Features

- **No-Code Logic Builder**: A drag-and-drop node workspace allowing users to map triggers (message keywords, slash commands, member updates, reactions) to custom actions (sending text/embeds, role assignment, timeouts/kicks/bans, API webhooks, delay nodes).
- **Process Manager**: Dedicated background worker runtime utilizing `discord-php` to boot, monitor, and manage the live lifecycle of multiple bot instances.
- **Real-Time Logs & Telemetry**: Full terminal emulator rendering live bot stdout outputs alongside resource allocation telemetry (CPU / Memory).
- **Monetization & Plans**: Tier-based pricing plans (Free vs. Premium/Pro) enforcing maximum bot capacity limits, session timeouts, and resource constraints, integrated with Stripe checkout and subscription portals.
- **Discord OAuth2 Integration**: Seamless user authentication directly utilizing Discord Socialite configuration.
- **Modern Cyber Aesthetics**: Sleek glassmorphism visual system styled with premium dark palettes, glows, custom layouts, and Lucide icons.

---

## Technical Stack

- **Backend**: Laravel 11, PHP 8.2+, SQLite/MySQL.
- **Bot Engine**: `team-reflex/discord-php` library.
- **Payment Processing**: Stripe API integrations (Checkout Sessions, Setup Intents, Customer portal, and Webhooks).
- **Frontend**: Blade templates, TailwindCSS, Vite, Lucide Icons, and Vanilla JavaScript.

---

## Installation & Setup

### 1. Prerequisites
- **PHP 8.2 or higher** with `ext-curl` enabled in your `php.ini`.
- **Composer** and **Node.js** (NPM).

### 2. Clone and Install Dependencies
```bash
git clone https://github.com/ArthurAugis/HublyBot.git
cd HublyBot

# Install Laravel dependencies
composer install

# Install node packages
npm install
```

### 3. Environment Setup
Copy the example environment file and generate the application key:
```bash
cp .env.example .env
php artisan key:generate
```

Configure your Discord OAuth application credentials and Stripe API credentials inside your `.env` file:
```env
DISCORD_CLIENT_ID=your_client_id
DISCORD_CLIENT_SECRET=your_client_secret
DISCORD_REDIRECT_URI=http://localhost:8000/auth/callback

STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

### 4. Database Setup & Seed
Initialize the database, run tables migrations, and load standard pricing plan seeds:
```bash
php artisan migrate --seed
```

### 5. Running the Application
Start the local PHP development server and Vite asset compiler:
```bash
# Run Laravel server
php artisan serve

# Run Vite dev server
npm run dev
```
Open `http://localhost:8000` to view the landing page and log in with your Discord account.

---

## Architecture Details

- **Bot Runtime**: Discord bot worker processes are managed as standalone command instances using `php artisan bot:run {bot_id}`.
- **Session Management**: Processes are tracked using local `.pid` files mapped to database instances.
- **Interactive Canvas**: The logic editor relies on native HTML5 drag-and-drop APIs and SVG path renderers to dynamically save logical flows directly into JSON records.

---

## License
HublyBot is open-sourced software licensed under the [MIT license](LICENSE).
