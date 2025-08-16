<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\{Customer, Motorcycle, Service, Part, WorkOrder, Invoice, WorkOrderItem, Payment, InventoryMovement};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class POSPage extends Component
{
    use WithFileUploads;

    // Individual properties for Livewire binding
    public $customer_id = null;
    public $motorcycle_id = null;
    public $item_name = '';
    public $name_snapshot = '';
    public $qty = 1;
    public $unit_price = 0;
    public $line_total = 0;
    public $cart_items = [];
    public $subtotal = 0;
    public $gst_rate = 8; // Default GST rate
    public $gst_enabled = true; // GST toggle
    public $gst_amount = 0;
    public $grand_total = 0;
    public $payment_method = 'cash';
    public $proof = null;

    // Walk-in customer properties
    public $is_walkin = false;
    public $walkin_name = '';
    public $walkin_phone = '';
    public $walkin_plate_no = '';
    public $walkin_model = '';

    // Customer creation properties
    public $show_customer_modal = false;
    public $new_customer_name = '';
    public $new_customer_phone = '';
    public $new_customer_plate_no = '';
    public $new_customer_model = '';

    public function mount()
    {
        $this->cart_items = [];
        $this->calculateTotals();
    }

    public function getSubtotalProperty(): float
    {
        $cartItems = $this->cart_items ?? [];
        $subtotal = collect($cartItems)->sum(function ($item) {
            return (float) ($item['line_total'] ?? 0);
        });

        return $subtotal;
    }

    public function getGstAmountProperty(): float
    {
        if (!$this->gst_enabled) {
            return 0;
        }
        $subtotal = $this->subtotal;
        $gstRate = $this->gst_rate ?? 0;
        return ($subtotal * $gstRate) / 100;
    }

    public function getGrandTotalProperty(): float
    {
        return $this->subtotal + $this->gstAmount;
    }

    public function getQuickServicesProperty(): array
    {
        $services = Service::where('is_active', true)
            ->where('is_quick_service', true)
            ->orderBy('name')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => 'service:' . $service->id,
                    'name' => $service->name,
                    'default_price' => $service->default_price,
                    'category' => 'service',
                    'item_type' => 'service',
                    'item_id' => $service->id,
                ];
            });

        $parts = Part::where('is_active', true)
            ->where('is_quick_service', true)
            ->orderBy('name')
            ->get()
            ->map(function ($part) {
                return [
                    'id' => 'part:' . $part->id,
                    'name' => $part->name,
                    'default_price' => $part->price,
                    'category' => 'part',
                    'item_type' => 'part',
                    'item_id' => $part->id,
                ];
            });

        return $services->concat($parts)->toArray();
    }

    public function searchItems(?string $term): array
    {
        if (!$term) {
            return [];
        }

        $services = Service::query()
            ->where('is_active', true)
            ->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($term) . '%'])
                    ->orWhereRaw('LOWER(category) LIKE ?', ['%' . strtolower($term) . '%']);
            })
            ->limit(10)
            ->get()
            ->mapWithKeys(fn($s) => [
                "service:{$s->id}" => "🔧 {$s->name} (Service)"
            ]);

        $parts = Part::query()
            ->where('is_active', true)
            ->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($term) . '%'])
                    ->orWhereRaw('LOWER(sku) LIKE ?', ['%' . strtolower($term) . '%']);
            })
            ->limit(10)
            ->get()
            ->mapWithKeys(fn($p) => [
                "part:{$p->id}" => "🧩 {$p->name} — {$p->sku} (Stock: {$p->stock_qty})"
            ]);

        return $services->concat($parts)->take(20)->toArray();
    }

    public function getItemLabel(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        [$source, $id] = explode(':', $value);

        if ($source === 'service') {
            $service = Service::find($id);
            return $service ? "🔧 {$service->name} (Service)" : null;
        } else {
            $part = Part::find($id);
            return $part ? "🧩 {$part->name} — {$part->sku} (Stock: {$part->stock_qty})" : null;
        }
    }

    public function updatedCustomerId()
    {
        // Reset motorcycle when customer changes
        $this->motorcycle_id = null;
    }

    public function updatedIsWalkin()
    {
        // Reset customer and motorcycle when walk-in mode is toggled
        if ($this->is_walkin) {
            $this->customer_id = null;
            $this->motorcycle_id = null;
        }
    }

    public function updatedGstEnabled()
    {
        // Recalculate totals when GST toggle changes
        $this->calculateTotals();
    }



    public function updatedQty()
    {
        // Calculate line total when quantity changes
        $this->line_total = (float) $this->qty * (float) $this->unit_price;
    }

    public function updatedUnitPrice()
    {
        // Calculate line total when unit price changes
        $this->line_total = (float) $this->qty * (float) $this->unit_price;
    }

    public function updatedItemName()
    {
        // When item name changes, check if it's a selected item from dropdown
        if (str_contains($this->item_name, ':')) {
            [$source, $id] = explode(':', $this->item_name);

            if ($source === 'service') {
                $service = Service::find($id);
                if ($service) {
                    $this->name_snapshot = $service->name;
                    $this->unit_price = $service->default_price;
                    $this->line_total = (float) $this->qty * (float) $this->unit_price;
                }
            } elseif ($source === 'part') {
                $part = Part::find($id);
                if ($part) {
                    $this->name_snapshot = $part->name;
                    $this->unit_price = $part->price;
                    $this->line_total = (float) $this->qty * (float) $this->unit_price;
                }
            }
        }
    }

    public function addQuickService(string $quickServiceId, ?float $customPrice = null)
    {
        // Debug logging
        Log::info('addQuickService called', [
            'quickServiceId' => $quickServiceId,
            'customPrice' => $customPrice
        ]);

        // Parse the quick service ID to get type and ID
        $parts = explode(':', $quickServiceId);
        if (count($parts) !== 2) {
            session()->flash('error', 'Invalid quick service ID');
            return;
        }

        [$type, $id] = $parts;

        if ($type === 'service') {
            $item = Service::find($id);
            $defaultPrice = $item ? $item->default_price : 0;
        } elseif ($type === 'part') {
            $item = Part::find($id);
            $defaultPrice = $item ? $item->price : 0;
        } else {
            session()->flash('error', 'Invalid item type');
            return;
        }

        if (!$item) {
            Log::error('Item not found', ['type' => $type, 'id' => $id]);
            session()->flash('error', 'Item not found');
            return;
        }

        // Use custom price if provided, otherwise use default price
        $price = $customPrice ?? $defaultPrice;

        if ($price <= 0) {
            session()->flash('error', 'Please enter a valid price');
            return;
        }

        // Add to cart
        $cartItem = [
            'item_type' => $type,
            'item_id' => $item->id,
            'name_snapshot' => $item->name,
            'qty' => 1,
            'unit_price' => $price,
            'line_total' => $price,
        ];

        $this->cart_items[] = $cartItem;

        // Debug logging
        Log::info('Cart item added', [
            'cartItem' => $cartItem,
            'cartCount' => count($this->cart_items)
        ]);

        // Calculate totals
        $this->calculateTotals();

        // Debug logging
        Log::info('Totals calculated', [
            'subtotal' => $this->subtotal,
            'gst_amount' => $this->gst_amount,
            'grand_total' => $this->grand_total
        ]);

        // Add success notification
        session()->flash('success', $item->name . ' added to cart!');
    }

    public function addItemToCart()
    {
        $itemName = $this->item_name ?? '';
        $description = $this->name_snapshot ?? '';
        $qty = $this->qty ?? 1;
        $unitPrice = $this->unit_price ?? 0;

        // Check if we have a valid item (either from dropdown or manual entry)
        $hasValidItem = !empty($description) || (!empty($itemName) && !str_contains($itemName, ':'));
        $hasValidPrice = $unitPrice > 0;

        if (!$hasValidItem || !$hasValidPrice) {
            session()->flash('error', 'Please select an item and enter a price');
            return;
        }

        // Calculate line total properly
        $lineTotal = (float) $qty * (float) $unitPrice;

        // Use description as the display name (it contains the actual item name)
        $displayName = $description ?: $itemName;

        $cartItem = [
            'item_type' => 'custom',
            'item_id' => null,
            'name_snapshot' => $displayName,
            'qty' => (int) $qty,
            'unit_price' => (float) $unitPrice,
            'line_total' => $lineTotal,
        ];

        $this->cart_items[] = $cartItem;

        // Reset form
        $this->item_name = '';
        $this->name_snapshot = '';
        $this->qty = 1;
        $this->unit_price = 0;
        $this->line_total = 0;

        // Calculate totals
        $this->calculateTotals();

        session()->flash('success', "Added: {$displayName}");
    }

    public function removeFromCart($index)
    {
        if (isset($this->cart_items[$index])) {
            unset($this->cart_items[$index]);
            $this->cart_items = array_values($this->cart_items);
            $this->calculateTotals();
            session()->flash('success', 'Item removed from cart');
        }
    }

    public function clearCart()
    {
        $this->cart_items = [];
        $this->customer_id = null;
        $this->motorcycle_id = null;
        $this->item_name = '';
        $this->name_snapshot = '';
        $this->qty = 1;
        $this->unit_price = 0;
        $this->line_total = 0;
        $this->is_walkin = false;
        $this->walkin_name = '';
        $this->walkin_phone = '';
        $this->walkin_plate_no = '';
        $this->walkin_model = '';
        $this->calculateTotals();
        session()->flash('success', 'Cart cleared');
    }

    public function calculateTotals()
    {
        // Calculate subtotal from cart items
        $this->subtotal = collect($this->cart_items)->sum(function ($item) {
            return (float) ($item['line_total'] ?? 0);
        });

        // Calculate GST (only if enabled)
        if ($this->gst_enabled) {
            $this->gst_amount = ($this->subtotal * $this->gst_rate) / 100;
        } else {
            $this->gst_amount = 0;
        }

        // Calculate grand total
        $this->grand_total = $this->subtotal + $this->gst_amount;
    }



    public function save()
    {
        try {
            DB::beginTransaction();

            // Handle walk-in customer (cash bill without customer)
            if ($this->is_walkin) {
                // For walk-in customers, create minimal customer record if details provided
                if (!empty($this->walkin_name) && !empty($this->walkin_phone)) {
                    // Create walk-in customer with provided details
                    $customer = Customer::create([
                        'name' => $this->walkin_name,
                        'phone' => $this->walkin_phone,
                        'email' => null,
                        'address' => 'Walk-in Customer',
                        'is_active' => true,
                    ]);

                    // Create motorcycle for walk-in customer
                    $motorcycle = Motorcycle::create([
                        'customer_id' => $customer->id,
                        'plate_no' => $this->walkin_plate_no ?: 'WALK-IN',
                        'model' => $this->walkin_model ?: 'Walk-in Vehicle',
                        'year' => null,
                        'color' => null,
                        'is_active' => true,
                    ]);

                    $this->customer_id = $customer->id;
                    $this->motorcycle_id = $motorcycle->id;
                } else {
                    // Create generic walk-in customer for cash transactions
                    $customer = Customer::create([
                        'name' => 'Walk-in Customer',
                        'phone' => 'N/A',
                        'email' => null,
                        'address' => 'Cash Transaction',
                        'is_active' => true,
                    ]);

                    $motorcycle = Motorcycle::create([
                        'customer_id' => $customer->id,
                        'plate_no' => 'CASH',
                        'model' => 'Cash Transaction',
                        'year' => null,
                        'color' => null,
                        'is_active' => true,
                    ]);

                    $this->customer_id = $customer->id;
                    $this->motorcycle_id = $motorcycle->id;
                }
            } else {
                // Validate required fields for regular customer
                if (empty($this->customer_id)) {
                    session()->flash('error', 'Please select a customer.');
                    return;
                }

                if (empty($this->motorcycle_id)) {
                    session()->flash('error', 'Please select a motorcycle.');
                    return;
                }
            }

            if (empty($this->cart_items)) {
                session()->flash('error', 'Please add items to cart.');
                return;
            }

            // Validate payment method and proof for BML transfer
            if ($this->payment_method === 'bml_transfer' && empty($this->proof)) {
                session()->flash('error', 'Please upload a transfer screenshot for BML transfer.');
                return;
            }

            // Create invoice
            $invoice = Invoice::create([
                'number' => 'INV-' . now()->format('ymd-His'),
                'customer_id' => $this->customer_id,
                'motorcycle_id' => $this->motorcycle_id,
                'subtotal' => $this->subtotal,
                'discount' => 0,
                'tax' => $this->gst_amount,
                'total' => $this->grand_total,
                'status' => 'paid',
            ]);

            // Create work order items
            foreach ($this->cart_items as $item) {
                WorkOrderItem::create([
                    'work_order_id' => null, // No work order for direct sales
                    'invoice_id' => $invoice->id, // Link to invoice
                    'item_type' => $item['item_type'],
                    'item_id' => $item['item_id'],
                    'name_snapshot' => $item['name_snapshot'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                    'installed' => true,
                    'mechanic_id' => auth()->id() ?? 1,
                ]);
            }

            // Handle proof image (compress if provided)
            $proofPath = null;
            if (!empty($this->proof)) {
                $proofPath = $this->compressImage($this->proof);
            }



            // Create payment record
            Payment::create([
                'invoice_id' => $invoice->id,
                'method' => $this->payment_method,
                'amount' => $this->grand_total,
                'proof_image_path' => $proofPath,
                'received_by' => auth()->id() ?? 1,
                'received_at' => now(),

            ]);

            // Handle inventory movements for parts
            foreach ($this->cart_items as $item) {
                if ($item['item_type'] === 'part' && $item['item_id']) {
                    $part = Part::find($item['item_id']);
                    if ($part) {
                        InventoryMovement::create([
                            'part_id' => $part->id,
                            'change_qty' => -$item['qty'], // Negative for sales
                            'reason' => 'sale',
                            'ref_type' => 'invoice',
                            'ref_id' => $invoice->id,
                            'notes' => 'POS Sale - Invoice #' . $invoice->number,
                        ]);
                    }
                }
            }

            DB::commit();

            // Success message
            $customerType = $this->is_walkin ? 'Walk-in' : 'Regular';
            session()->flash('success', "✅ {$customerType} payment recorded: {$invoice->number}");

            // Clear cart
            $this->clearCart();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving payment: ' . $e->getMessage());
            session()->flash('error', 'Error saving payment: ' . $e->getMessage());
        }
    }

    protected function compressImage($image): ?string
    {
        try {
            $manager = new ImageManager(new Driver());
            $img = $manager->read($image->getRealPath());
            $img->scaleDown(1600);
            $new = 'proofs/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($new, $img->toJpeg(75));
            return $new;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function openCustomerModal()
    {
        $this->show_customer_modal = true;
        $this->resetCustomerForm();
    }

    public function closeCustomerModal()
    {
        $this->show_customer_modal = false;
        $this->resetCustomerForm();
    }

    public function resetCustomerForm()
    {
        $this->new_customer_name = '';
        $this->new_customer_phone = '';
        $this->new_customer_plate_no = '';
        $this->new_customer_model = '';
    }

    public function createCustomer()
    {
        // Validate required fields
        $this->validate([
            'new_customer_name' => 'required|string|max:255',
            'new_customer_phone' => 'nullable|string|max:20',
            'new_customer_plate_no' => 'nullable|string|max:20',
            'new_customer_model' => 'nullable|string|max:255',
        ], [
            'new_customer_name.required' => 'Customer name is required.',
            'new_customer_name.max' => 'Customer name cannot exceed 255 characters.',
            'new_customer_phone.max' => 'Phone number cannot exceed 20 characters.',
            'new_customer_plate_no.max' => 'Plate number cannot exceed 20 characters.',
            'new_customer_model.max' => 'Vehicle model cannot exceed 255 characters.',
        ]);

        try {
            DB::beginTransaction();

            // Create customer
            $customer = Customer::create([
                'name' => $this->new_customer_name,
                'phone' => $this->new_customer_phone ?: null,
            ]);

            // Create motorcycle if plate number is provided
            if (!empty($this->new_customer_plate_no)) {
                $motorcycle = Motorcycle::create([
                    'customer_id' => $customer->id,
                    'plate_no' => $this->new_customer_plate_no,
                    'model' => $this->new_customer_model ?: 'Unknown',
                    'year' => null,
                    'color' => null,
                ]);
            }

            DB::commit();

            // Set the newly created customer as selected
            $this->customer_id = $customer->id;
            if (isset($motorcycle)) {
                $this->motorcycle_id = $motorcycle->id;
            }

            // Close modal and show success message
            $this->closeCustomerModal();
            session()->flash('success', "✅ Customer '{$customer->name}' created successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating customer: ' . $e->getMessage());
            session()->flash('error', 'Error creating customer: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.p-o-s-page');
    }
}
