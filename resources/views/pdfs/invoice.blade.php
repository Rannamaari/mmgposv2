<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 5px;
        }
        .company-address {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .company-contact {
            font-size: 14px;
            color: #666;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-info {
            flex: 1;
        }
        .customer-info {
            flex: 1;
            text-align: right;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        .items-table td {
            border: 1px solid #ddd;
            padding: 12px;
        }
        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .totals {
            float: right;
            width: 300px;
            margin-bottom: 30px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .total-row.final {
            font-weight: bold;
            font-size: 18px;
            border-bottom: 2px solid #e74c3c;
            color: #e74c3c;
        }
        .payment-info {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">Micro Moto Garage</div>
        <div class="company-address">Janavaree Hingun</div>
        <div class="company-contact">micronet.mv/mmg | Phone: 009607779493</div>
    </div>

    <!-- Invoice Details -->
    <div class="invoice-details">
        <div class="invoice-info">
            <div class="section-title">Invoice Details</div>
            <p><strong>Invoice Number:</strong> {{ $invoice->number }}</p>
            <p><strong>Date:</strong> {{ $invoice->created_at->format('F d, Y') }}</p>
            <p><strong>Status:</strong> 
                <span class="status-badge status-{{ $invoice->status }}">
                    {{ ucfirst($invoice->status) }}
                </span>
            </p>
        </div>
        <div class="customer-info">
            <div class="section-title">Customer Information</div>
            <p><strong>Name:</strong> {{ $invoice->customer->name ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $invoice->customer->phone ?? 'N/A' }}</p>
            <p><strong>Vehicle:</strong> {{ $invoice->motorcycle->plate_no ?? 'N/A' }}</p>
            <p><strong>Model:</strong> {{ $invoice->motorcycle->model ?? 'N/A' }}</p>
        </div>
    </div>

    <!-- Items Table -->
    <div class="section-title">Items & Services</div>
    @if($invoice->workOrderItems->count() > 0)
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item/Service</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Unit Price (MVR)</th>
                    <th>Line Total (MVR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->workOrderItems as $item)
                    <tr>
                        <td>{{ $item->name_snapshot }}</td>
                        <td>{{ ucfirst($item->item_type) }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: #666; font-style: italic;">No items found for this invoice.</p>
    @endif

    <!-- Totals -->
    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>MVR {{ number_format($invoice->subtotal, 2) }}</span>
        </div>
        @if($invoice->tax > 0)
            <div class="total-row">
                <span>GST (8%):</span>
                <span>MVR {{ number_format($invoice->tax, 2) }}</span>
            </div>
        @endif
        @if($invoice->discount > 0)
            <div class="total-row">
                <span>Discount:</span>
                <span>-MVR {{ number_format($invoice->discount, 2) }}</span>
            </div>
        @endif
        <div class="total-row final">
            <span>Total Amount:</span>
            <span>MVR {{ number_format($invoice->total, 2) }}</span>
        </div>
    </div>

    <!-- Payment Information -->
    @if($invoice->payments->count() > 0)
        <div class="payment-info">
            <div class="section-title">Payment Information</div>
            @foreach($invoice->payments as $payment)
                <div style="margin-bottom: 15px;">
                    <p><strong>Method:</strong> {{ $payment->method === 'bml_transfer' ? 'BML Transfer' : 'Cash' }}</p>
                    <p><strong>Amount:</strong> MVR {{ number_format($payment->amount, 2) }}</p>
                    <p><strong>Date:</strong> {{ $payment->received_at ? $payment->received_at->format('F d, Y H:i') : 'N/A' }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Work Order Information -->
    @if($invoice->workOrder)
        <div style="margin-top: 30px;">
            <div class="section-title">Work Order Information</div>
            <p><strong>Work Order Number:</strong> {{ $invoice->workOrder->number ?? 'N/A' }}</p>
            <p><strong>Status:</strong> {{ ucfirst($invoice->workOrder->status ?? 'N/A') }}</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for choosing Micro Moto Garage!</p>
        <p>For any queries, please contact us at 009607779493</p>
        <p>This is a computer-generated invoice. No signature required.</p>
    </div>
</body>
</html>
