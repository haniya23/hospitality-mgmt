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
            line-height: 1.3;
            color: #333;
            background: #fff;
            font-size: 11px;
        }
        
        .invoice-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm;
            background: #fff;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2c3e50;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-info h1 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .company-info p {
            color: #666;
            font-size: 11px;
            margin: 2px 0;
            line-height: 1.4;
        }
        
        .invoice-info {
            text-align: right;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            min-width: 200px;
        }
        
        .invoice-info h2 {
            color: #2c3e50;
            font-size: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .invoice-info p {
            color: #333;
            font-size: 11px;
            margin: 3px 0;
            line-height: 1.4;
        }
        
        .guest-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #3498db;
        }
        
        .guest-section h3 {
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }
        
        .guest-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .guest-info p {
            color: #333;
            font-size: 11px;
            margin: 3px 0;
            line-height: 1.4;
        }
        
        .guest-info strong {
            color: #2c3e50;
            font-weight: bold;
        }
        
        .booking-details {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 2px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .booking-details h3 {
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .detail-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 11px;
        }
        
        .detail-value {
            color: #333;
            font-size: 11px;
            font-weight: 500;
        }
        
        .accommodation-details {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 2px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .accommodation-details h3 {
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }
        
        .accommodation-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .financial-summary {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 2px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .financial-summary h3 {
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }
        
        .financial-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .total-section {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 25px;
            text-align: center;
            margin-bottom: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .total-section h3 {
            font-size: 18px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .total-amount {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .payment-status {
            font-size: 13px;
            opacity: 0.9;
        }
        
        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        
        .footer p {
            margin: 3px 0;
        }
        
        .terms-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 4px solid #3498db;
        }
        
        .terms-section h4 {
            color: #2c3e50;
            font-size: 12px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }
        
        .terms-section p {
            color: #666;
            font-size: 10px;
            line-height: 1.5;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #3498db;
            color: white;
        }
        
        .status-confirmed {
            background: #27ae60;
        }
        
        .status-pending {
            background: #f39c12;
        }
        
        .status-cancelled {
            background: #e74c3c;
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
                padding: 12mm;
                margin: 0;
            }
            
            .header {
                page-break-inside: avoid;
                margin-bottom: 15px;
            }
            
            .guest-section {
                page-break-inside: avoid;
                margin-bottom: 15px;
            }
            
            .booking-details {
                page-break-inside: avoid;
                margin-bottom: 15px;
            }
            
            .accommodation-details {
                page-break-inside: avoid;
                margin-bottom: 15px;
            }
            
            .financial-summary {
                page-break-inside: avoid;
                margin-bottom: 15px;
            }
            
            .total-section {
                page-break-inside: avoid;
                margin-bottom: 15px;
            }
            
            .terms-section {
                page-break-inside: avoid;
                margin-top: 15px;
            }
            
            .footer {
                page-break-inside: avoid;
                margin-top: 15px;
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

        <!-- Guest Information -->
        <div class="guest-section">
            <h3>Guest Information</h3>
            <div class="guest-info">
                <div>
                    <p><strong>Name:</strong> {{ $guest_details->name }}</p>
                    <p><strong>Email:</strong> {{ $guest_details->email }}</p>
                    <p><strong>Mobile:</strong> {{ $guest_details->mobile_number }}</p>
                </div>
                <div>
                    @if($guest_details->address)
                        <p><strong>Address:</strong> {{ $guest_details->address }}</p>
                    @endif
                    @if($guest_details->id_type && $guest_details->id_number)
                        <p><strong>ID:</strong> {{ $guest_details->id_type }} - {{ $guest_details->id_number }}</p>
                    @endif
                </div>
            </div>
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
                    <span class="detail-value">₹{{ number_format($accommodation_details->base_price, 2) }}/night</span>
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
                    <span class="detail-value">₹{{ number_format($accommodation_details->base_price * $nights, 2) }}</span>
                </div>
                @if($booking->rate_override)
                <div class="detail-item">
                    <span class="detail-label">Rate Override:</span>
                    <span class="detail-value">₹{{ number_format($booking->rate_override, 2) }}</span>
                </div>
                @endif
                <div class="detail-item">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value">₹{{ number_format($booking->total_amount, 2) }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Advance Paid:</span>
                    <span class="detail-value">₹{{ number_format($booking->advance_paid, 2) }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Balance Pending:</span>
                    <span class="detail-value">₹{{ number_format($booking->balance_pending, 2) }}</span>
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
            <div class="total-amount">₹{{ number_format($booking->total_amount, 2) }}</div>
            <div class="payment-status">
                @if($booking->balance_pending > 0)
                    Balance Due: ₹{{ number_format($booking->balance_pending, 2) }}
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
