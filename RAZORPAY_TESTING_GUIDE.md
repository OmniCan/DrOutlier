# Razorpay Testing Guide for DrOutlier

## Test Mode vs Live Mode

### Test Mode (Development)
Use these credentials in `.env`:
```env
RAZORPAY_KEY=rzp_test_xxxxxxxxxxxxx
RAZORPAY_SECRET=xxxxxxxxxxxxxxxxxxxxx
```

### Live Mode (Production)
Use these credentials in `.env`:
```env
RAZORPAY_KEY=rzp_live_xxxxxxxxxxxxx
RAZORPAY_SECRET=xxxxxxxxxxxxxxxxxxxxx
```

---

## Test Card Numbers

### Successful Payments

| Card Number | CVV | Expiry | Result |
|-------------|-----|--------|--------|
| 4111 1111 1111 1111 | Any | Future | Success |
| 5555 5555 5555 4444 | Any | Future | Success |
| 3566 0020 2036 0505 | Any | Future | Success |

### Failed Payments

| Card Number | CVV | Expiry | Result |
|-------------|-----|--------|--------|
| 4000 0000 0000 0002 | Any | Future | Card Declined |
| 4000 0000 0000 0101 | Any | Future | Insufficient Funds |

### Test UPI

- UPI ID: `success@razorpay`
- Result: Payment Success

---

## Testing Steps

### 1. Test Successful Payment Flow

```
1. Go to https://www.droutlier.com/pricing
2. Login with test account
3. Select any plan
4. Click "Subscribe Now"
5. In Razorpay modal:
   - Select "Card"
   - Card Number: 4111 1111 1111 1111
   - CVV: 123
   - Expiry: 12/25
   - Name: Test User
6. Click "Pay"
7. Wait for success message
8. Check subscription activated
```

### 2. Test Failed Payment

```
1. Follow same steps as above
2. Use card: 4000 0000 0000 0002
3. Payment should fail
4. Error message should appear
5. Subscription should remain pending
```

### 3. Test Payment Cancellation

```
1. Start payment flow
2. Close Razorpay modal before paying
3. Error message: "Payment cancelled"
4. No subscription created
```

---

## Verification Checklist

After successful test payment, verify:

- [ ] User's subscription status shows "Active"
- [ ] `user_subscriptions` table has new record with status = 'active'
- [ ] `started_at` and `expires_at` dates are correct
- [ ] Payment details stored in `payment_details` column
- [ ] User can access modules included in plan
- [ ] Razorpay dashboard shows payment
- [ ] Navigation shows only accessible modules

---

## Database Verification Queries

```sql
-- Check user's active subscription
SELECT us.*, p.name as plan_name
FROM user_subscriptions us
JOIN plans p ON us.plan_id = p.id
WHERE us.user_id = YOUR_USER_ID
AND us.status = 'active'
ORDER BY us.created_at DESC;

-- Check all subscriptions for a user
SELECT 
    us.id,
    p.name as plan_name,
    us.amount_paid,
    us.status,
    us.started_at,
    us.expires_at,
    us.created_at
FROM user_subscriptions us
JOIN plans p ON us.plan_id = p.id
WHERE us.user_id = YOUR_USER_ID
ORDER BY us.created_at DESC;

-- Check plan modules
SELECT p.name as plan_name, m.display_name as module_name
FROM plans p
JOIN plan_modules pm ON p.id = pm.plan_id
JOIN modules m ON pm.module_id = m.id
WHERE p.id = YOUR_PLAN_ID;

-- Check payment details
SELECT 
    id,
    plan_id,
    amount_paid,
    razorpay_order_id,
    razorpay_payment_id,
    status,
    payment_details
FROM user_subscriptions
WHERE razorpay_payment_id IS NOT NULL
ORDER BY created_at DESC
LIMIT 10;
```

---

## Webhook Configuration (Optional)

For automatic payment status updates, configure webhook in Razorpay dashboard:

### Webhook URL:
```
https://admin.droutlier.com/api/razorpay/webhook
```

### Events to Subscribe:
- `payment.authorized`
- `payment.failed`
- `payment.captured`

### Webhook Secret:
Store in `.env`:
```env
RAZORPAY_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxx
```

---

## Common Test Scenarios

### Scenario 1: First Time User
```
User: New registered user
Plan: Basic Plan (₹499/month)
Expected: 
- Creates new subscription
- Status: Active
- Expires: 1 month from now
- Access to: Notes + Spotters modules
```

### Scenario 2: Upgrade Plan
```
User: Has active Basic Plan
Action: Purchase Premium Plan
Expected:
- Old subscription still active
- New subscription created
- User has access to all modules from both plans
- Can implement logic to auto-cancel old plan
```

### Scenario 3: Expired Subscription Renewal
```
User: Had subscription that expired
Action: Purchase same/different plan
Expected:
- New subscription created
- Status: Active
- Modules accessible again
```

### Scenario 4: Multiple Concurrent Subscriptions
```
User: Has active subscription
Action: Purchases different plan
Expected:
- Both subscriptions active
- User has combined module access
- Or implement logic to prevent this
```

---

## Error Codes Reference

| Code | Message | Action |
|------|---------|--------|
| `BAD_REQUEST_ERROR` | Invalid payment details | Check card details |
| `GATEWAY_ERROR` | Payment gateway error | Retry payment |
| `SERVER_ERROR` | Server error | Contact support |
| `AUTHENTICATION_ERROR` | Invalid API keys | Check `.env` credentials |

---

## Testing Mobile Payments

### Test Wallets:
- Paytm: Use test numbers from Razorpay docs
- PhonePe: Use test UPI ID
- Google Pay: Use test UPI ID

### Test Net Banking:
- Bank: HDFC (Test)
- Username: `razorpay`
- Password: `razorpay`

---

## Production Deployment Checklist

Before going live:

- [ ] Switch to live Razorpay keys
- [ ] Test live payment with small amount
- [ ] Configure webhook URL
- [ ] Update payment gateway in Razorpay dashboard
- [ ] Set up payment alerts/notifications
- [ ] Test email notifications (if configured)
- [ ] Verify SSL certificate is valid
- [ ] Test from different devices
- [ ] Test from different networks
- [ ] Prepare refund policy
- [ ] Train support team

---

## Support & Documentation

### Razorpay Documentation:
- [Payment Gateway](https://razorpay.com/docs/payments/)
- [Test Cards](https://razorpay.com/docs/payments/payments/test-card-details/)
- [Webhooks](https://razorpay.com/docs/webhooks/)
- [API Reference](https://razorpay.com/docs/api/)

### DrOutlier Implementation:
- See `PRICING_PAGE_SETUP.md` for setup guide
- See `PRICING_IMPLEMENTATION_SUMMARY.md` for overview
- Check `SubscriptionController.php` for backend logic

---

## Quick Debug Commands

### Laravel Logs:
```bash
# View latest logs
tail -f admin/application/storage/logs/laravel.log

# Search for Razorpay errors
grep -i "razorpay" admin/application/storage/logs/laravel.log
```

### Browser Console:
```javascript
// Check if Razorpay script loaded
console.log(window.Razorpay);

// Check user token
console.log(Cookies.get('user-token'));

// Check API response
// Open Network tab and filter "subscription"
```

---

## Contact & Support

For Razorpay issues:
- Dashboard: https://dashboard.razorpay.com/
- Support: support@razorpay.com
- Phone: 1800-102-7000

For DrOutlier implementation:
- Check Laravel logs
- Check browser console
- Review API responses
- Verify database records
