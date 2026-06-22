<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', function () {
    return redirect()->route('home');
})->name('login');

Route::get('/auth/redirect', [AuthController::class, 'redirectToDiscord'])->name('auth.redirect');
Route::get('/auth/callback', [AuthController::class, 'handleDiscordCallback'])->name('auth.callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/pricing', function () {
    return view('pricing', ['plans' => \App\Models\Plan::all()]);
})->name('pricing');

Route::get('/dashboard', function () {
    $latestOrder = auth()->user()->orders()->where('status', 'paid')->with('plan')->latest()->first();
    $cancelAtPeriodEnd = false;
    $cancelDate = null;
    
    if ($latestOrder && !empty($latestOrder->stripe_session_id)) {
        $secretKey = config('services.stripe.secret');
        if (!empty($secretKey)) {
            try {
                \Stripe\Stripe::setApiKey($secretKey);
                if (str_starts_with($latestOrder->stripe_session_id, 'sub_')) {
                    $subscription = \Stripe\Subscription::retrieve($latestOrder->stripe_session_id);
                } else {
                    $session = \Stripe\Checkout\Session::retrieve($latestOrder->stripe_session_id);
                    $subscription = !empty($session->subscription) ? \Stripe\Subscription::retrieve($session->subscription) : null;
                }
                
                if ($subscription) {
                    if (in_array($subscription->status, ['canceled', 'incomplete_expired'])) {
                        $latestOrder->update(['status' => 'cancelled']);
                        $latestOrder = auth()->user()->orders()->where('status', 'paid')->with('plan')->latest()->first();
                    } elseif ($subscription->cancel_at_period_end || !empty($subscription->cancel_at)) {
                        $cancelAtPeriodEnd = true;
                        $cancelTimestamp = $subscription->cancel_at ?? $subscription->current_period_end;
                        $cancelDate = \Carbon\Carbon::createFromTimestamp($cancelTimestamp)->format('M d, Y');
                    }
                }
            } catch (\Exception $e) {
                // Silently ignore Stripe connection errors
            }
        }
    }

    return view('dashboard', [
        'activeOrder' => $latestOrder,
        'cancelAtPeriodEnd' => $cancelAtPeriodEnd,
        'cancelDate' => $cancelDate,
    ]);
})->name('dashboard')->middleware('auth');

use App\Http\Controllers\CheckoutController;
Route::post('/subscription/cancel', [CheckoutController::class, 'cancelSubscription'])->name('subscription.cancel')->middleware('auth');
Route::get('/billing', function() { return redirect()->route('billing.subscription'); })->name('billing')->middleware('auth');
Route::get('/billing/subscription', [CheckoutController::class, 'showSubscriptionBilling'])->name('billing.subscription')->middleware('auth');
Route::get('/billing/card', [CheckoutController::class, 'showCardBilling'])->name('billing.card')->middleware('auth');
Route::get('/billing/invoices', [CheckoutController::class, 'showInvoicesBilling'])->name('billing.invoices')->middleware('auth');
Route::post('/billing/payment-method/update', [CheckoutController::class, 'updatePaymentMethod'])->name('billing.payment-method.update')->middleware('auth');
Route::post('/billing/payment-method/remove', [CheckoutController::class, 'removePaymentMethod'])->name('billing.payment-method.remove')->middleware('auth');
Route::get('/checkout/portal', function() { return redirect()->route('billing.subscription'); })->name('checkout.portal')->middleware('auth');
Route::get('/checkout/start/{plan}', [CheckoutController::class, 'startCheckout'])->name('checkout.start')->middleware('auth');
Route::get('/checkout/{custom_id}', [CheckoutController::class, 'showCart'])->name('checkout')->middleware('auth');
Route::post('/checkout/{custom_id}', [CheckoutController::class, 'processCart'])->name('checkout.process')->middleware('auth');
Route::get('/checkout/{custom_id}/payment', [CheckoutController::class, 'showPayment'])->name('checkout.payment')->middleware('auth');
Route::get('/checkout/{custom_id}/success', [CheckoutController::class, 'success'])->name('checkout.success')->middleware('auth');
Route::get('/checkout/{custom_id}/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel')->middleware('auth');
Route::get('/api/coupons/{code}', [CheckoutController::class, 'checkCoupon'])->name('api.coupons.check')->middleware('auth');
Route::get('/api/countries', [CheckoutController::class, 'getCountries'])->name('api.countries')->middleware('auth');

use App\Http\Controllers\BotController;

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/builder', function () {
        return view('products.builder');
    })->name('builder');

    Route::get('/hosting', function () {
        return view('products.hosting');
    })->name('hosting');

    Route::get('/stats', function () {
        return view('products.stats');
    })->name('stats');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/bots', [BotController::class, 'index'])->name('dashboard.bots');
    Route::get('/dashboard/bots/new', [BotController::class, 'create'])->name('dashboard.bots.create');
    Route::post('/dashboard/bots/store', [BotController::class, 'store'])->name('dashboard.bots.store');
    Route::get('/dashboard/bots/{bot}/setup', [BotController::class, 'setup'])->name('dashboard.bots.setup');
    Route::get('/dashboard/bots/{bot}/builder', [BotController::class, 'builder'])->name('dashboard.bots.builder');
    Route::get('/dashboard/bots/{bot}/hosting', [BotController::class, 'hosting'])->name('dashboard.bots.hosting');
    Route::get('/dashboard/bots/{bot}/edit', [BotController::class, 'edit'])->name('dashboard.bots.edit');
    Route::post('/dashboard/bots/{bot}/update', [BotController::class, 'update'])->name('dashboard.bots.update');
    Route::delete('/dashboard/bots/{bot}', [BotController::class, 'destroy'])->name('dashboard.bots.destroy');
    Route::post('/dashboard/bots/{bot}/start', [BotController::class, 'start'])->name('dashboard.bots.start');
    Route::post('/dashboard/bots/{bot}/stop', [BotController::class, 'stop'])->name('dashboard.bots.stop');
    Route::post('/dashboard/bots/{bot}/restart', [BotController::class, 'restart'])->name('dashboard.bots.restart');
    Route::get('/dashboard/bots/{bot}/logs', [BotController::class, 'logs'])->name('dashboard.bots.logs');
    Route::get('/dashboard/bots/{bot}/status', [BotController::class, 'status'])->name('dashboard.bots.status');
});


