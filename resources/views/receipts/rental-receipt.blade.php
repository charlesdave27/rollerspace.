<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Receipt #{{ $rental->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        
        .receipt-container {
            max-width: 80mm;
            margin: 0 auto;
            padding: 15px;
            background: white;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        
        .header h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 10px;
            color: #666;
        }
        
        .receipt-info {
            margin-bottom: 15px;
        }
        
        .receipt-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .receipt-info-label {
            font-weight: bold;
        }
        
        .section {
            margin-bottom: 15px;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 13px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .customer-info {
            font-size: 11px;
            line-height: 1.8;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .items-table th {
            background-color: #f5f5f5;
            padding: 8px 5px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
        }
        
        .items-table td {
            padding: 6px 5px;
            font-size: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        .total-section {
            margin-top: 15px;
            border-top: 2px solid #000;
            padding-top: 10px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 12px;
        }
        
        .total-row.final {
            font-weight: bold;
            font-size: 14px;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid #ddd;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        
        .notes {
            margin-top: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid #333;
            font-size: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .status-active {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        
        .status-returned {
            background-color: #e8f5e9;
            color: #388e3c;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h1>RENTAL RECEIPT</h1>
            <p>Roller Space Rental</p>
        </div>
        
        <div class="receipt-info">
            <div class="receipt-info-row">
                <span class="receipt-info-label">Receipt #:</span>
                <span>#{{ str_pad($rental->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="receipt-info-row">
                <span class="receipt-info-label">Date:</span>
                <span>{{ $rental->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div class="receipt-info-row">
                <span class="receipt-info-label">Status:</span>
                <span>
                    <span class="status-badge {{ $rental->returned ? 'status-returned' : 'status-active' }}">
                        {{ $rental->returned ? 'RETURNED' : 'ACTIVE' }}
                    </span>
                </span>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">CUSTOMER INFORMATION</div>
            <div class="customer-info">
                <div><strong>Name:</strong> {{ $rental->name ?? 'Walk-in Customer' }}</div>
                @if($rental->loyaltyMember)
                    <div><strong>Member ID:</strong> #{{ $rental->loyaltyMember->id }}</div>
                    <div><strong>Points Balance:</strong> {{ $rental->loyaltyMember->loyalty_points ?? 0 }} pts</div>
                @else
                    <div><strong>Type:</strong> Walk-in Customer</div>
                @endif
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">RENTAL DETAILS</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="text-align: right;">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @if($rental->rentalPackage)
                        <tr>
                            <td><strong>Package:</strong></td>
                            <td style="text-align: right;">{{ $rental->rentalPackage->name ?? 'N/A' }}</td>
                        </tr>
                        @if($rental->rentalPackage->duration)
                            <tr>
                                <td>Duration:</td>
                                <td style="text-align: right;">{{ $rental->rentalPackage->duration }} hours</td>
                            </tr>
                        @endif
                    @elseif($rental->reward)
                        <tr>
                            <td><strong>Reward:</strong></td>
                            <td style="text-align: right;">{{ $rental->reward->name ?? 'N/A' }}</td>
                        </tr>
                        @if($rental->reward->duration ?? null)
                            <tr>
                                <td>Duration:</td>
                                <td style="text-align: right;">{{ $rental->reward->duration }} hours</td>
                            </tr>
                        @endif
                    @endif
                    
                    @if($rental->equipments->count() > 0)
                        <tr>
                            <td colspan="2" style="padding-top: 10px;"><strong>Equipment Rented:</strong></td>
                        </tr>
                        @foreach($rental->equipments as $equipment)
                            <tr>
                                <td style="padding-left: 15px;">• {{ $equipment->name }}</td>
                                <td style="text-align: right;">{{ $equipment->type }} ({{ $equipment->size }})</td>
                            </tr>
                        @endforeach
                    @elseif($rental->equipment)
                        <tr>
                            <td colspan="2" style="padding-top: 10px;"><strong>Equipment Rented:</strong></td>
                        </tr>
                        <tr>
                            <td style="padding-left: 15px;">• {{ $rental->equipment->name }}</td>
                            <td style="text-align: right;">{{ $rental->equipment->type }} ({{ $rental->equipment->size }})</td>
                        </tr>
                    @endif
                    
                    @if($rental->deadline)
                        <tr>
                            <td><strong>Return Deadline:</strong></td>
                            <td style="text-align: right;">{{ \Carbon\Carbon::parse($rental->deadline)->format('M d, Y h:i A') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <div class="section-title">PAYMENT SUMMARY</div>
            <div class="total-section">
                @if($rental->price_paid)
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>₱{{ number_format($rental->price_paid, 2) }}</span>
                    </div>
                    <div class="total-row final">
                        <span>TOTAL PAID:</span>
                        <span>₱{{ number_format($rental->price_paid, 2) }}</span>
                    </div>
                @elseif($rental->reward)
                    <div class="total-row final">
                        <span>PAYMENT METHOD:</span>
                        <span>Reward Redemption</span>
                    </div>
                    @if($rental->reward->required_points ?? null)
                        <div class="total-row">
                            <span>Points Used:</span>
                            <span>{{ $rental->reward->required_points }} pts</span>
                        </div>
                    @endif
                @else
                    <div class="total-row final">
                        <span>TOTAL PAID:</span>
                        <span>₱0.00</span>
                    </div>
                @endif
                
                @if($rental->points && $rental->loyaltyMember)
                    <div class="total-row" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd;">
                        <span>Points Earned:</span>
                        <span>+{{ $rental->points }} pts</span>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="footer">
            <p>Thank you for your rental!</p>
            <p>Please return equipment by the deadline to avoid late fees.</p>
            <p style="margin-top: 10px;">Generated on {{ now()->format('M d, Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>

