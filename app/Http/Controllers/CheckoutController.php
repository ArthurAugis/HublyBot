<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Plan;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Support\Str;

/**
 * Class CheckoutController
 * 
 * Handles SaaS subscription checkouts, cart totals calculations, proration adjustments,
 * coupon applications, and Stripe integration endpoints.
 */
class CheckoutController extends Controller
{
    /**
     * Start the checkout process for a subscription plan, checking for active plan proration.
     */
    public function startCheckout($planSlug)
    {
        $plan = Plan::where('slug', $planSlug)->firstOrFail();

        $cycle = request()->query('cycle', 12);
        if (!in_array($cycle, [1, 12, 24])) {
            $cycle = 12;
        }

        $proratedDiscount = 0.00;
        $activeOrder = auth()->user()->orders()->where('status', 'paid')->with('plan')->latest()->first();
        if ($activeOrder && $activeOrder->plan->slug === 'premium' && $plan->slug === 'pro') {
            $daysInCycle = $activeOrder->months * 30;
            $daysUsed = max(0, $activeOrder->created_at->diffInDays(now()));
            $daysRemaining = max(0, $daysInCycle - $daysUsed);
            $proratedDiscount = round(($activeOrder->subtotal / $daysInCycle) * $daysRemaining, 2);
            if ($cycle < $activeOrder->months) {
                $cycle = $activeOrder->months;
            }
        }

        $order = Order::create([
            'custom_id' => 'ord_' . Str::lower(Str::random(12)),
            'user_id' => auth()->id(),
            'plan_id' => $plan->id,
            'months' => $cycle,
            'status' => 'pending',
            'prorated_discount' => $proratedDiscount,
        ]);

        return redirect()->route('checkout', ['custom_id' => $order->custom_id]);
    }

    /**
     * Show the shopping cart overview page for an order.
     */
    public function showCart($customId)
    {
        $order = Order::where('custom_id', $customId)->where('user_id', auth()->id())->firstOrFail();
        $plan = $order->plan;

        $activeOrder = auth()->user()->orders()
            ->where('status', 'paid')
            ->where('id', '!=', $order->id)
            ->with('plan')
            ->latest()
            ->first();

        return view('checkout_cart', [
            'order' => $order,
            'plan' => $plan,
            'activeOrder' => $activeOrder,
        ]);
    }

