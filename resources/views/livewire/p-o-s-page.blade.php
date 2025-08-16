<div class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 min-h-screen">
    <!-- Professional POS Header -->
    <div class="bg-gradient-to-r from-blue-900 via-purple-800 to-indigo-900 shadow-xl border-b-4 border-blue-500">
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-2">
                        <span class="text-2xl">💰</span>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">POS System</h1>
                        <p class="text-blue-100 text-sm font-medium">Garage Management</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-3">
                        <div class="text-white/90 text-sm font-semibold" id="pos-date">
                            {{ now()->format('M d, Y') }}
                        </div>
                        <div class="text-white text-xl font-bold" id="pos-time">
                            {{ now()->format('H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Service Buttons - Mobile Optimized Square Grid -->
    <div class="bg-white/80 backdrop-blur-sm shadow-xl border-b-4 border-gray-200">
        <div class="px-4 py-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <span class="text-2xl mr-3">⚡</span>
                Quick Services
            </h2>
            <div class="grid grid-cols-3 gap-4">
                @forelse($this->quickServices as $quickService)
                    <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl p-4 text-white text-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 cursor-pointer"
                        onclick="showQuickServicePrice('{{ $quickService['id'] }}', '{{ $quickService['name'] }}', {{ $quickService['default_price'] }})">
                        <div class="text-2xl mb-2">
                            @if($quickService['category'] === 'service')
                                🔧
                            @elseif($quickService['category'] === 'part')
                                🧩
                            @else
                                ⚡
                            @endif
                        </div>
                        <div class="font-bold text-sm mb-1">{{ $quickService['name'] }}</div>
                        <div class="text-xs opacity-90">MVR {{ number_format($quickService['default_price'], 0) }}</div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-gray-500">
                        <div class="text-4xl mb-2">📝</div>
                        <div class="text-lg font-semibold">No Quick Services</div>
                        <div class="text-sm">Add quick services from the admin panel</div>
                    </div>
                @endforelse
            </div>

            <!-- Quick Service Price Modal - Mobile Optimized -->
            <div id="quickServiceModal"
                class="fixed inset-0 bg-black/60 backdrop-blur-md hidden z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl border-4 border-blue-200">
                    <div class="text-center mb-6">
                        <div class="text-4xl mb-3" id="modalServiceIcon">⚡</div>
                        <h3 class="text-xl font-bold text-gray-800">Set Price</h3>
                        <p class="text-sm text-gray-600" id="modalServiceName"></p>
                    </div>
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Price (MVR)</label>
                        <input type="number" id="modalPrice" step="0.01" min="0"
                            class="w-full px-6 py-4 text-xl border-2 border-gray-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div class="flex gap-4">
                        <button type="button" onclick="closeQuickServiceModal()"
                            class="flex-1 px-6 py-4 bg-gray-200 text-gray-700 rounded-2xl hover:bg-gray-300 font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="button" onclick="addQuickServiceWithPrice()"
                            class="flex-1 px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl hover:from-blue-700 hover:to-purple-700 font-medium transition-colors shadow-lg">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main POS Form Container -->
    <div class="px-4 py-6">
        <div class="max-w-4xl mx-auto">
            <form wire:submit="save" class="space-y-6">
                <!-- Customer & Motorcycle Section -->
                <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800">👤 Customer & Motorcycle</h3>
                    </div>
                    <div class="p-6">
                        <!-- Walk-in Customer Toggle -->
                        <div class="mb-4">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" wire:model="is_walkin" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Walk-in Customer</span>
                            </label>
                        </div>

                        @if($is_walkin)
                            <!-- Walk-in Customer Form -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <div class="text-sm text-blue-700 mb-3">
                                    <strong>💡 Cash Transaction Mode:</strong> Walk-in customers can be processed as cash bills. Customer details are optional.
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer Name <span class="text-gray-500">(Optional)</span></label>
                                        <input type="text" wire:model="walkin_name" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" placeholder="Enter customer name (optional)">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number <span class="text-gray-500">(Optional)</span></label>
                                        <input type="text" wire:model="walkin_phone" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" placeholder="Enter phone number (optional)">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Plate Number <span class="text-gray-500">(Optional)</span></label>
                                        <input type="text" wire:model="walkin_plate_no" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" placeholder="Enter plate number (optional)">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Model <span class="text-gray-500">(Optional)</span></label>
                                        <input type="text" wire:model="walkin_model" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" placeholder="Enter vehicle model (optional)">
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Regular Customer Selection -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                                    <div class="flex gap-2">
                                        <select wire:model.live="customer_id" class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                            <option value="">Select Customer</option>
                                            @foreach(App\Models\Customer::all() as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                                            @endforeach
                                        </select>
                                        <button type="button" wire:click="openCustomerModal" class="px-4 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors font-medium">
                                            ➕ New
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Motorcycle *</label>
                                    <select wire:model="motorcycle_id" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" {{ empty($customer_id) ? 'disabled' : '' }}>
                                        <option value="">Select Motorcycle</option>
                                        @if($customer_id)
                                            @foreach(App\Models\Motorcycle::where('customer_id', $customer_id)->get() as $motorcycle)
                                                <option value="{{ $motorcycle->id }}">{{ $motorcycle->plate_no }} - {{ $motorcycle->model }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Add Item Section -->
                <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800">➕ Add Item</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Item Name</label>
                                <div class="relative">
                                    <input type="text" wire:model.live="item_name" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" placeholder="Enter item name or search...">
                                    @if(strlen($item_name) > 2)
                                        <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                            @foreach($this->searchItems($item_name) as $value => $label)
                                                <div wire:click="$set('item_name', '{{ $value }}')" class="px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm">
                                                    {{ $label }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                <input type="number" wire:model="qty" min="1" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price (MVR)</label>
                                <input type="number" wire:model="unit_price" step="0.01" min="0" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                            </div>
                        </div>
                        @if($qty > 0 && $unit_price > 0)
                            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="text-sm text-blue-700">
                                    <strong>Line Total:</strong> {{ $qty }} × MVR {{ number_format($unit_price, 2) }} = <strong>MVR {{ number_format($line_total, 2) }}</strong>
                                </div>
                            </div>
                        @endif
                        <div class="mt-4">
                            <button type="button" wire:click="addItemToCart" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition-colors">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cart Section -->
                <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800">🛒 Cart Items</h3>
                    </div>
                    <div class="p-6">
                        <!-- Debug info -->
                        <div class="text-xs text-gray-500 mb-2">
                            Cart items count: {{ count($cart_items) }} | 
                            Subtotal: {{ $subtotal }} | 
                            <button wire:click="addItemToCart" class="text-blue-500 underline">Test Add Item</button>
                        </div>
                        @if(count($cart_items) > 0)
                            <div class="space-y-3">
                                @foreach($cart_items as $index => $item)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-800">{{ $item['name_snapshot'] }}</div>
                                            <div class="text-sm text-gray-600">Qty: {{ $item['qty'] }} × MVR {{ number_format($item['unit_price'], 2) }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-gray-800">MVR {{ number_format($item['line_total'], 2) }}</div>
                                            <button type="button" wire:click="removeFromCart({{ $index }})" class="text-red-500 hover:text-red-700 text-sm">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <div class="text-4xl mb-2">🛒</div>
                                <div class="text-lg font-semibold">Cart is Empty</div>
                                <div class="text-sm">Add items to get started</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Totals Section -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl shadow-lg border-3 border-green-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-100 to-emerald-100 px-6 py-4 border-b border-green-200">
                        <h3 class="text-lg font-bold text-green-800">💰 Totals</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span class="font-semibold">MVR {{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center space-x-2">
                                        <label class="flex items-center space-x-2 cursor-pointer">
                                            <input type="checkbox" wire:model="gst_enabled" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="text-gray-600">GST ({{ $gst_rate }}%)</span>
                                        </label>
                                    </div>
                                    <span class="font-semibold">MVR {{ number_format($gst_amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold text-green-800 border-t pt-3">
                                    <span>Grand Total:</span>
                                    <span>MVR {{ number_format($grand_total, 2) }}</span>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                                    <select wire:model="payment_method" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                        <option value="cash">💵 Cash</option>
                                        <option value="bml_transfer">🏦 BML Transfer</option>
                                    </select>
                                </div>
                                @if($payment_method === 'bml_transfer')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">📱 Transfer Screenshot</label>
                                        <input type="file" wire:model="proof" accept="image/*" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                        <p class="text-xs text-gray-500 mt-1">Required for BML transfers only</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 mt-8">
                    <button type="button" wire:click="clearCart"
                        class="px-8 py-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-2xl hover:from-red-600 hover:to-red-700 font-bold transition-all duration-300 min-h-[56px] text-lg shadow-lg hover:shadow-xl transform hover:scale-105">
                        🗑️ Clear Cart
                    </button>
                    <button type="submit"
                        class="px-8 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-2xl hover:from-green-600 hover:to-emerald-700 font-bold transition-all duration-300 min-h-[56px] text-lg shadow-lg hover:shadow-xl transform hover:scale-105">
                        💳 Complete Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif

    <!-- Customer Creation Modal -->
    @if($show_customer_modal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-md z-50 flex items-center justify-center p-4" wire:click="closeCustomerModal">
            <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl border-4 border-green-200" wire:click.stop>
                <div class="text-center mb-6">
                    <div class="text-4xl mb-3">👤</div>
                    <h3 class="text-xl font-bold text-gray-800">Create New Customer</h3>
                    <p class="text-sm text-gray-600">Add customer details for this transaction</p>
                </div>
                
                <form wire:submit="createCustomer" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer Name *</label>
                        <input type="text" wire:model="new_customer_name" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all"
                            placeholder="Enter customer name">
                        @error('new_customer_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="text" wire:model="new_customer_phone"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all"
                            placeholder="Enter phone number (optional)">
                        @error('new_customer_phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Plate Number</label>
                        <input type="text" wire:model="new_customer_plate_no"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all"
                            placeholder="Enter plate number (optional)">
                        @error('new_customer_plate_no')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Model</label>
                        <input type="text" wire:model="new_customer_model"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all"
                            placeholder="Enter vehicle model (optional)">
                        @error('new_customer_model')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" wire:click="closeCustomerModal"
                            class="flex-1 px-6 py-4 bg-gray-200 text-gray-700 rounded-2xl hover:bg-gray-300 font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-2xl hover:from-green-700 hover:to-emerald-700 font-medium transition-colors shadow-lg">
                            Create Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
    let currentQuickServiceId = null;

    function showQuickServicePrice(quickServiceId, serviceName, defaultPrice) {
        console.log('Showing quick service modal:', quickServiceId, serviceName, defaultPrice);
        currentQuickServiceId = quickServiceId;
        document.getElementById('modalServiceName').textContent = serviceName;
        document.getElementById('modalPrice').value = defaultPrice;
        document.getElementById('quickServiceModal').classList.remove('hidden');
        
        // Focus on price input
        setTimeout(() => {
            document.getElementById('modalPrice').focus();
        }, 100);
    }

    function closeQuickServiceModal() {
        document.getElementById('quickServiceModal').classList.add('hidden');
        currentQuickServiceId = null;
    }

    function addQuickServiceWithPrice() {
        const price = document.getElementById('modalPrice').value;
        console.log('Adding quick service:', currentQuickServiceId, 'with price:', price);
        if (currentQuickServiceId && price) {
            // Call Livewire method using Livewire.find
            const component = document.querySelector('[wire\\:id]');
            if (component) {
                const wireId = component.getAttribute('wire:id');
                console.log('Wire ID:', wireId);
                Livewire.find(wireId).call('addQuickService', currentQuickServiceId, parseFloat(price));
            } else {
                console.error('Livewire component not found');
            }
            closeQuickServiceModal();
        }
    }

    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('quickServiceModal');
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === this) {
                    closeQuickServiceModal();
                }
            });
        }

        // Enter key support
        const priceInput = document.getElementById('modalPrice');
        if (priceInput) {
            priceInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    addQuickServiceWithPrice();
                }
            });
        }

        // Customer modal click outside to close
        document.addEventListener('click', function (e) {
            const customerModal = document.querySelector('[wire\\:id] [wire\\:click="closeCustomerModal"]');
            if (customerModal && e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
                // This is a simplified approach - Livewire will handle the actual closing
            }
        });
    });
</script>