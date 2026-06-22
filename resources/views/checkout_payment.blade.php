<!DOCTYPE html>
<html lang="en" class="scroll-smooth bg-[#030307]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Payment</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @if(isset($stripeKey))
        <script src="https://js.stripe.com/v3/"></script>
    @endif
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

        .step-inactive {
            display: none !important;
        }
    </style>
</head>
<body class="bg-[#030307] text-slate-100 font-sans min-h-screen flex flex-col justify-between relative overflow-x-hidden selection:bg-purple-500 selection:text-white">

    <!-- Glowing ambient blobs -->
    <div class="fixed top-[-20%] left-[-10%] w-[60vw] h-[60vw] rounded-full bg-purple-950/10 blur-[130px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-20%] right-[-10%] w-[60vw] h-[60vw] rounded-full bg-blue-950/10 blur-[130px] pointer-events-none z-0"></div>

    <!-- Cyber grid overlay -->
    <div class="fixed inset-0 cyber-grid pointer-events-none z-0"></div>

    <!-- Header bar -->
    <div class="w-full bg-[#080914]/80 border-b border-white/5 py-4 px-6 z-10 relative flex justify-between items-center">
        <a href="{{ route('checkout', ['custom_id' => $order->custom_id]) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Cart
        </a>
        <div class="flex items-center gap-3 text-xs text-slate-400">
            <span>{{ Auth::user()->email }}</span>
            <form action="{{ route('logout') }}" method="POST" class="inline m-0">
                @csrf
                <button type="submit" class="font-bold text-[#5865F2] hover:underline cursor-pointer">Logout</button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow z-10 relative py-12 px-4 max-w-6xl mx-auto w-full">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <!-- Left & Middle: Payment Method Form -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Payment Form Card -->
                <div class="cyber-card rounded-2xl p-6 md:p-8 space-y-6">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Payment Details</h3>

                    <!-- Card Form fields -->
                    <div id="payment-form-card" class="space-y-4">
                        @if(isset($clientSecret) && isset($stripeKey))
                            <form id="stripe-payment-form" class="space-y-4">
                                @if(isset($activeCard))
                                    <!-- Saved Card Option Selector -->
                                    <div class="space-y-3 p-4 bg-slate-950/40 border border-white/10 rounded-xl">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Select Payment Method</p>
                                        
                                        <label id="label-use-saved" class="flex items-center gap-3 p-3 rounded-lg border border-[#5865F2]/30 bg-[#5865F2]/5 cursor-pointer hover:bg-[#5865F2]/10 transition-colors">
                                            <input type="radio" name="payment_choice" value="saved" checked onclick="togglePaymentChoice('saved')" class="accent-[#5865F2]">
                                            <div class="flex-grow flex items-center justify-between">
                                                <div>
                                                    <p class="text-xs font-bold text-white flex items-center gap-1.5">
                                                        <i class="fa-brands fa-cc-{{ strtolower($activeCard['brand']) }} text-slate-300 text-sm"></i>
                                                        {{ strtoupper($activeCard['brand']) }} ending in •••• {{ $activeCard['last4'] }}
                                                    </p>
                                                    <p class="text-[10px] text-slate-400">Expires {{ sprintf('%02d', $activeCard['exp_month']) }}/{{ $activeCard['exp_year'] }}</p>
                                                </div>
                                                <span class="text-[9px] uppercase font-black px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">Default</span>
                                            </div>
                                        </label>

                                        <label id="label-use-new" class="flex items-center gap-3 p-3 rounded-lg border border-white/5 cursor-pointer hover:bg-white/5 transition-colors">
                                            <input type="radio" name="payment_choice" value="new" onclick="togglePaymentChoice('new')" class="accent-[#5865F2]">
                                            <span class="text-xs font-bold text-slate-300">Use a different credit card</span>
                                        </label>
                                    </div>
                                @endif

                                <div id="stripe-payment-element" class="p-4 bg-slate-950/40 border border-white/10 rounded-xl @if(isset($activeCard)) hidden @endif"></div>
                                <div id="stripe-error-message" class="text-xs text-rose-500 hidden p-3 bg-rose-950/20 border border-rose-500/25 rounded-lg"></div>
                                <button id="stripe-submit-button" type="submit" class="w-full neon-glow-btn text-white text-xs font-bold py-3 rounded-xl flex items-center justify-center gap-1.5 mt-2 cursor-pointer">
                                    Confirm Payment <i data-lucide="shield-check" class="w-4 h-4"></i>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('checkout.success', ['custom_id' => $order->custom_id]) }}" method="GET" class="space-y-4">
                                <!-- Stripe test warning -->
                                <div class="p-3 bg-purple-950/20 border border-purple-500/25 rounded-lg text-[10px] text-purple-300 flex items-start gap-2">
                                    <i data-lucide="info" class="w-4 h-4 text-purple-400 shrink-0 mt-0.5"></i>
                                    <div>
                                        <p class="font-bold text-white">Stripe Test Mode</p>
                                        <p>Your keys are not set, so you can input any card details to simulate checkout.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Cardholder Name</label>
                                        <input type="text" placeholder="Cardholder name" value="{{ $order->full_name }}" class="w-full bg-[#030307] border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-[#5865F2]" required>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Card Number</label>
                                        <input type="text" placeholder="4242 4242 4242 4242" class="w-full bg-[#030307] border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-[#5865F2]" required>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Expiration Date</label>
                                        <input type="text" placeholder="MM / YY" class="w-full bg-[#030307] border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-[#5865F2]" required>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">CVC Code</label>
                                        <input type="text" placeholder="•••" class="w-full bg-[#030307] border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-[#5865F2]" required>
                                    </div>
                                </div>

                                <button type="submit" class="w-full neon-glow-btn text-white text-xs font-bold py-3 rounded-xl flex items-center justify-center gap-1.5 mt-2 cursor-pointer">
                                    Submit Payment <i data-lucide="shield-check" class="w-4 h-4"></i>
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Card footer disclaimers -->
                    <div class="text-[10px] text-slate-400 space-y-2 pt-4 border-t border-white/5">
                        <p class="flex items-center gap-1.5"><i data-lucide="lock" class="w-3.5 h-3.5 text-emerald-400"></i> Encrypted Secure Payments</p>
                        <p class="text-slate-500">By validating your payment, you agree to our Terms of Use and confirm you have read our Privacy Policy. You can cancel your recurring subscription at any time.</p>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar: Billing Summary Review (read-only) -->
            <div class="space-y-6">
                <div class="cyber-card rounded-2xl p-6 space-y-6">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Order Summary</h3>
                    <div class="space-y-3.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Pack {{ $plan->name }} ({{ $order->months }} Months)</span>
                            <span class="text-white">${{ number_format(($order->subtotal + $order->discount), 2) }}</span>
                        </div>
                        
                        @if ($order->discount > 0)
                            <div class="flex justify-between text-emerald-400">
                                <span>Discounts Applied</span>
                                <span>-${{ number_format($order->discount, 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between">
                             <span class="text-slate-400">Taxes ({{ $order->country }})</span>
                             <span class="text-white">${{ number_format($order->tax, 2) }}</span>
                         </div>
                        <div class="flex justify-between text-sm font-black pt-1 border-t border-white/5">
                            <span class="text-white">Total</span>
                            <span class="text-white">${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-2 text-[10px] text-slate-500">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    <span>30-Day Money-Back Guarantee</span>
                </div>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="w-full bg-[#020204] border-t border-white/5 py-8 px-6 z-10 relative text-center text-[10px] text-slate-500 space-y-3">
        <p>&copy; 2026 HublyBot. All rights reserved.</p>
        <div class="flex justify-center gap-4">
            <a href="#" class="hover:underline">Terms of Service</a>
            <a href="#" class="hover:underline">Privacy Policy</a>
        </div>
    </footer>

    <script>
        lucide.createIcons();
 
        @if(isset($clientSecret) && isset($stripeKey))
            const stripe = Stripe('{{ $stripeKey }}');
            const options = {
                clientSecret: '{{ $clientSecret }}',
                appearance: {
                    theme: 'night',
                    variables: {
                        colorPrimary: '#5865F2',
                        colorBackground: '#0a0b16',
                        colorText: '#f1f5f9',
                        colorDanger: '#ef4444',
                        fontFamily: 'ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
                        spacingUnit: '4px',
                        borderRadius: '12px',
                    }
                }
            };
            const elements = stripe.elements(options);
            const paymentElement = elements.create('payment');
            paymentElement.mount('#stripe-payment-element');
 
            // Toggle payment choices
            function togglePaymentChoice(choice) {
                const labelSaved = document.getElementById('label-use-saved');
                const labelNew = document.getElementById('label-use-new');
                const elementContainer = document.getElementById('stripe-payment-element');
 
                if (choice === 'saved') {
                    if (labelSaved) {
                        labelSaved.classList.add('border-[#5865F2]/30', 'bg-[#5865F2]/5');
                        labelSaved.classList.remove('border-white/5');
                    }
                    if (labelNew) {
                        labelNew.classList.remove('border-[#5865F2]/30', 'bg-[#5865F2]/5');
                        labelNew.classList.add('border-white/5');
                    }
                    if (elementContainer) {
                        elementContainer.classList.add('hidden');
                    }
                } else {
                    if (labelSaved) {
                        labelSaved.classList.remove('border-[#5865F2]/30', 'bg-[#5865F2]/5');
                        labelSaved.classList.add('border-white/5');
                    }
                    if (labelNew) {
                        labelNew.classList.add('border-[#5865F2]/30', 'bg-[#5865F2]/5');
                        labelNew.classList.remove('border-white/5');
                    }
                    if (elementContainer) {
                        elementContainer.classList.remove('hidden');
                    }
                }
            }
 
            const stripeForm = document.getElementById('stripe-payment-form');
            const stripeSubmitBtn = document.getElementById('stripe-submit-button');
            const stripeErrorMsg = document.getElementById('stripe-error-message');
 
            stripeForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                stripeSubmitBtn.disabled = true;
                stripeSubmitBtn.innerHTML = 'Processing... <i class="fa-solid fa-spinner fa-spin"></i>';
                stripeErrorMsg.classList.add('hidden');
 
                // Check selection
                const choiceInput = document.querySelector('input[name="payment_choice"]:checked');
                const isSaved = choiceInput && choiceInput.value === 'saved';
 
                if (isSaved) {
                    const { paymentIntent, error } = await stripe.confirmCardPayment('{{ $clientSecret }}', {
                        payment_method: '{{ $activeCard['id'] ?? '' }}',
                    });
 
                    if (error) {
                        stripeErrorMsg.textContent = error.message;
                        stripeErrorMsg.classList.remove('hidden');
                        stripeSubmitBtn.disabled = false;
                        stripeSubmitBtn.innerHTML = 'Confirm Payment <i data-lucide="shield-check" class="w-4 h-4"></i>';
                        lucide.createIcons();
                    } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                        window.location.href = "{{ route('checkout.success', ['custom_id' => $order->custom_id]) }}";
                    }
                } else {
                    const { error } = await stripe.confirmPayment({
                        elements,
                        confirmParams: {
                            return_url: "{{ route('checkout.success', ['custom_id' => $order->custom_id]) }}",
                        },
                    });
 
                    if (error) {
                        stripeErrorMsg.textContent = error.message;
                        stripeErrorMsg.classList.remove('hidden');
                        stripeSubmitBtn.disabled = false;
                        stripeSubmitBtn.innerHTML = 'Confirm Payment <i data-lucide="shield-check" class="w-4 h-4"></i>';
                        lucide.createIcons();
                    }
                }
            });
        @endif
    </script>
</body>
</html>
