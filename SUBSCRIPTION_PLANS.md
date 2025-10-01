# Stay loops - Subscription Plans & Cashfree Integration

## Overview

Stay loops offers a comprehensive hospitality management platform with flexible subscription plans designed to scale with your business needs. The platform integrates with Cashfree payment gateway for secure and reliable subscription billing.

## Subscription Plans

### 1. Trial Plan (Free)
- **Duration**: 15 days
- **Price**: Free
- **Features**: Full access to Professional plan features
- **Limits**: 
  - Up to 5 properties
  - Up to 15 accommodations
  - All advanced features included
- **Purpose**: Allow users to test the platform before committing to a paid plan

### 2. Starter Plan
- **Price**: ₹299/month (Special offer from ₹2,999)
- **Target**: Single property management
- **Features**:
  - 1 property only
  - 3 accommodations
  - Unlimited bookings
  - Customer management
  - Basic pricing management
  - Image uploads available
  - Email support
- **Limitations**:
  - No B2B partner management
  - Basic reports only

### 3. Professional Plan
- **Price**: ₹999/month (Special offer from ₹9,999)
- **Target**: Growing hospitality businesses
- **Features**:
  - Up to 5 properties
  - 15 accommodations
  - Unlimited bookings
  - Advanced customer management
  - Dynamic pricing & calendar
  - Unlimited image uploads
  - B2B partner management
  - Advanced reports & analytics
  - Priority support
  - New features early access
- **Most Popular**: Recommended for most businesses

### 4. Additional Accommodations
- **Price**: ₹99/month per accommodation
- **Purpose**: Add extra accommodations beyond plan limits
- **Features**:
  - Additional accommodation slots
  - Same features as your current plan
  - Cancel anytime
- **Maximum**: Up to 50 additional accommodations

## Billing Options

### Monthly Billing
- Standard monthly subscription
- Automatic renewal
- Cancel anytime

### Yearly Billing
- 12 months upfront payment
- **Discount**: 2 months free (equivalent to ₹1,998 savings)
- Better value for long-term users

## Cashfree Payment Integration

### Configuration
The platform uses Cashfree payment gateway for secure subscription processing:

```php
// Configuration in config/cashfree.php
'plans' => [
    'starter' => [
        'name' => 'Starter Plan',
        'amount' => 299, // Amount in rupees (₹299)
        'currency' => 'INR',
        'interval' => 'month',
        'interval_count' => 1,
    ],
    'professional' => [
        'name' => 'Professional Plan',
        'amount' => 999, // Amount in rupees (₹999)
        'currency' => 'INR',
        'interval' => 'month',
        'interval_count' => 1,
    ],
    'additional_accommodation' => [
        'name' => 'Additional Accommodation',
        'amount' => 99, // Amount in rupees (₹99)
        'currency' => 'INR',
        'interval' => 'month',
        'interval_count' => 1,
    ]
]
```

### Payment Flow

1. **Order Creation**: User selects a plan and initiates payment
2. **Cashfree Integration**: System creates payment order via Cashfree API
3. **Payment Processing**: User completes payment on Cashfree's secure platform
4. **Webhook Verification**: Cashfree sends webhook notification for payment status
5. **Subscription Activation**: System activates user's subscription upon successful payment

### Key Features

- **Secure Payment Processing**: All payments handled by Cashfree's PCI DSS compliant platform
- **Multiple Payment Methods**: Credit cards, debit cards, UPI, net banking, wallets
- **Webhook Integration**: Real-time payment status updates
- **Order Tracking**: Unique order IDs for each transaction
- **Error Handling**: Comprehensive error handling and logging
- **Test Mode**: Sandbox environment for testing

### API Endpoints

- **Create Order**: `POST /cashfree/create-order`
- **Success Callback**: `GET /cashfree/success`
- **Webhook Handler**: `POST /cashfree/webhook`

### Webhook Events

- `PAYMENT_SUCCESS_WEBHOOK`: Payment completed successfully
- `PAYMENT_FAILED_WEBHOOK`: Payment failed
- `PAYMENT_USER_DROPPED_WEBHOOK`: User abandoned payment

## Subscription Management

### User Limits by Plan

| Feature | Trial | Starter | Professional |
|---------|-------|---------|--------------|
| Properties | 5 | 1 | 5 |
| Accommodations | 15 | 3 | 15 |
| B2B Partners | ✅ | ❌ | ✅ |
| Advanced Analytics | ✅ | ❌ | ✅ |
| Priority Support | ✅ | ❌ | ✅ |
| Dynamic Pricing | ✅ | ❌ | ✅ |

### Usage Tracking

The system tracks usage in real-time:
- Properties used vs. maximum allowed
- Accommodations used vs. maximum allowed
- Visual progress bars in dashboard
- Automatic enforcement of limits

### Subscription Status

- **Active**: Subscription is active and within limits
- **Trial**: Free trial period (15 days)
- **Expired**: Subscription has expired
- **Pending**: Payment processing or admin approval required

## Admin Features

### Subscription Requests
- Users can request subscription upgrades
- Admin approval required for activation
- Flexible duration setting (1-12 months)
- Referral reward processing for 3+ month subscriptions

### User Management
- View all active subscriptions
- Modify subscription status
- Extend subscription periods
- Process cancellations

### Financial Tracking
- Revenue tracking by plan
- Commission management for B2B partners
- Referral reward system
- Withdrawal requests handling

## Security & Compliance

### Payment Security
- PCI DSS compliant payment processing
- Encrypted data transmission
- Secure webhook signature verification
- No sensitive payment data stored locally

### Data Protection
- User data encryption
- Secure API endpoints
- Role-based access control
- Audit logging for all transactions

## Support & Documentation

### Customer Support
- **Starter Plan**: Email support
- **Professional Plan**: Priority support
- **Trial Users**: Full support during trial period

### Documentation
- API documentation
- Integration guides
- Troubleshooting guides
- Video tutorials

## Pricing Strategy

### Special Offers
- **Starter Plan**: 90% discount (₹299 vs ₹2,999)
- **Professional Plan**: 90% discount (₹999 vs ₹9,999)
- **Yearly Billing**: 2 months free

### Value Proposition
- **Cost-effective**: Competitive pricing in the market
- **Scalable**: Easy upgrade path as business grows
- **Feature-rich**: Comprehensive hospitality management
- **Reliable**: 99.9% uptime guarantee

## Technical Implementation

### Database Schema
- `users` table: Subscription status and limits
- `subscription_requests` table: Pending subscription requests
- `commissions` table: B2B partner commissions
- `referrals` table: Referral tracking and rewards

### Middleware
- `CheckSubscription`: Enforces subscription limits
- Role-based access control
- Feature gating based on subscription level

### Controllers
- `SubscriptionController`: Plan selection and management
- `CashfreeController`: Payment processing
- `AdminController`: Admin subscription management

## Future Enhancements

### Planned Features
- **Enterprise Plan**: For large hotel chains
- **White-label Solution**: Custom branding options
- **API Access**: Third-party integrations
- **Advanced Analytics**: Business intelligence dashboard
- **Mobile App**: Native mobile applications

### Payment Improvements
- **Auto-renewal**: Seamless subscription renewal
- **Proration**: Mid-cycle plan changes
- **Multiple Currencies**: International expansion
- **Invoice Generation**: Automated billing

## Conclusion

Stay loops provides a comprehensive subscription model that scales with business needs, backed by secure Cashfree payment integration. The platform offers excellent value with special pricing, robust features, and reliable payment processing.

For technical support or subscription inquiries, contact our support team or refer to the documentation portal.