    /**
     * Process checkout form submission, calculating taxes, coupons, and discounts.
     */
    public function processCart(Request $request, $customId)
    {
        $order = Order::where('custom_id', $customId)->where('user_id', auth()->id())->firstOrFail();

        $validated = $request->validate([
            'months' => 'required|integer|in:1,12,24',
            'coupon' => 'nullable|string',
            'full_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'subtotal' => 'required|numeric',
            'discount' => 'required|numeric',
            'tax' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        $months = (int)$validated['months'];

        $activeOrder = auth()->user()->orders()
            ->where('status', 'paid')
            ->where('id', '!=', $order->id)
            ->with('plan')
            ->latest()
            ->first();

        if ($activeOrder && $activeOrder->plan->slug === 'premium' && $order->plan->slug === 'pro') {
            if ($months < $activeOrder->months) {
                return back()->with('error', 'You cannot choose a billing cycle shorter than your current subscription (' . $activeOrder->months . ' months).');
            }
        }

        $couponId = null;
        $discountPercent = 0.0;
        if (!empty($validated['coupon'])) {
            $coupon = Coupon::where('code', strtoupper($validated['coupon']))->where('is_active', true)->first();
            if ($coupon) {
                $couponId = $coupon->id;
                if (strtoupper($validated['coupon']) === 'BRAINROT') {
                    $discountPercent = -0.67;
                } else {
                    $discountPercent = $coupon->discount_percent / 100;
                }
            }
        }

        $plan = $order->plan;
        $months = (int)$validated['months'];
        $priceField = 'price_' . $months;
        $pricePerMonth = $plan->$priceField ?? $plan->price_1;
        $subtotal = $pricePerMonth * $months;
        $discount = $subtotal * $discountPercent;
        
        $proratedDiscount = (float)$order->prorated_discount;
        $priceAfterDiscountAndProration = max(0.00, $subtotal - $discount - $proratedDiscount);

        $countryInput = trim($validated['country'] ?? 'France');
        $dbCountry = \App\Models\Country::where('name', 'like', $countryInput)
            ->orWhere('code', 'like', $countryInput)
            ->first();
        
        $taxRate = $dbCountry ? (float)$dbCountry->tax_rate : 0.10;
        $tax = $priceAfterDiscountAndProration * $taxRate;
        $total = $priceAfterDiscountAndProration + $tax;

        $order->update([
            'months' => $months,
            'coupon_id' => $couponId,
            'full_name' => $validated['full_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $dbCountry ? $dbCountry->name : $countryInput,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
        ]);

        return redirect()->route('checkout.payment', ['custom_id' => $order->custom_id]);
    }

    /**
     * Display the payment options or handle Stripe Checkout redirect.
     */
    public function showPayment($customId)
    {
        $order = Order::where('custom_id', $customId)->where('user_id', auth()->id())->firstOrFail();
        $plan = $order->plan;

        $secretKey = config('services.stripe.secret');

        if (!empty($secretKey)) {
            Stripe::setApiKey($secretKey);

            try {
                
                $user = auth()->user();
                $customerId = null;
                $customers = \Stripe\Customer::all(['email' => $user->email, 'limit' => 1]);
                if (count($customers->data) > 0) {
                    $customerId = $customers->data[0]->id;
                } else {
                    $customer = \Stripe\Customer::create([
                        'email' => $user->email,
                        'name' => $user->name,
                    ]);
                    $customerId = $customer->id;
                }

                $productName = 'HublyBot ' . $plan->name;
                $productId = null;
                $products = \Stripe\Product::all(['limit' => 100]);
                foreach ($products->data as $prod) {
                    if (isset($prod->metadata['plan_slug']) && $prod->metadata['plan_slug'] === $plan->slug) {
                        $productId = $prod->id;
                        break;
                    }
                }
                if (!$productId) {
                    $product = \Stripe\Product::create([
                        'name' => $productName,
                        'metadata' => [
                            'plan_slug' => $plan->slug,
                        ],
                    ]);
                    $productId = $product->id;
                }

                $priceId = null;
                $amountInCents = intval(round($order->total * 100));
                $interval = $order->months == 1 ? 'month' : 'year';
                $interval_count = $order->months == 24 ? 2 : 1;

                $prices = \Stripe\Price::all(['product' => $productId, 'limit' => 100]);
                foreach ($prices->data as $pr) {
                    if ($pr->unit_amount == $amountInCents && 
                        $pr->currency == 'usd' && 
                        $pr->recurring && 
                        $pr->recurring->interval == $interval && 
                        $pr->recurring->interval_count == $interval_count) {
                        $priceId = $pr->id;
                        break;
                    }
                }

                if (!$priceId) {
                    $price = \Stripe\Price::create([
                        'product' => $productId,
                        'unit_amount' => $amountInCents,
                        'currency' => 'usd',
                        'recurring' => [
                            'interval' => $interval,
                            'interval_count' => $interval_count,
                        ],
                    ]);
                    $priceId = $price->id;
                }

                $defaultPaymentMethodId = null;
                $activeCard = null;
                try {
                    $customerObj = \Stripe\Customer::retrieve($customerId);
                    $defaultPaymentMethodId = $customerObj->invoice_settings->default_payment_method;
                } catch (\Exception $custEx) {}

                try {
                    $pms = \Stripe\PaymentMethod::all([
                        'customer' => $customerId,
                        'type' => 'card',
                        'limit' => 1,
                    ]);
                    if (count($pms->data) > 0) {
                        $pm = $pms->data[0];
                        $activeCard = [
                            'id' => $pm->id,
                            'brand' => $pm->card->brand,
                            'last4' => $pm->card->last4,
                            'exp_month' => $pm->card->exp_month,
                            'exp_year' => $pm->card->exp_year,
                        ];
                        if (!$defaultPaymentMethodId) {
                            $defaultPaymentMethodId = $pm->id;
                        }
                    }
                } catch (\Exception $pmEx) {}

                $subscriptionData = [
                    'customer' => $customerId,
                    'items' => [[
                        'price' => $priceId,
                    ]],
                    'payment_behavior' => 'default_incomplete',
                    'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
                ];

                if ($defaultPaymentMethodId) {
                    $subscriptionData['default_payment_method'] = $defaultPaymentMethodId;
                }

                $subscription = \Stripe\Subscription::create($subscriptionData);
                $order->update(['stripe_session_id' => $subscription->id]);

                $clientSecret = null;
                if ($subscription->latest_invoice) {
                    try {
                        $invoice = \Stripe\Invoice::retrieve($subscription->latest_invoice);
                        if ($invoice->payment_intent) {
                            $paymentIntent = \Stripe\PaymentIntent::retrieve($invoice->payment_intent);
                            $clientSecret = $paymentIntent->client_secret;
                        }
                    } catch (\Exception $piEx) {}
                }

                if (!$clientSecret) {
                    $piList = \Stripe\PaymentIntent::all([
                        'customer' => $customerId,
                        'limit' => 1,
                    ]);
                    $clientSecret = count($piList->data) > 0 ? $piList->data[0]->client_secret : null;
                }

                return view('checkout_payment', [
                    'order' => $order,
                    'plan' => $plan,
                    'stripeKey' => config('services.stripe.key'),
                    'clientSecret' => $clientSecret,
                    'activeCard' => $activeCard,
                ]);

            } catch (\Exception $e) {
                return redirect()->route('checkout', ['custom_id' => $order->custom_id])->with('error', 'Stripe error: ' . $e->getMessage());
            }
        }

        return view('checkout_payment', [
            'order' => $order,
            'plan' => $plan,
        ]);
    }

    /**
     * Cancel an active Stripe subscription, marking it to cancel at the end of current period.
     */
    public function cancelSubscription(Request $request)
    {
        $secretKey = config('services.stripe.secret');
        if (empty($secretKey)) {
            return redirect()->route('dashboard')->with('error', 'Stripe is not configured.');
        }

        $latestOrder = auth()->user()->orders()->where('status', 'paid')->with('plan')->latest()->first();

        if (!$latestOrder || empty($latestOrder->stripe_session_id)) {
            return redirect()->route('dashboard')->with('error', 'No active subscription found.');
        }

        try {
            \Stripe\Stripe::setApiKey($secretKey);

            if (str_starts_with($latestOrder->stripe_session_id, 'sub_')) {
                $subscriptionId = $latestOrder->stripe_session_id;
            } else {
                $session = \Stripe\Checkout\Session::retrieve($latestOrder->stripe_session_id);
                $subscriptionId = $session->subscription ?? null;
            }

            if ($subscriptionId) {
                \Stripe\Subscription::update($subscriptionId, [
                    'cancel_at_period_end' => true,
                ]);

                return redirect()->route('dashboard')->with('success', 'Your subscription will cancel at the end of the billing period.');
            }

            return redirect()->route('dashboard')->with('error', 'No active subscription could be resolved.');

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Stripe error: ' . $e->getMessage());
        }
    }

    /**
     * Handle Stripe success callback, marking the order paid and cancelling old plans.
     */
    public function success($customId)
    {
        $order = Order::where('custom_id', $customId)->where('user_id', auth()->id())->firstOrFail();
        $order->update(['status' => 'paid']);

        $secretKey = config('services.stripe.secret');
        if (!empty($secretKey)) {
            try {
                \Stripe\Stripe::setApiKey($secretKey);

                $previousPaidOrders = Order::where('user_id', auth()->id())
                    ->where('status', 'paid')
                    ->where('id', '!=', $order->id)
                    ->whereNotNull('stripe_session_id')
                    ->get();

                foreach ($previousPaidOrders as $oldOrder) {
                    $subscriptionId = null;
                    if (str_starts_with($oldOrder->stripe_session_id, 'sub_')) {
                        $subscriptionId = $oldOrder->stripe_session_id;
                    } else {
                        try {
                            $session = \Stripe\Checkout\Session::retrieve($oldOrder->stripe_session_id);
                            if ($session && !empty($session->subscription)) {
                                $subscriptionId = $session->subscription;
                            }
                        } catch (\Exception $sessEx) {
                            \Illuminate\Support\Facades\Log::warning('Failed to retrieve Stripe Checkout Session: ' . $sessEx->getMessage());
                        }
                    }
                    
                    if ($subscriptionId) {
                        try {
                            $subscription = \Stripe\Subscription::retrieve($subscriptionId);
                            if ($subscription && $subscription->status !== 'canceled') {
                                $subscription->cancel();
                            }
                        } catch (\Exception $subEx) {
                            \Illuminate\Support\Facades\Log::warning('Subscription already cancelled or missing: ' . $subEx->getMessage());
                        }
                    }
                    
                    $oldOrder->update(['status' => 'cancelled']);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to cancel old Stripe subscription: ' . $e->getMessage());
            }
        }

        return redirect()->route('dashboard')->with('success', 'Thank you! Your subscription to the ' . $order->plan->name . ' plan is now active.');
    }

    /**
     * Handle Stripe cancel callback, updating the order status.
     */
    public function cancel($customId)
    {
        $order = Order::where('custom_id', $customId)->where('user_id', auth()->id())->firstOrFail();
        $order->update(['status' => 'cancelled']);

        return redirect()->route('pricing')->with('error', 'Payment process was cancelled.');
    }

    /**
     * Validate a promo/coupon code and return discount details.
     */
    public function checkCoupon($code)
    {
        $coupon = Coupon::where('code', strtoupper($code))->where('is_active', true)->first();

        if ($coupon) {
            return response()->json([
                'valid' => true,
                'discount_percent' => $coupon->discount_percent,
            ]);
        }

        return response()->json([
            'valid' => false,
        ]);
    }

    /**
     * Fetch list of countries from restcountries.com API or local DB cache for tax calculation.
     */
    public function getCountries()
    {
        try {
            
            $response = \Illuminate\Support\Facades\Http::timeout(3)->get('https://restcountries.com/v3.1/all');
            
            if ($response->successful()) {
                $externalCountries = $response->json();
                $countries = [];

                foreach ($externalCountries as $c) {
                    $name = $c['name']['common'] ?? null;
                    $code = $c['cca2'] ?? null;
                    if (!$name || !$code) continue;

                    $dbCountry = \App\Models\Country::where('code', $code)->first();
                    if ($dbCountry) {
                        $taxRate = (float)$dbCountry->tax_rate;
                        
                        if ($dbCountry->name !== $name) {
                            $dbCountry->update(['name' => $name]);
                        }
                    } else {
                        
                        $taxRate = (strtoupper($code) === 'HK') ? 0.00 : 0.10;
                        \App\Models\Country::create([
                            'code' => $code,
                            'name' => $name,
                            'tax_rate' => $taxRate
                        ]);
                    }

                    $countries[] = [
                        'name' => $name,
                        'code' => $code,
                        'tax_rate' => number_format($taxRate, 4, '.', '')
                    ];
                }

                usort($countries, function ($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });

                return response()->json($countries);
            }
        } catch (\Exception $e) {
            
            \Illuminate\Support\Facades\Log::warning('Country API failed, loading from DB cache: ' . $e->getMessage());
        }

        $countries = \App\Models\Country::orderBy('name')->get()->map(function($c) {
            return [
                'name' => $c->name,
                'code' => $c->code,
                'tax_rate' => number_format((float)$c->tax_rate, 4, '.', '')
            ];
        });

        return response()->json($countries);
    }

    /**
     * Show the user's active subscription status in the billing section.
     */
    public function showSubscriptionBilling()
    {
        $secretKey = config('services.stripe.secret');
        $user = auth()->user();
        
        $latestOrder = $user->orders()->where('status', 'paid')->with('plan')->latest()->first();
        $cancelAtPeriodEnd = false;
        $cancelDate = null;

        if (!empty($secretKey)) {
            \Stripe\Stripe::setApiKey($secretKey);
            try {
                if ($latestOrder && !empty($latestOrder->stripe_session_id)) {
                    if (str_starts_with($latestOrder->stripe_session_id, 'sub_')) {
                        $subscription = \Stripe\Subscription::retrieve($latestOrder->stripe_session_id);
                    } else {
                        $session = \Stripe\Checkout\Session::retrieve($latestOrder->stripe_session_id);
                        $subscription = !empty($session->subscription) ? \Stripe\Subscription::retrieve($session->subscription) : null;
                    }

                    if ($subscription) {
                        if ($subscription->cancel_at_period_end || !empty($subscription->cancel_at)) {
                            $cancelAtPeriodEnd = true;
                            $cancelTimestamp = $subscription->cancel_at ?? $subscription->current_period_end;
                            $cancelDate = \Carbon\Carbon::createFromTimestamp($cancelTimestamp)->format('M d, Y');
                        }
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Stripe error in showSubscriptionBilling: ' . $e->getMessage());
            }
        }

        return view('billing_subscription', [
            'activeOrder' => $latestOrder,
            'cancelAtPeriodEnd' => $cancelAtPeriodEnd,
            'cancelDate' => $cancelDate,
        ]);
    }

    /**
     * Show card billing options and setup Stripe PaymentMethod setup session.
     */
    public function showCardBilling()
    {
        $secretKey = config('services.stripe.secret');
        $user = auth()->user();
        
        $activeCard = null;
        $setupIntentClientSecret = null;
        $stripeKey = config('services.stripe.key');

        if (!empty($secretKey)) {
            \Stripe\Stripe::setApiKey($secretKey);
            try {
                
                $customerId = null;
                $customers = \Stripe\Customer::all(['email' => $user->email, 'limit' => 1]);
                if (count($customers->data) > 0) {
                    $customer = $customers->data[0];
                    $customerId = $customer->id;
                } else {
                    $customer = \Stripe\Customer::create([
                        'email' => $user->email,
                        'name' => $user->name,
                    ]);
                    $customerId = $customer->id;
                }

                $pms = \Stripe\PaymentMethod::all([
                    'customer' => $customerId,
                    'type' => 'card',
                    'limit' => 1,
                ]);
                if (count($pms->data) > 0) {
                    $pm = $pms->data[0];
                    $activeCard = [
                        'id' => $pm->id,
                        'brand' => $pm->card->brand,
                        'last4' => $pm->card->last4,
                        'exp_month' => $pm->card->exp_month,
                        'exp_year' => $pm->card->exp_year,
                    ];
                }

                $setupIntent = \Stripe\SetupIntent::create([
                    'customer' => $customerId,
                    'payment_method_types' => ['card'],
                ]);
                $setupIntentClientSecret = $setupIntent->client_secret;

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Stripe error in showCardBilling: ' . $e->getMessage());
            }
        }

        return view('billing_card', [
            'activeCard' => $activeCard,
            'setupIntentClientSecret' => $setupIntentClientSecret,
            'stripeKey' => $stripeKey,
        ]);
    }

    /**
     * Display a listing of invoices retrieved directly from Stripe.
     */
    public function showInvoicesBilling()
    {
        $secretKey = config('services.stripe.secret');
        $user = auth()->user();
        
        $invoices = [];

        if (!empty($secretKey)) {
            \Stripe\Stripe::setApiKey($secretKey);
            try {
                
                $customerId = null;
                $customers = \Stripe\Customer::all(['email' => $user->email, 'limit' => 1]);
                if (count($customers->data) > 0) {
                    $customer = $customers->data[0];
                    $customerId = $customer->id;
                } else {
                    $customer = \Stripe\Customer::create([
                        'email' => $user->email,
                        'name' => $user->name,
                    ]);
                    $customerId = $customer->id;
                }

                $stripeInvoices = \Stripe\Invoice::all(['customer' => $customerId, 'limit' => 10]);
                foreach ($stripeInvoices->data as $inv) {
                    $invoices[] = [
                        'id' => $inv->id,
                        'number' => $inv->number ?? ('INV-' . \Carbon\Carbon::createFromTimestamp($inv->created)->format('Y') . '-' . sprintf('%03d', count($stripeInvoices->data) - count($invoices))),
                        'date' => \Carbon\Carbon::createFromTimestamp($inv->created)->format('M d, Y'),
                        'amount' => number_format($inv->total / 100, 2),
                        'currency' => strtoupper($inv->currency),
                        'status' => $inv->status,
                        'pdf' => $inv->invoice_pdf ?? $inv->hosted_invoice_url,
                        'subtotal' => number_format($inv->subtotal / 100, 2),
                        'tax' => number_format(($inv->tax ?? 0) / 100, 2),
                        'description' => $inv->lines->data[0]->description ?? 'Subscription payment',
                    ];
                }

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Stripe error in showInvoicesBilling: ' . $e->getMessage());
            }
        }

        return view('billing_invoices', [
            'invoices' => $invoices,
        ]);
    }

    /**
     * Update or attach a default payment method to the Stripe customer profile.
     */
    public function updatePaymentMethod(Request $request)
    {
        $secretKey = config('services.stripe.secret');
        if (empty($secretKey)) {
            return back()->with('error', 'Stripe is not configured.');
        }

        $paymentMethodId = $request->input('payment_method_id');
        if (empty($paymentMethodId)) {
            return back()->with('error', 'Invalid payment method ID.');
        }

        try {
            \Stripe\Stripe::setApiKey($secretKey);
            $user = auth()->user();

            $customers = \Stripe\Customer::all(['email' => $user->email, 'limit' => 1]);
            if (count($customers->data) === 0) {
                return back()->with('error', 'Stripe customer not found.');
            }
            $customer = $customers->data[0];

            $pm = \Stripe\PaymentMethod::retrieve($paymentMethodId);
            if ($pm->customer !== $customer->id) {
                $pm->attach(['customer' => $customer->id]);
            }

            \Stripe\Customer::update($customer->id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId,
                ],
            ]);

            $latestOrder = $user->orders()->where('status', 'paid')->latest()->first();
            if ($latestOrder && !empty($latestOrder->stripe_session_id)) {
                if (str_starts_with($latestOrder->stripe_session_id, 'sub_')) {
                    $subscriptionId = $latestOrder->stripe_session_id;
                } else {
                    $session = \Stripe\Checkout\Session::retrieve($latestOrder->stripe_session_id);
                    $subscriptionId = $session->subscription ?? null;
                }

                if ($subscriptionId) {
                    \Stripe\Subscription::update($subscriptionId, [
                        'default_payment_method' => $paymentMethodId,
                    ]);
                }
            }

            return redirect()->route('billing.card')->with('success', 'Your payment method has been updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Stripe error: ' . $e->getMessage());
        }
    }

    /**
     * Remove / detach a payment method from the customer profile.
     */
    public function removePaymentMethod(Request $request)
    {
        $secretKey = config('services.stripe.secret');
        if (empty($secretKey)) {
            return back()->with('error', 'Stripe is not configured.');
        }

        $paymentMethodId = $request->input('payment_method_id');
        if (empty($paymentMethodId)) {
            return back()->with('error', 'Invalid payment method.');
        }

        try {
            \Stripe\Stripe::setApiKey($secretKey);
            $pm = \Stripe\PaymentMethod::retrieve($paymentMethodId);
            $pm->detach();

            return redirect()->route('billing.card')->with('success', 'Your card has been removed successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Stripe error: ' . $e->getMessage());
        }
    }
}
