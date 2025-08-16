<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Invoice Header -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Invoice #{{ $this->record->number }}</h2>
                    <p class="text-gray-600">Created on {{ $this->record->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $this->record->status === 'paid' ? 'bg-green-100 text-green-800' :
    ($this->record->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($this->record->status) }}
                    </span>
                    <div class="mt-4 space-x-2">
                        <a href="{{ route('invoice.view-pdf', $this->record->id) }}" target="_blank"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            View PDF
                        </a>
                        <a href="{{ route('invoice.pdf', $this->record->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Customer Name</p>
                    <p class="text-lg">{{ $this->record->customer->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Phone Number</p>
                    <p class="text-lg">{{ $this->record->customer->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Vehicle Plate</p>
                    <p class="text-lg">{{ $this->record->motorcycle->plate_no ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Vehicle Model</p>
                    <p class="text-lg">{{ $this->record->motorcycle->model ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Invoice Items</h3>
            @if($this->record->workOrderItems->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Item</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Unit Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($this->record->workOrderItems as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $item->name_snapshot }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ucfirst($item->item_type) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item->qty }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        MVR {{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        MVR {{ number_format($item->line_total, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">No items found for this invoice.</p>
            @endif
        </div>

        <!-- Financial Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Financial Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium">MVR {{ number_format($this->record->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">GST ({{ $this->record->tax > 0 ? '8%' : '0%' }}):</span>
                    <span class="font-medium">MVR {{ number_format($this->record->tax, 2) }}</span>
                </div>
                @if($this->record->discount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Discount:</span>
                        <span class="font-medium text-red-600">-MVR {{ number_format($this->record->discount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-lg font-bold border-t pt-3">
                    <span>Total:</span>
                    <span>MVR {{ number_format($this->record->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h3>
            @if($this->record->payments->count() > 0)
                @foreach($this->record->payments as $payment)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium">{{ $payment->method === 'bml_transfer' ? 'BML Transfer' : 'Cash' }}</p>
                                <p class="text-sm text-gray-500">{{ $payment->received_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold">MVR {{ number_format($payment->amount, 2) }}</p>
                                @if($payment->proof_image_path)
                                    <a href="{{ Storage::url($payment->proof_image_path) }}" target="_blank"
                                        class="text-sm text-blue-600 hover:text-blue-800">View Proof</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-gray-500">No payment information available.</p>
            @endif
        </div>

        <!-- Work Order Information -->
        @if($this->record->workOrder)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Work Order Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Work Order Number</p>
                        <p class="text-lg">{{ $this->record->workOrder->number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Work Order Status</p>
                        <p class="text-lg">{{ ucfirst($this->record->workOrder->status ?? 'N/A') }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>