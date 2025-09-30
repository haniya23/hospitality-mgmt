<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - {{ $booking->confirmation_number }}</title>
    
    {{-- 
        NOTE FOR DOMPDF: For the 'Poppins' font to work, you must install the TTF font files 
        and configure them in your dompdf settings. The @import may not work directly.
    --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'DejaVu Sans', sans-serif; /* DejaVu Sans is a good fallback for symbols */
            background-color: #ffffff;
            font-size: 11px;
            color: #555;
        }

        .invoice-container {
            width: 100%;
            position: relative;
            background: #fff;
        }
        
        /* --- Header with Teal Curve --- */
        .header {
            color: #ffffff;
            padding: 40px 50px;
            position: relative;
            z-index: 1;
            height: 200px;
        }

        .header-background {
            position: absolute;
            top: -100px;
            left: -100px;
            width: 650px;
            height: 300px;
            background-color:rgb(171, 208, 206); /* Main Teal Color */
            border-bottom-right-radius: 200px;
            z-index: -1;
        }

        .header h1 {
            font-size: 42px;
            margin: 0;
            font-weight: 700;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 13px;
        }

        .company-info {
            position: absolute;
            top: 40px;
            right: 50px;
            text-align: right;
        }

        .company-info h2 {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
            color: #333;
        }

        .company-info p {
            font-size: 11px;
            color: #888;
            margin: 0;
        }

        /* --- Customer & Payment Details Table for Layout --- */
        .details-section {
            width: 100%;
            padding: 20px 50px 0px;
        }

        .details-section td {
            width: 50%;
            vertical-align: top;
        }

        .details-section h3 {
            font-size: 14px;
            color: #00A99D;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .details-section p {
            font-size: 11px;
            color: #555;
            margin: 4px 0;
            line-height: 1.6;
        }

        .details-section .label {
            color: #333;
            font-weight: 500;
            display: inline-block;
            width: 50px;
        }
        
        /* --- Items Section --- */
        .items-section {
            padding: 30px 50px;
        }
        
        .items-header {
            background-color: #00A99D;
            color: #ffffff;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 12px;
            border-radius: 8px;
        }
        
        .items-header-table, .item-row-table {
            width: 100%;
            border-collapse: collapse;
        }

        .item-row {
            background-color: #F0FDFB; /* Light teal for rows */
            border-radius: 8px;
            padding: 12px 20px;
            margin-top: 10px;
        }
        
        .items-section .col-1 { width: 5%; }
        .items-section .col-2 { width: 45%; }
        .items-section .col-3 { width: 15%; text-align: center; }
        .items-section .col-4 { width: 15%; text-align: right; }
        .items-section .col-5 { width: 20%; text-align: right; }
        
        .item-description { font-weight: 500; color: #333; }
        
        /* --- Totals Section --- */
        .totals-section {
            padding: 0 50px;
            page-break-inside: avoid;
        }

        .totals-table {
            width: 300px;
            float: right;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 0;
            font-size: 12px;
            color: #555;
        }
        
        .totals-table .grand-total-label {
            font-weight: 600;
            color: #333;
            border-top: 1px solid #eee;
        }

        .totals-table .grand-total-value {
            font-weight: 700;
            color: #00A99D;
            font-size: 16px;
            text-align: right;
            border-top: 1px solid #eee;
        }

        .totals-table .label { text-align: left; }
        .totals-table .value { text-align: right; }
        
        /* --- Footer --- */
        .footer {
            padding: 40px 50px;
            position: relative;
            width: 100%;
            page-break-before: auto;
            page-break-inside: avoid;
        }
        
        .footer-background {
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background-color: #00A99D;
            border-top-left-radius: 100px;
            z-index: 0;
        }
        
        .footer-content {
            position: relative;
            z-index: 1;
        }

        .footer h3 {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            margin: 0 0 10px;
        }
        
        .footer h4 {
            font-size: 13px;
            color: #00A99D;
            margin: 15px 0 8px;
            font-weight: 600;
        }

        .footer p {
            font-size: 11px;
            color: #666;
            margin: 2px 0;
            line-height: 1.6;
        }
        
        .footer .footer-table { width: 100%; }
        .footer .footer-table td { width: 50%; vertical-align: top; }

    </style>
</head>
<body>

    <div class="invoice-container">
        
        <div class="header">
            <div class="header-background"></div>
            <h1>INVOICE</h1>
            <p>No. {{ $booking->confirmation_number }}</p>
            <p>Date: {{ $invoice_date }}</p>

            <div class="company-info">
                <h2>{{ $property_owner->name ?? 'Hospitality Management' }}</h2>
                <p>Your Trusted Hospitality Partner</p>
            </div>
        </div>

        <table class="details-section">
            <tr>
                <td>
                    <h3>Bill to.</h3>
                    <p><span class="label">Name</span>: {{ $guest_details->name }}</p>
                    <p><span class="label">Phone</span>: {{ $guest_details->mobile_number }}</p>
                    <p><span class="label">Mail</span>: {{ $guest_details->email }}</p>
                    @if($guest_details->address)
                        <p><span class="label">Address</span>: {{ $guest_details->address }}</p>
                    @endif
                </td>
                <td>
                    <h3>Payment Method.</h3>
                    <p>Bank Transfer Recommended</p>
                    <p style="color:#888;">(Details available on request)</p>
                    <br>
                    <p>Due Date: {{ $due_date }}</p>
                </td>
            </tr>
        </table>

        <div class="items-section">
            <div class="items-header">
                <table class="items-header-table">
                    <tr>
                        <td class="col-1">No.</td>
                        <td class="col-2">ITEM DESCRIPTION</td>
                        <td class="col-3">QTY</td>
                        <td class="col-4">PRICE</td>
                        <td class="col-5">TOTAL</td>
                    </tr>
                </table>
            </div>
            
            <div class="item-row">
                 <table class="item-row-table">
                    <tr>
                        <td class="col-1">1</td>
                        <td class="col-2 item-description">
                            {{ $accommodation_details->display_name }} Booking<br>
                            <small style="color: #777;">({{ $nights }} Nights from {{ $booking->check_in_date->format('d/m/Y') }})</small>
                        </td>
                        <td class="col-3">1</td>
                        <td class="col-4">&#8377;{{ number_format($booking->total_amount, 2) }}</td>
                        <td class="col-5">&#8377;{{ number_format($booking->total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="grand-total-label">Grand Total</td>
                    <td class="grand-total-value">&#8377;{{ number_format($booking->total_amount, 2) }}</td>
                </tr>
                 <tr>
                    <td class="label">Advance Paid</td>
                    <td class="value">&#8377;{{ number_format($booking->advance_paid, 2) }}</td>
                </tr>
                <tr>
                    <td class="label"><b>Balance Due</b></td>
                    <td class="value"><b>&#8377;{{ number_format($booking->balance_pending, 2) }}</b></td>
                </tr>
            </table>
        </div>

        <div style="clear: both;"></div>

        <div class="footer">
            <div class="footer-background"></div>
            <div class="footer-content">
                <table class="footer-table">
                    <tr>
                        <td>
                             <h3>THANK YOU</h3>
                            <p>Best Regards,</p>
                            <p><b>{{ $property_owner->name ?? 'Management' }}</b></p>
                            
                            <h4>Contact.</h4>
                            <p><b>Phone</b>: {{ $property_owner->mobile_number ?? 'N/A' }}</p>
                            <p><b>Email</b>: {{ $property_owner->email ?? 'N/A' }}</p>
                        </td>
                        <td>
                            <h4>Terms and Condition.</h4>
                            <p>
                                • Check-in time: 2:00 PM | Check-out time: 11:00 AM<br>
                                • Cancellation policy applies as per booking terms.<br>
                                • All payments are non-refundable unless otherwise specified.<br>
                                • Guest is responsible for any damages to the property.
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
