<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Bot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

/**
 * Class BotController
 * 
 * Handles all dashboard operations for managing Discord bots, including
 * listing, creating, updating, deleting, starting, stopping, and viewing logs.
 */
class BotController extends Controller
{
    /**
     * Display a listing of the user's bots.
     */
    public function index()
    {
        $bots = Auth::user()->bots()->orderBy('created_at', 'desc')->get();
        return view('dashboard.bots.index', compact('bots'));
    }

    /**
     * Show the form for creating a new bot, checking subscription capacity limits.
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->bots()->count() >= $user->maxBotsLimit()) {
            return redirect()->route('dashboard.bots')
                ->with('error', 'You have reached the maximum number of bots allowed for your current subscription. Please upgrade to create more.');
        }
        return view('dashboard.bots.builder', ['tab' => 'create']);
    }

    /**
     * Store a newly created bot in the database.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->bots()->count() >= $user->maxBotsLimit()) {
            return redirect()->route('dashboard.bots')
                ->with('error', 'You have reached the maximum number of bots allowed for your current subscription. Please upgrade to create more.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'token' => 'nullable|string|max:255',
            'settings' => 'nullable|json',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $bot = new Bot([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'token' => $validated['token'] ?? null,
            'settings' => isset($validated['settings']) ? json_decode($validated['settings'], true) : [],
            'status' => $validated['status'] ?? 'inactive',
        ]);
        $bot->save();

        return redirect()->route('dashboard.bots.builder', $bot)
            ->with('success', 'Bot created successfully! Now you can design its logic.');
    }

    /**
     * Show the bot setup tab.
     */
    public function setup(Bot $bot)
    {
        if ($bot->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('dashboard.bots.builder', compact('bot'))->with('tab', 'setup');
    }

    /**
     * Show the visual logic builder tab.
     */
    public function builder(Bot $bot)
    {
        if ($bot->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('dashboard.bots.builder', compact('bot'))->with('tab', 'builder');
    }

    /**
     * Show the hosting/lifecycle monitoring tab.
     */
    public function hosting(Bot $bot)
    {
        if ($bot->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('dashboard.bots.builder', compact('bot'))->with('tab', 'hosting');
    }

    /**
     * Redirect to the setup page for the bot.
     */
    public function edit(Bot $bot)
    {
        if ($bot->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return redirect()->route('dashboard.bots.setup', $bot);
    }

    /**
     * Update the bot details or active configuration.
     */
    public function update(Request $request, Bot $bot)
    {
        if ($bot->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'token' => 'nullable|string|max:255',
            'settings' => 'nullable|json',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $bot->update([
            'name' => $validated['name'],
            'token' => $validated['token'] ?? null,
            'settings' => isset($validated['settings']) ? json_decode($validated['settings'], true) : [],
            'status' => $validated['status'] ?? 'inactive',
        ]);

        $nextTab = $request->input('current_tab', 'setup');
        $routeName = $nextTab === 'builder' ? 'dashboard.bots.builder' : 'dashboard.bots.setup';

        return redirect()->route($routeName, $bot)
            ->with('success', 'Bot updated successfully!');
    }

    /**
     * Delete a bot instance from the database.
     */
    public function destroy(Bot $bot)
    {
        if ($bot->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $bot->delete();

        return redirect()->route('dashboard.bots')
            ->with('success', 'Bot deleted successfully!');
    }

    /**
     * Start the Discord bot background process using artisan command.
     */
    public function start(Bot $bot)
    {
        if ($bot->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $pidFile = storage_path("app/bot_{$bot->id}.pid");
        if (File::exists($pidFile)) {
            $pid = (int) File::get($pidFile);
            if ($this->isProcessRunning($pid)) {
                return response()->json(['success' => true, 'message' => 'Bot is already running.']);
            }
        }

        $logFile = storage_path("logs/bot_{$bot->id}.log");
        if (File::exists($logFile)) {
            File::delete($logFile);
        }

        $artisan = base_path('artisan');
        $cmd = "php " . escapeshellarg($artisan) . " bot:run " . escapeshellarg($bot->id) . " > " . escapeshellarg($logFile) . " 2>&1";
        
        // Spawn background worker process asynchronously depending on OS platform
        if (substr(php_uname(), 0, 7) == "Windows") {
            pclose(popen("start /B " . $cmd, "r"));
        } else {
            exec($cmd . " > /dev/null &");
        }

        $bot->update(['status' => 'active']);

        return response()->json(['success' => true, 'message' => 'Bot started successfully.']);
    }

    /**
     * Stop the running Discord bot background process.
     */
    public function stop(Bot $bot)
    {
        if ($bot->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $pidFile = storage_path("app/bot_{$bot->id}.pid");
        if (File::exists($pidFile)) {
            $pid = (int) File::get($pidFile);
            if ($this->isProcessRunning($pid)) {
                if (substr(php_uname(), 0, 7) == "Windows") {
                    exec("taskkill /F /PID {$pid}");
                } else {
                    exec("kill -9 {$pid}");
                }
            }
            File::delete($pidFile);
        }

        $bot->update(['status' => 'inactive']);

        return response()->json(['success' => true, 'message' => 'Bot stopped successfully.']);
    }

    /**
     * Restart the bot process.
     */
    public function restart(Bot $bot)
    {
        $this->stop($bot);
        sleep(1);
        return $this->start($bot);
    }

    /**
     * Get live console log tail output for the bot.
     */
    public function logs(Bot $bot)
    {
        if ($bot->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $logFile = storage_path("logs/bot_{$bot->id}.log");
        if (!File::exists($logFile)) {
            return response()->json(['logs' => ""]);
        }

        $content = File::get($logFile);
        return response()->json(['logs' => $content]);
    }

    /**
     * Get the current status, PID, and uptime of the bot process.
     * Enforces the 2-hour timeout for non-premium users.
     */
    public function status(Bot $bot)
    {
        if ($bot->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $pidFile = storage_path("app/bot_{$bot->id}.pid");
        $isRunning = false;
        $pidValue = 'N/A';
        $uptimeSeconds = 0;

        if (File::exists($pidFile)) {
            $pid = (int) File::get($pidFile);
            $isRunning = $this->isProcessRunning($pid);
            if ($isRunning) {
                $pidValue = "#hb-{$pid}";
                $uptimeSeconds = time() - File::lastModified($pidFile);

                // Enforce 2-hour session limits on free plans
                if (Auth::user()->activePlan() === null && $uptimeSeconds >= 7200) {
                    $this->stop($bot);
                    $isRunning = false;
                    $pidValue = 'N/A';
                    $uptimeSeconds = 0;
                }
            }
        }

        return response()->json([
            'status' => $isRunning ? 'online' : 'offline',
            'pid' => $pidValue,
            'uptime' => $uptimeSeconds,
            'is_premium' => Auth::user()->activePlan() !== null
        ]);
    }

    /**
     * Helper to check if a process ID is running on Windows or POSIX systems.
     */
    protected function isProcessRunning($pid)
    {
        if (substr(php_uname(), 0, 7) == "Windows") {
            $output = [];
            exec("tasklist /FI \"PID eq {$pid}\"", $output);
            return count($output) > 3; 
        } else {
            if (function_exists('posix_kill')) {
                return posix_kill($pid, 0);
            }
            return file_exists("/proc/{$pid}");
        }
    }
}
