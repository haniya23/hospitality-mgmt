<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Subscription Limit Exceeded - Stay Loops</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @include('partials.styles')
    
    @include('partials.scripts')
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            padding: 1rem;
            margin: 0;
        }
        
        .limit-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            padding: 2rem;
            max-width: 500px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .limit-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ff6b6b, #feca57, #48dbfb, #ff9ff3);
        }
        
        .limit-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .limit-icon svg {
            width: 40px;
            height: 40px;
            color: white;
        }
        
        h1 {
            color: #2d3748;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        
        .limit-message {
            color: #4a5568;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .whatsapp-button {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: #25d366;
            color: white;
            text-decoration: none;
            padding: 14px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
            margin-bottom: 1.5rem;
            width: 100%;
            max-width: 300px;
            justify-content: center;
        }
        
        .whatsapp-button:hover {
            background: #128c7e;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
            color: white;
            text-decoration: none;
        }
        
        /* Plan Details Styles */
        .plan-details {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .plan-details h3 {
            color: #2d3748;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .plan-info {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .plan-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .plan-item:last-child {
            border-bottom: none;
        }
        
        .plan-label {
            color: #4a5568;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .plan-value {
            color: #2d3748;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .plan-value.exceeded {
            color: #e53e3e;
            background: #fed7d7;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        
        /* Addon Options Styles */
        .addon-options {
            margin-bottom: 1.5rem;
        }
        
        .addon-options h3 {
            color: #2d3748;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .addon-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        @media (min-width: 768px) {
            .addon-grid {
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
            }
        }
        
        .addon-option {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .addon-option:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }
        
        .addon-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }
        
        .addon-option h4 {
            color: #2d3748;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .addon-option p {
            color: #4a5568;
            font-size: 0.9rem;
            line-height: 1.4;
            margin-bottom: 1rem;
        }
        
        .addon-price {
            margin-bottom: 1rem;
        }
        
        .addon-price .price {
            display: block;
            color: #2d3748;
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .addon-price .price-note {
            color: #718096;
            font-size: 0.8rem;
        }
        
        .addon-button {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
        }
        
        .addon-button.primary {
            background: #667eea;
            color: white;
            border: 2px solid #667eea;
        }
        
        .addon-button.primary:hover {
            background: #5a67d8;
            border-color: #5a67d8;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
        }
        
        .addon-button.secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .addon-button.secondary:hover {
            background: #667eea;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
        }
        
        /* Quick Access Button Styles */
        .quick-access {
            margin-bottom: 1.5rem;
        }
        
        .quick-access-button {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .quick-access-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .quick-access-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        
        .quick-access-content {
            flex: 1;
            text-align: left;
        }
        
        .quick-access-title {
            display: block;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }
        
        .quick-access-subtitle {
            display: block;
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        .quick-access-arrow {
            font-size: 1.2rem;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .whatsapp-icon {
            width: 24px;
            height: 24px;
        }
        
        .contact-info {
            background: #f7fafc;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }
        
        .contact-info h3 {
            color: #2d3748;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        
        .contact-details {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            color: #4a5568;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .contact-item span:first-child {
            min-width: fit-content;
        }
        
        .logout-button {
            background: #e2e8f0;
            color: #4a5568;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
            width: 100%;
            max-width: 200px;
            text-align: center;
        }
        
        .logout-button:hover {
            background: #cbd5e0;
            color: #2d3748;
            text-decoration: none;
        }
        
        .features-blocked {
            background: #fed7d7;
            border: 1px solid #feb2b2;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
        }
        
        .features-blocked h4 {
            color: #c53030;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        
        .features-blocked p {
            color: #9b2c2c;
            font-size: 0.85rem;
            line-height: 1.4;
        }
        
        @media (max-width: 640px) {
            body {
                padding: 0.5rem;
            }
            
            .limit-container {
                padding: 1.5rem;
                margin: 0;
                border-radius: 16px;
            }
            
            .limit-icon {
                width: 60px;
                height: 60px;
                margin-bottom: 1rem;
            }
            
            .limit-icon svg {
                width: 30px;
                height: 30px;
            }
            
            h1 {
                font-size: 1.5rem;
                margin-bottom: 0.75rem;
            }
            
            .limit-message {
                font-size: 0.95rem;
                margin-bottom: 1.25rem;
            }
            
            .whatsapp-button {
                padding: 12px 20px;
                font-size: 0.95rem;
                margin-bottom: 1.25rem;
            }
            
            .contact-info {
                padding: 1rem;
                margin-bottom: 1.25rem;
            }
            
            .contact-info h3 {
                font-size: 1rem;
                margin-bottom: 0.5rem;
            }
            
            .contact-item {
                font-size: 0.9rem;
                text-align: center;
            }
            
            .features-blocked {
                padding: 0.75rem;
                margin-top: 1.25rem;
            }
            
            .features-blocked h4 {
                font-size: 0.95rem;
            }
            
            .features-blocked p {
                font-size: 0.8rem;
            }
            
            .logout-button {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
            
            /* Plan details mobile */
            .plan-details {
                padding: 1rem;
                margin-bottom: 1.25rem;
            }
            
            .plan-details h3 {
                font-size: 1.1rem;
                margin-bottom: 0.75rem;
            }
            
            .plan-item {
                padding: 0.4rem 0;
            }
            
            .plan-label {
                font-size: 0.85rem;
            }
            
            .plan-value {
                font-size: 0.85rem;
            }
            
            .plan-value.exceeded {
                font-size: 0.8rem;
                padding: 0.2rem 0.4rem;
            }
            
            /* Addon options mobile */
            .addon-options {
                margin-bottom: 1.25rem;
            }
            
            .addon-options h3 {
                font-size: 1.1rem;
                margin-bottom: 0.75rem;
            }
            
            .addon-option {
                padding: 1.25rem;
            }
            
            .addon-icon {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }
            
            .addon-option h4 {
                font-size: 1rem;
                margin-bottom: 0.4rem;
            }
            
            .addon-option p {
                font-size: 0.85rem;
                margin-bottom: 0.75rem;
            }
            
            .addon-price .price {
                font-size: 1.1rem;
                margin-bottom: 0.2rem;
            }
            
            .addon-price .price-note {
                font-size: 0.75rem;
            }
            
            .addon-button {
                padding: 10px 20px;
                font-size: 0.85rem;
            }
            
            /* Quick access mobile */
            .quick-access-button {
                padding: 0.75rem 1rem;
                gap: 0.75rem;
            }
            
            .quick-access-icon {
                font-size: 1.25rem;
            }
            
            .quick-access-title {
                font-size: 0.9rem;
            }
            
            .quick-access-subtitle {
                font-size: 0.8rem;
            }
            
            .quick-access-arrow {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .limit-container {
                padding: 1.25rem;
                border-radius: 12px;
            }
            
            .limit-icon {
                width: 50px;
                height: 50px;
            }
            
            .limit-icon svg {
                width: 25px;
                height: 25px;
            }
            
            h1 {
                font-size: 1.3rem;
            }
            
            .limit-message {
                font-size: 0.9rem;
            }
            
            .whatsapp-button {
                padding: 10px 16px;
                font-size: 0.9rem;
                gap: 8px;
            }
            
            .whatsapp-icon {
                width: 20px;
                height: 20px;
            }
            
            .contact-info {
                padding: 0.75rem;
            }
            
            .contact-item {
                font-size: 0.85rem;
            }
            
            .features-blocked {
                padding: 0.5rem;
            }
            
            .features-blocked h4 {
                font-size: 0.9rem;
            }
            
            .features-blocked p {
                font-size: 0.75rem;
            }
            
            /* Plan details mobile small */
            .plan-details {
                padding: 0.75rem;
            }
            
            .plan-details h3 {
                font-size: 1rem;
            }
            
            .plan-item {
                padding: 0.3rem 0;
            }
            
            .plan-label {
                font-size: 0.8rem;
            }
            
            .plan-value {
                font-size: 0.8rem;
            }
            
            .plan-value.exceeded {
                font-size: 0.75rem;
                padding: 0.15rem 0.3rem;
            }
            
            /* Addon options mobile small */
            .addon-option {
                padding: 1rem;
            }
            
            .addon-icon {
                font-size: 1.25rem;
            }
            
            .addon-option h4 {
                font-size: 0.95rem;
            }
            
            .addon-option p {
                font-size: 0.8rem;
            }
            
            .addon-price .price {
                font-size: 1rem;
            }
            
            .addon-price .price-note {
                font-size: 0.7rem;
            }
            
            .addon-button {
                padding: 8px 16px;
                font-size: 0.8rem;
            }
            
            /* Quick access mobile small */
            .quick-access-button {
                padding: 0.6rem 0.8rem;
                gap: 0.6rem;
            }
            
            .quick-access-icon {
                font-size: 1.1rem;
            }
            
            .quick-access-title {
                font-size: 0.85rem;
            }
            
            .quick-access-subtitle {
                font-size: 0.75rem;
            }
            
            .quick-access-arrow {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="limit-container">
        <div class="limit-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>
        
        <h1>Accommodation Limit Exceeded</h1>
        
        <p class="limit-message">
            You've exceeded the maximum number of accommodations allowed on your current plan. 
            Choose an option below to unlock additional accommodations and continue using the app.
        </p>
        
        <!-- Quick Access to Plans Page -->
        <div class="quick-access">
            <a href="{{ route('subscription.plans') }}" 
               class="quick-access-button">
                <span class="quick-access-icon">💳</span>
                <div class="quick-access-content">
                    <span class="quick-access-title">Buy Accommodations Online</span>
                    <span class="quick-access-subtitle">Add accommodations instantly at ₹99/month each</span>
                </div>
                <span class="quick-access-arrow">→</span>
            </a>
        </div>
        
        <!-- Plan Details Section -->
        <div class="plan-details">
            <h3>📊 Your Current Plan Details</h3>
            <div class="plan-info">
                <div class="plan-item">
                    <span class="plan-label">Plan:</span>
                    <span class="plan-value">{{ $planDetails['name'] }} Plan</span>
                </div>
                @if($planDetails['billing_cycle'])
                <div class="plan-item">
                    <span class="plan-label">Billing Cycle:</span>
                    <span class="plan-value">{{ ucfirst($planDetails['billing_cycle']) }}</span>
                </div>
                @endif
                <div class="plan-item">
                    <span class="plan-label">Properties Limit:</span>
                    <span class="plan-value">{{ $planDetails['properties_used'] }}/{{ $planDetails['properties_limit'] }}</span>
                </div>
                <div class="plan-item">
                    <span class="plan-label">Base Accommodations:</span>
                    <span class="plan-value">{{ $planDetails['base_accommodation_limit'] }}</span>
                </div>
                @if($planDetails['addon_count'] > 0)
                <div class="plan-item">
                    <span class="plan-label">Added Accommodations:</span>
                    <span class="plan-value">+{{ $planDetails['addon_count'] }}</span>
                </div>
                @endif
                <div class="plan-item">
                    <span class="plan-label">Total Accommodations:</span>
                    <span class="plan-value">{{ $planDetails['accommodations_allowed'] }}</span>
                </div>
                <div class="plan-item">
                    <span class="plan-label">Accommodations Used:</span>
                    <span class="plan-value exceeded">{{ $planDetails['accommodations_used'] }}</span>
                </div>
                <div class="plan-item">
                    <span class="plan-label">Exceeded By:</span>
                    <span class="plan-value exceeded">+{{ $planDetails['accommodations_exceeded'] }}</span>
                </div>
                @if($planDetails['subscription_ends_at'])
                <div class="plan-item">
                    <span class="plan-label">Subscription Ends:</span>
                    <span class="plan-value">{{ \Carbon\Carbon::parse($planDetails['subscription_ends_at'])->format('M d, Y') }}</span>
                </div>
                @endif
                @if($planDetails['active_subscription'])
                <div class="plan-item">
                    <span class="plan-label">Total Cost:</span>
                    <span class="plan-value">₹{{ number_format($planDetails['active_subscription']->total_subscription_amount, 0) }}/{{ $planDetails['active_subscription']->billing_interval }}</span>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Addon Options Section -->
        <div class="addon-options">
            <h3>🚀 Choose Your Solution</h3>
            <div class="addon-grid">
                <!-- Addon Accommodations Option -->
                <div class="addon-option">
                    <div class="addon-icon">➕</div>
                    <h4>Add Accommodations</h4>
                    <p>Add {{ $planDetails['accommodations_exceeded'] }} accommodation(s) as an addon to unlock your account</p>
                    <div class="addon-price">
                        <span class="price">₹{{ $planDetails['accommodations_exceeded'] * $planDetails['addon_price_per_month'] }}/month</span>
                        <span class="price-note">(₹{{ $planDetails['addon_price_per_month'] }} per accommodation/month)</span>
                    </div>
        <a href="https://wa.me/919400960223?text=Hi%2C%20I%20would%20like%20to%20add%20{{ $planDetails['accommodations_exceeded'] }}%20accommodation(s)%20as%20addon%20to%20my%20{{ $planDetails['name'] }}%20plan%20at%20₹{{ $planDetails['addon_price_per_month'] }}%20per%20accommodation%20per%20month." 
           target="_blank" 
           class="addon-button primary">
            📱 Contact WhatsApp
        </a>
        <a href="{{ route('subscription.plans') }}" 
           class="addon-button secondary mt-2">
            💳 Buy Addons Online
        </a>
                </div>
                
                <!-- Upgrade Plan Option -->
                <div class="addon-option">
                    <div class="addon-icon">⬆️</div>
                    <h4>Upgrade Plan</h4>
                    @if($planDetails['name'] === 'Trial' || $planDetails['name'] === 'Starter')
                        <p>Upgrade to Professional plan for 15 accommodations and advanced features</p>
                        <div class="addon-price">
                            <span class="price">₹999/month</span>
                            <span class="price-note">or ₹9,990/year (17% savings)</span>
                        </div>
                    @else
                        <p>Contact us for enterprise plans with unlimited accommodations</p>
                        <div class="addon-price">
                            <span class="price">Contact for Pricing</span>
                            <span class="price-note">Custom solutions available</span>
                        </div>
                    @endif
                    <a href="https://wa.me/919400960223?text=Hi%2C%20I%20would%20like%20to%20upgrade%20my%20{{ $planDetails['name'] }}%20plan%20to%20a%20higher%20tier%20with%20more%20accommodations.%20I%20currently%20have%20{{ $planDetails['accommodations_used'] }}%20accommodations%20and%20need%20more." 
                       target="_blank" 
                       class="addon-button secondary">
                        📱 Contact WhatsApp
                    </a>
                    <a href="{{ route('subscription.plans') }}" 
                       class="addon-button primary mt-2">
                        💳 View Plans Online
                    </a>
                </div>
            </div>
        </div>
        
        <div class="contact-info">
            <h3>📞 Contact Information</h3>
            <div class="contact-details">
                <div class="contact-item">
                    <span>📱 WhatsApp:</span>
                    <a href="https://wa.me/919400960223" target="_blank" style="color: #25d366; font-weight: 600; text-decoration: none;">+91 9400960223</a>
                </div>
                <div class="contact-item">
                    <span>💬 Message:</span>
                    <span style="word-break: break-word;">"I need to upgrade my subscription to add more accommodations"</span>
                </div>
                <div class="contact-item">
                    <span>💳 Online:</span>
                    <a href="{{ route('subscription.plans') }}" style="color: #667eea; font-weight: 600; text-decoration: none;">Buy Addons Instantly</a>
                </div>
            </div>
        </div>
        
        <div class="features-blocked">
            <h4>🚫 Features Currently Blocked:</h4>
            <p>• Adding new accommodations<br>
               • Creating new properties<br>
               • Accessing advanced features<br>
               • Full app functionality</p>
        </div>
        
        <a href="{{ route('logout') }}" class="logout-button">
            Logout
        </a>
    </div>
</body>
</html>
