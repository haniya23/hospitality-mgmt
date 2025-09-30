<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $booking->confirmation_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.4;
            color: #000;
            background: #fff;
            font-size: 12px;
        }
        
        .rupee {
            font-family: 'Arial', sans-serif;
            font-weight: bold;
        }
        
        .invoice-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 10mm;
            background: #fff;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
            min-height: 120px;
        }
        
        .company-info {
            flex: 1;
            margin-right: 20px;
        }
        
        .company-info h1 {
            color: #000;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
            line-height: 1.2;
        }
        
        .company-info p {
            color: #000;
            font-size: 10px;
            margin: 1px 0;
            line-height: 1.2;
        }
        
        .invoice-info {
            text-align: right;
            flex: 0 0 200px;
            min-width: 200px;
        }
        
        .invoice-info h2 {
            color: #000;
            font-size: 16px;
            margin-bottom: 8px;
            font-weight: bold;
            line-height: 1.2;
        }
        
        .invoice-info p {
            color: #000;
            font-size: 10px;
            margin: 1px 0;
            line-height: 1.2;
            word-break: break-all;
        }
        
        .billing-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .billing-box {
            flex: 1;
            margin: 0 5px;
            padding: 8px;
            background: #fff;
            border: 1px solid #000;
        }
        
        .billing-box h3 {
            color: #000;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        
        .billing-box p {
            color: #000;
            font-size: 11px;
            margin: 2px 0;
        }
        
        .booking-details {
            background: #fff;
            padding: 12px;
            border: 1px solid #000;
            margin-bottom: 15px;
        }
        
        .booking-details h3 {
            color: #000;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #ccc;
        }
        
        .detail-label {
            font-weight: 600;
            color: #000;
            font-size: 11px;
        }
        
        .detail-value {
            color: #000;
            font-size: 11px;
        }
        
        .accommodation-details {
            background: #fff;
            padding: 12px;
            border: 1px solid #000;
            margin-bottom: 15px;
        }
        
        .accommodation-details h3 {
            color: #000;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        
        .accommodation-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .financial-summary {
            background: #fff;
            padding: 12px;
            border: 1px solid #000;
            margin-bottom: 15px;
        }
        
        .financial-summary h3 {
            color: #000;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        
        .financial-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .total-section {
            background: #000;
            color: white;
            padding: 12px;
            text-align: center;
            margin-bottom: 15px;
        }
        
        .total-section h3 {
            font-size: 16px;
            margin-bottom: 8px;
        }
        
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .payment-status {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .b2b-section {
            background: #fff;
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .b2b-section h3 {
            color: #000;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        
        .commission-details {
            background: #fff;
            border: 1px solid #000;
            padding: 12px;
            margin-bottom: 15px;
        }
        
        .commission-details h3 {
            color: #000;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #000;
            text-align: center;
            color: #000;
            font-size: 10px;
        }
        
        .footer p {
            margin: 3px 0;
        }
        
        .terms-section {
            background: #fff;
            padding: 12px;
            border: 1px solid #000;
            margin-top: 15px;
        }
        
        .terms-section h4 {
            color: #000;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        
        .terms-section p {
            color: #000;
            font-size: 10px;
            line-height: 1.4;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border: 1px solid #000;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #fff;
            color: #000;
        }
        
        @media print {
            body {
                font-size: 10px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .invoice-container {
                width: 100%;
                min-height: 100%;
                padding: 8mm;
                margin: 0;
            }
            
            .header {
                page-break-inside: avoid;
                margin-bottom: 10px;
                min-height: 100px;
            }
            
            .company-info {
                flex: 1;
                margin-right: 15px;
            }
            
            .invoice-info {
                flex: 0 0 180px;
                min-width: 180px;
            }
            
            .billing-section {
                page-break-inside: avoid;
                margin-bottom: 10px;
            }
            
            .booking-details {
                page-break-inside: avoid;
                margin-bottom: 10px;
            }
            
            .accommodation-details {
                page-break-inside: avoid;
                margin-bottom: 10px;
            }
            
            .financial-summary {
                page-break-inside: avoid;
                margin-bottom: 10px;
            }
            
            .commission-details {
                page-break-inside: avoid;
                margin-bottom: 10px;
            }
            
            .total-section {
                page-break-inside: avoid;
                margin-bottom: 10px;
            }
            
            .terms-section {
                page-break-inside: avoid;
                margin-top: 10px;
            }
            
            .footer {
                page-break-inside: avoid;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>{{ $property_owner->name ?? 'Hospitality Management' }}</h1>
                <p>{{ $property_location->address ?? 'Property Address' }}</p>
                <p>{{ $property_location->city->name ?? 'City' }}, {{ $property_location->city->district->name ?? 'District' }}, {{ $property_location->city->district->state->name ?? 'State' }}</p>
                <p>PIN: {{ $property_location->pincode ?? 'N/A' }}</p>
                <p>Phone: {{ $property_owner->mobile_number ?? 'N/A' }}</p>
                <p>Email: {{ $property_owner->email ?? 'N/A' }}</p>
            </div>
            <div class="invoice-info">
                <h2>INVOICE</h2>
                <p><strong>Invoice No:</strong><br>{{ $invoice_number }}</p>
                <p><strong>Date:</strong><br>{{ $invoice_date }}</p>
                <p><strong>Due Date:</strong><br>{{ $due_date }}</p>
                <p><strong>Confirmation:</strong><br>{{ $booking->confirmation_number }}</p>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="billing-section">
            <div class="billing-box">
                <h3>Bill To</h3>
                <p><strong>{{ $guest_details->name }}</strong></p>
                <p>{{ $guest_details->email }}</p>
                <p>{{ $guest_details->mobile_number }}</p>
                @if($guest_details->address)
                    <p>{{ $guest_details->address }}</p>
                @endif
                @if($guest_details->id_type && $guest_details->id_number)
                    <p>ID: {{ $guest_details->id_type }} - {{ $guest_details->id_number }}</p>
                @endif
            </div>
            
            @if($is_b2b_booking && $b2b_partner)
            <div class="billing-box">
                <h3>B2B Partner</h3>
                <p><strong>{{ $b2b_partner->partner_name }}</strong></p>
                <p>{{ $b2b_partner->email }}</p>
                <p>{{ $b2b_partner->phone }}</p>
                <p>Commission Rate: {{ $b2b_partner->commission_rate }}%</p>
            </div>
            @endif
        </div>

        <!-- Booking Details -->
        <div class="booking-details">
            <h3>Booking Information</h3>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Check-in Date:</span>
                    <span class="detail-value">{{ $booking->check_in_date->format('d/m/Y') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Check-out Date:</span>
                    <span class="detail-value">{{ $booking->check_out_date->format('d/m/Y') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Duration:</span>
                    <span class="detail-value">{{ $nights }} {{ $nights == 1 ? 'Night' : 'Nights' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Guests:</span>
                    <span class="detail-value">{{ $booking->adults }} Adults, {{ $booking->children }} Children</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="status-badge status-{{ $booking->status }}">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Booking Date:</span>
                    <span class="detail-value">{{ $booking->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Accommodation Details -->
        <div class="accommodation-details">
            <h3>Accommodation Details</h3>
            <div class="accommodation-info">
                <div class="detail-item">
                    <span class="detail-label">Property:</span>
                    <span class="detail-value">{{ $accommodation_details->property->name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Accommodation Type:</span>
                    <span class="detail-value">{{ $accommodation_details->display_name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Max Occupancy:</span>
                    <span class="detail-value">{{ $accommodation_details->max_occupancy }} Guests</span>
                </div>
                @if($accommodation_details->size)
                <div class="detail-item">
                    <span class="detail-label">Size:</span>
                    <span class="detail-value">{{ $accommodation_details->size }} sq ft</span>
                </div>
                @endif
                <div class="detail-item">
                    <span class="detail-label">Base Price:</span>
                    <span class="detail-value">&#8377;{{ number_format($accommodation_details->base_price, 2) }}/night</span>
                </div>
                @if($accommodation_details->description)
                <div class="detail-item" style="grid-column: 1 / -1;">
                    <span class="detail-label">Description:</span>
                    <span class="detail-value">{{ $accommodation_details->description }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="financial-summary">
            <h3>Financial Summary</h3>
            <div class="financial-grid">
                <div class="detail-item">
                    <span class="detail-label">Base Amount ({{ $nights }} nights):</span>
                    <span class="detail-value">&#8377;{{ number_format($accommodation_details->base_price * $nights, 2) }}</span>
                </div>
                @if($booking->rate_override)
                <div class="detail-item">
                    <span class="detail-label">Rate Override:</span>
                    <span class="detail-value">&#8377;{{ number_format($booking->rate_override, 2) }}</span>
                </div>
                @endif
                <div class="detail-item">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value">&#8377;{{ number_format($booking->total_amount, 2) }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Advance Paid:</span>
                    <span class="detail-value">&#8377;{{ number_format($booking->advance_paid, 2) }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Balance Pending:</span>
                    <span class="detail-value">&#8377;{{ number_format($booking->balance_pending, 2) }}</span>
                </div>
                @if($booking->override_reason)
                <div class="detail-item" style="grid-column: 1 / -1;">
                    <span class="detail-label">Override Reason:</span>
                    <span class="detail-value">{{ $booking->override_reason }}</span>
                </div>
                @endif
            </div>
        </div>


        <!-- Total Section -->
        <div class="total-section">
            <h3>Total Amount</h3>
            <div class="total-amount">&#8377;{{ number_format($booking->total_amount, 2) }}</div>
            <div class="payment-status">
                @if($booking->balance_pending > 0)
                    Balance Due: &#8377;{{ number_format($booking->balance_pending, 2) }}
                @else
                    Fully Paid
                @endif
            </div>
        </div>

        <!-- Special Requests -->
        @if($booking->special_requests)
        <div class="terms-section">
            <h4>Special Requests</h4>
            <p>{{ $booking->special_requests }}</p>
        </div>
        @endif

        <!-- Notes -->
        @if($booking->notes)
        <div class="terms-section">
            <h4>Notes</h4>
            <p>{{ $booking->notes }}</p>
        </div>
        @endif

        <!-- Terms and Conditions -->
        <div class="terms-section">
            <h4>Terms & Conditions</h4>
            <p>
                • Check-in time: 2:00 PM | Check-out time: 11:00 AM<br>
                • Cancellation policy applies as per booking terms<br>
                • All payments are non-refundable unless otherwise specified<br>
                • Guest is responsible for any damages to the property<br>
                • This invoice is generated automatically and is valid for accounting purposes
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for choosing our hospitality services!</strong></p>
            <p>For any queries, please contact us at {{ $property_owner->mobile_number ?? 'N/A' }}</p>
            <p>This is a computer-generated invoice and does not require a signature.</p>
            <p>Generated on {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
