<!DOCTYPE html>
<html lang="en" class="scroll-smooth bg-[#030307]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Cart</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
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
        <a href="{{ route('pricing') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
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
        <!-- Error Alerts -->
        @if (session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4.5 h-4.5"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <h1 class="text-2xl font-black text-white uppercase tracking-wider flex items-center gap-2 mb-6">
            <i data-lucide="shopping-cart" class="text-[#5865F2]"></i> Your Cart
        </h1>

        <form action="{{ route('checkout.process', ['custom_id' => $order->custom_id]) }}" method="POST" id="checkout-cart-form">
            @csrf

            <!-- Hidden calculation fields populated by Javascript before submission -->
            <input type="hidden" name="subtotal" id="hidden-subtotal">
            <input type="hidden" name="discount" id="hidden-discount">
            <input type="hidden" name="tax" id="hidden-tax">
            <input type="hidden" name="total" id="hidden-total">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- Left & Middle Pane: Plan Details & Address -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Choose Plan Duration -->
                    @php
                        $minMonths = 0;
                        if (isset($activeOrder) && $activeOrder && $activeOrder->plan->slug === 'premium' && $plan->slug === 'pro') {
                            $minMonths = $activeOrder->months;
                        }
                    @endphp
                    <div class="cyber-card rounded-2xl p-6 md:p-8 space-y-6">
                        <h2 class="text-sm font-bold text-white uppercase tracking-wider">1. Choose Billing Cycle</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @if($minMonths <= 1)
                            <!-- 1 Month -->
                            <label class="relative flex flex-col justify-between p-5 rounded-xl border border-white/10 bg-white/5 cursor-pointer hover:border-white/20 select-none">
                                <input type="radio" name="months" value="1" class="sr-only" {{ $order->months == 1 ? 'checked' : '' }} onclick="calculatePrice()">
                                <div class="flex justify-between items-start">
                                    <span class="text-xs font-bold text-white">Monthly</span>
                                    <span class="w-4 h-4 rounded-full border border-white/20 flex items-center justify-center check-dot"></span>
                                </div>
                                <div class="mt-4">
                                    <span class="text-xl font-black text-white">${{ $plan->price_1 }}</span>
                                    <span class="text-[10px] text-slate-400 block">per month</span>
                                </div>
                            </label>
                            @endif

                            @if($minMonths <= 12)
                            <!-- 12 Months -->
                            <label class="relative flex flex-col justify-between p-5 rounded-xl border border-white/10 bg-white/5 cursor-pointer hover:border-white/20 select-none">
                                <input type="radio" name="months" value="12" class="sr-only" {{ $order->months == 12 ? 'checked' : '' }} onclick="calculatePrice()">
                                <div class="absolute -top-2.5 right-4 bg-[#5865F2] text-white text-[8px] font-bold uppercase px-2 py-0.5 rounded-full tracking-wider">Save 20%</div>
                                <div class="flex justify-between items-start">
                                    <span class="text-xs font-bold text-white">12 Months</span>
                                    <span class="w-4 h-4 rounded-full border border-white/20 flex items-center justify-center check-dot"></span>
                                </div>
                                <div class="mt-4">
                                    <span class="text-xl font-black text-white">${{ $plan->price_12 }}</span>
                                    <span class="text-[10px] text-slate-400 block">per month</span>
                                    <span class="text-[9px] text-slate-500 line-through">${{ $plan->price_1 }}</span>
                                </div>
                            </label>
                            @endif

                            @if($minMonths <= 24)
                            <!-- 24 Months -->
                            <label class="relative flex flex-col justify-between p-5 rounded-xl border border-white/10 bg-white/5 cursor-pointer hover:border-white/20 select-none">
                                <input type="radio" name="months" value="24" class="sr-only" {{ $order->months == 24 ? 'checked' : '' }} onclick="calculatePrice()">
                                <div class="absolute -top-2.5 right-4 bg-purple-600 text-white text-[8px] font-bold uppercase px-2 py-0.5 rounded-full tracking-wider font-mono">Save 30%</div>
                                <div class="flex justify-between items-start">
                                    <span class="text-xs font-bold text-white">24 Months</span>
                                    <span class="w-4 h-4 rounded-full border border-white/20 flex items-center justify-center check-dot"></span>
                                </div>
                                <div class="mt-4">
                                    <span class="text-xl font-black text-white">${{ $plan->price_24 }}</span>
                                    <span class="text-[10px] text-slate-400 block">per month</span>
                                    <span class="text-[9px] text-slate-500 line-through">${{ $plan->price_1 }}</span>
                                </div>
                            </label>
                            @endif
                        </div>
                    </div>

                    <!-- Billing Address Form -->
                    <div class="cyber-card rounded-2xl p-6 md:p-8 space-y-6">
                        <h2 class="text-sm font-bold text-white uppercase tracking-wider">2. Tax Location</h2>
                        <div class="relative max-w-md">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Country</label>
                            <div class="relative">
                                <input type="text" name="country" id="country-input" autocomplete="off" value="{{ $order->country ? ($order->country === 'FR' ? 'France' : ($order->country === 'US' ? 'United States' : ($order->country === 'UK' ? 'United Kingdom' : ($order->country === 'CA' ? 'Canada' : ($order->country === 'AU' ? 'Australia' : $order->country))))) : 'France' }}" placeholder="e.g. France" class="w-full bg-[#030307] border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-[#5865F2]" onfocus="showCountrySuggestions()" oninput="filterCountrySuggestions()" onchange="calculatePrice()" onblur="setTimeout(calculatePrice, 200)" required>
                                 <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 absolute right-3.5 top-3 cursor-pointer z-10" onclick="toggleCountryDropdown(event)"></i>
                            </div>
                            <div id="country-dropdown" class="hidden absolute left-0 right-0 mt-1 max-h-48 overflow-y-auto rounded-xl bg-[#090a14] border border-white/10 shadow-2xl z-50 py-1.5 scrollbar-thin scrollbar-thumb-white/10">
                                <div class="px-4 py-2 text-xs text-slate-500">Loading countries...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Order Summary & Coupon -->
                <div class="space-y-6">
                    <!-- Coupon System -->
                    <div class="cyber-card rounded-2xl p-6 space-y-4">
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">Have a promo code?</h3>
                        <div id="coupon-input-group" class="flex gap-2">
                            <input type="text" name="coupon" id="coupon-input" placeholder="Promo code" class="flex-grow bg-[#030307] border border-white/10 rounded-xl px-4 py-2 text-xs text-white uppercase focus:outline-none focus:border-[#5865F2]">
                            <button type="button" onclick="applyPromoCode()" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 text-xs font-bold text-white transition cursor-pointer">Apply</button>
                        </div>
                        <p id="coupon-message" class="text-[10px] hidden"></p>

                        <!-- Applied Coupon Badge -->
                        <div id="applied-coupon-container" class="hidden items-center justify-between bg-white/5 border border-emerald-500/20 rounded-xl p-3 mt-2">
                            <div class="flex items-center gap-2">
                                <i data-lucide="tag" class="w-4 h-4 text-[#5865F2] shrink-0"></i>
                                <span id="applied-coupon-code" class="text-xs font-bold text-white uppercase"></span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span id="applied-coupon-discount" class="text-xs font-black text-emerald-400"></span>
                                <button type="button" onclick="removePromoCode()" class="text-slate-400 hover:text-rose-400 transition cursor-pointer flex items-center justify-center">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Display -->
                    <div class="cyber-card rounded-2xl p-6 space-y-6">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Order Summary</h3>
                        <div class="space-y-3.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Subscription Plan</span>
                                <span class="text-white capitalize">{{ $plan->name }} Plan</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Base Cost</span>
                                <span id="summary-base-price" class="text-white">$0.00</span>
                            </div>
                            <div class="flex justify-between text-emerald-400 hidden" id="summary-duration-discount-row">
                                <span>Duration Discount</span>
                                <span id="summary-duration-discount">-$0.00</span>
                            </div>
                            <div class="flex justify-between text-emerald-400 hidden" id="summary-coupon-discount-row">
                                <span id="summary-coupon-label">Coupon Discount</span>
                                <span id="summary-coupon-discount">-$0.00</span>
                            </div>
                            @if($order->prorated_discount > 0)
                            <div class="flex justify-between text-emerald-400" id="summary-prorated-discount-row">
                                <span>Prorated Discount</span>
                                <span id="summary-prorated-discount">-${{ number_format($order->prorated_discount, 2) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between pb-3 border-b border-white/5">
                                <span class="text-slate-400">Estimated Taxes</span>
                                <span id="summary-tax" class="text-white">$0.00</span>
                            </div>
                            <div class="flex justify-between text-sm font-black pt-1">
                                <span class="text-white">Total</span>
                                <span id="summary-total" class="text-white">$0.00</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full neon-glow-btn text-white text-xs font-bold py-3 rounded-xl flex items-center justify-center gap-1.5 cursor-pointer">
                            Continue to Payment <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <div class="flex items-center justify-center gap-2 text-[10px] text-slate-500">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        <span>30-Day Money-Back Guarantee</span>
                    </div>
                </div>

            </div>
        </form>
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

        // Pricing Configuration
        const prices = {
            1: {{ $plan->price_1 }},
            12: {{ $plan->price_12 }},
            24: {{ $plan->price_24 }}
        };
        const proratedDiscount = {{ (float)$order->prorated_discount }};
        let activeDiscountPercent = 0; // Promo coupon discount

        let taxRates = {}; // Populated dynamically from API

        function calculatePrice() {
            // Get selected months (1, 12, or 24)
            const monthsSelect = document.querySelector('input[name="months"]:checked');
            const months = parseInt(monthsSelect ? monthsSelect.value : 12);
            
            // Highlight duration radio UI borders
            document.querySelectorAll('input[name="months"]').forEach(input => {
                const label = input.closest('label');
                if (input.checked) {
                    label.className = "relative flex flex-col justify-between p-5 rounded-xl border-2 border-[#5865F2] bg-[#5865F2]/5 cursor-pointer select-none";
                    // Update check dot inside
                    const dot = label.querySelector('.check-dot');
                    if (dot) dot.innerHTML = '<span class="w-2 h-2 rounded-full bg-[#5865F2]"></span>';
                } else {
                    label.className = "relative flex flex-col justify-between p-5 rounded-xl border border-white/10 bg-white/5 cursor-pointer hover:border-white/20 select-none";
                    const dot = label.querySelector('.check-dot');
                    if (dot) dot.innerHTML = '';
                }
            });

            // Calculate Base Cost
            const baseCost = prices[1] * months;
            
            // Calculate the actual discounted price for this duration
            const priceAfterDurationDiscount = prices[months] * months;
            
            // The savings/discount amount is the difference
            const durationDiscount = baseCost - priceAfterDurationDiscount;

            // Calculate Coupon Discount
            const couponDiscount = priceAfterDurationDiscount * activeDiscountPercent;
            const priceAfterCouponAndProration = Math.max(0, priceAfterDurationDiscount - couponDiscount - proratedDiscount);

            // Calculate Tax
            const countryVal = document.getElementById('country-input').value.trim().toUpperCase();
            const taxRate = taxRates.hasOwnProperty(countryVal) ? taxRates[countryVal] : 0.10;
            const tax = priceAfterCouponAndProration * taxRate;

            // Calculate Final Total
            const total = priceAfterCouponAndProration + tax;

            // Populate UI Summary
            document.getElementById('summary-base-price').innerText = '$' + baseCost.toFixed(2);
            
            // Show duration discount row if applicable
            if (durationDiscount > 0) {
                document.getElementById('summary-duration-discount-row').classList.remove('hidden');
                document.getElementById('summary-duration-discount').innerText = '-$' + durationDiscount.toFixed(2);
            } else {
                document.getElementById('summary-duration-discount-row').classList.add('hidden');
            }

            // Show coupon discount row if applicable
            const summaryCouponRow = document.getElementById('summary-coupon-discount-row');
            const summaryCouponDiscount = document.getElementById('summary-coupon-discount');
            const summaryCouponLabel = document.getElementById('summary-coupon-label');

            if (couponDiscount !== 0) {
                summaryCouponRow.classList.remove('hidden');
                if (couponDiscount < 0) {
                    // Surcharge (BRAINROT)
                    summaryCouponRow.classList.remove('text-emerald-400');
                    summaryCouponRow.classList.add('text-rose-400');
                    summaryCouponLabel.innerText = 'Price Surcharge';
                    summaryCouponDiscount.innerText = '+$' + Math.abs(couponDiscount).toFixed(2);
                } else {
                    // Discount
                    summaryCouponRow.classList.remove('text-rose-400');
                    summaryCouponRow.classList.add('text-emerald-400');
                    summaryCouponLabel.innerText = 'Coupon Discount';
                    summaryCouponDiscount.innerText = '-$' + couponDiscount.toFixed(2);
                }
            } else {
                summaryCouponRow.classList.add('hidden');
            }

            document.getElementById('summary-tax').innerText = '$' + tax.toFixed(2);
            document.getElementById('summary-total').innerText = '$' + total.toFixed(2);

            // Populate Hidden Inputs for Submission
            document.getElementById('hidden-subtotal').value = priceAfterDurationDiscount.toFixed(2);
            document.getElementById('hidden-discount').value = couponDiscount.toFixed(2);
            document.getElementById('hidden-tax').value = tax.toFixed(2);
            document.getElementById('hidden-total').value = total.toFixed(2);
        }

        function applyPromoCode() {
            const codeInput = document.getElementById('coupon-input');
            const code = codeInput.value.trim();
            const msg = document.getElementById('coupon-message');
            const appliedContainer = document.getElementById('applied-coupon-container');
            const appliedCode = document.getElementById('applied-coupon-code');
            const appliedDiscount = document.getElementById('applied-coupon-discount');

            msg.classList.add('hidden');
            msg.classList.remove('text-emerald-400', 'text-rose-400');

            if (code === '') {
                activeDiscountPercent = 0;
                calculatePrice();
                return;
            }

            fetch('/api/coupons/' + encodeURIComponent(code))
                .then(response => response.json())
                .then(data => {
                    if (data.valid) {
                        const isBrainrot = code.toUpperCase() === 'BRAINROT';
                        if (isBrainrot) {
                            activeDiscountPercent = -0.67;
                        } else {
                            activeDiscountPercent = data.discount_percent / 100;
                        }
                        
                        // Show applied container badge below input group
                        appliedContainer.classList.remove('hidden');
                        appliedContainer.classList.add('flex');
                        
                        appliedCode.innerText = code;
                        if (isBrainrot) {
                            appliedDiscount.innerText = `+${data.discount_percent}%`;
                        } else {
                            appliedDiscount.innerText = `-${data.discount_percent}%`;
                        }
                        appliedDiscount.className = "text-xs font-black text-emerald-400";
                        
                        lucide.createIcons();
                    } else {
                        activeDiscountPercent = 0;
                        msg.innerText = 'Invalid promo code.';
                        msg.classList.remove('hidden');
                        msg.classList.add('text-rose-400');
                        appliedContainer.classList.add('hidden');
                        appliedContainer.classList.remove('flex');
                    }
                    calculatePrice();
                })
                .catch(err => {
                    console.error(err);
                    activeDiscountPercent = 0;
                    msg.innerText = 'Error validating coupon.';
                    msg.classList.remove('hidden');
                    msg.classList.add('text-rose-400');
                    appliedContainer.classList.add('hidden');
                    appliedContainer.classList.remove('flex');
                    calculatePrice();
                });
        }

        function removePromoCode() {
            const codeInput = document.getElementById('coupon-input');
            codeInput.value = '';
            
            const appliedContainer = document.getElementById('applied-coupon-container');
            const msg = document.getElementById('coupon-message');
            
            appliedContainer.classList.add('hidden');
            appliedContainer.classList.remove('flex');
            msg.classList.add('hidden');
            
            activeDiscountPercent = 0;
            calculatePrice();
        }

        let countriesData = [];

        // Fetch countries dynamically from our database API
        fetch('/api/countries')
            .then(res => res.json())
            .then(data => {
                countriesData = data;
                taxRates = {};
                data.forEach(c => {
                    const rate = parseFloat(c.tax_rate);
                    taxRates[c.name.toUpperCase()] = rate;
                    taxRates[c.code.toUpperCase()] = rate;
                });
                renderCountryDropdown(countriesData);
                calculatePrice();
            })
            .catch(err => {
                console.error('Failed to load countries database API:', err);
            });

        function renderCountryDropdown(list) {
            const dropdown = document.getElementById('country-dropdown');
            if (!dropdown) return;
            dropdown.innerHTML = '';
            
            if (list.length === 0) {
                dropdown.innerHTML = '<div class="px-4 py-2 text-xs text-slate-500">No countries found</div>';
                return;
            }

            list.forEach(country => {
                const div = document.createElement('div');
                div.className = "country-item px-4 py-2 text-xs text-slate-300 hover:bg-[#5865F2] hover:text-white cursor-pointer transition";
                div.innerText = country.name;
                div.onclick = () => selectCountry(country.name);
                dropdown.appendChild(div);
            });
        }

        function showCountrySuggestions() {
            const dropdown = document.getElementById('country-dropdown');
            if (dropdown) {
                dropdown.classList.remove('hidden');
            }
            filterCountrySuggestions();
        }

        function toggleCountryDropdown(event) {
            event.stopPropagation();
            const input = document.getElementById('country-input');
            const dropdown = document.getElementById('country-dropdown');
            if (!dropdown || !input) return;
            if (dropdown.classList.contains('hidden')) {
                input.focus();
                showCountrySuggestions();
            } else {
                dropdown.classList.add('hidden');
            }
        }

        function filterCountrySuggestions() {
            const input = document.getElementById('country-input');
            if (!input) return;
            const filter = input.value.trim().toUpperCase();
            
            const filtered = countriesData.filter(country => 
                country.name.toUpperCase().includes(filter) || country.code.toUpperCase().includes(filter)
            );
            
            renderCountryDropdown(filtered);
            calculatePrice();
        }

        function selectCountry(countryName) {
            const input = document.getElementById('country-input');
            if (input) {
                input.value = countryName;
            }
            
            const dropdown = document.getElementById('country-dropdown');
            if (dropdown) {
                dropdown.classList.add('hidden');
            }
            
            calculatePrice();
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const input = document.getElementById('country-input');
            const dropdown = document.getElementById('country-dropdown');
            
            if (input && dropdown) {
                const isClickInside = input.contains(event.target) || dropdown.contains(event.target);
                if (!isClickInside) {
                    dropdown.classList.add('hidden');
                }
            }
        });

        // Initialize calculations
        calculatePrice();
    </script>
</body>
</html>
