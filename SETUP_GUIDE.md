# 🚀 DrOutlier Subscription System - Quick Setup Guide

## Prerequisites Checklist
- [ ] PHP 8.1 or higher
- [ ] Laravel 10.x
- [ ] MySQL Database
- [ ] Composer installed
- [ ] Node.js and npm installed (for Next.js frontend)
- [ ] Razorpay account (get from https://razorpay.com)

---

## 📦 Step 1: Backend Setup (Laravel)

### 1.1 Run Database Migrations

Navigate to your Laravel application directory:

```bash
cd admin/application
php artisan migrate
```

This will create the following tables:
- `modules` (with pre-seeded data)
- `plans`
- `plan_modules`
- `user_subscriptions`

### 1.2 Configure Razorpay

Add these variables to your `.env` file:

```env
RAZORPAY_KEY=rzp_test_xxxxxxxxxxxxx
RAZORPAY_SECRET=your_secret_key_here
RAZORPAY_WEBHOOK_SECRET=your_webhook_secret
RAZORPAY_CURRENCY=INR
RAZORPAY_PAYMENT_CAPTURE=1
```

**How to get Razorpay credentials:**
1. Go to https://dashboard.razorpay.com/
2. Sign up/Login
3. Navigate to Settings → API Keys
4. Generate Test/Live API keys
5. Copy Key ID and Secret

### 1.3 Clear Laravel Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan optimize
```

---

## 🎨 Step 2: Admin Panel Setup

### 2.1 Access Admin Panel

Navigate to: `https://admin.droutlier.com/admin/login`

### 2.2 Create Your First Plan

1. Go to **Admin Dashboard** → **Plans** → **Create Plan**
2. Fill in the details:
   - **Name**: e.g., "Spotters Premium"
   - **Slug**: e.g., "spotters-premium"
   - **Description**: Brief description
   - **Price**: e.g., 299
   - **Discount Price**: (optional) e.g., 199
   - **Duration**: e.g., 1 Month
   - **Modules**: Select "Spotters" checkbox
   - **Status**: Active
3. Click **Create**

### 2.3 Create Plans for All Modules

Create separate plans or combo plans:

**Example Plans:**

1. **Spotters Only** - ₹299/month
   - Access to: Spotters

2. **OSCE Essentials** - ₹499/month
   - Access to: OSCE

3. **Complete Package** - ₹999/month
   - Access to: All modules

### 2.4 Manage Modules (Optional)

Modules are auto-created, but you can:
1. Go to **Modules Manager**
2. Edit module details
3. Update icons, descriptions
4. Add new modules for future content

---

## 🌐 Step 3: Frontend Integration (Next.js)

### 3.1 Install Dependencies

Make sure Razorpay SDK is loaded. Add to your `public/index.html` or use dynamic script loading:

```html
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
```

### 3.2 Create Pricing Page

A pricing page has been created at `src/app/pricing/page.js`. You can customize it or use it as-is.

**Access**: https://www.droutlier.com/pricing

### 3.3 Protect Module Pages

For each module page (spotters, osce, notes, etc.), wrap the content with `ModuleAccessGuard`:

**Example for Spotters:**

```javascript
// src/app/spotters/page.js
import ModuleAccessGuard from '@/hooks/useSubscription';

export default function SpottersPage() {
  return (
    <ModuleAccessGuard moduleSlug="spotters">
      {/* Your existing spotters content */}
    </ModuleAccessGuard>
  );
}
```

**Repeat for all modules:**
- `/notes` → `moduleSlug="notes"`
- `/osce` → `moduleSlug="osce"`
- `/ai-rad` → `moduleSlug="ai-rad"`
- `/practical-essentials` → `moduleSlug="practical-essentials"`
- `/watch-and-learn` → `moduleSlug="watch-and-learn"`
- `/quizora` → `moduleSlug="quizora"`

### 3.4 Update Existing Pages

See `EXAMPLE_PROTECTED_PAGE.js` for a complete example of how to protect the practical-essentials page.

Apply the same pattern to:
- `src/app/spotters/page.js`
- `src/app/osce/page.js`
- `src/app/notes/page.js`
- `src/app/ai-rad/page.js`
- `src/app/watch-and-learn/page.js`
- `src/app/quizora/page.js`

---

## 🧪 Step 4: Testing

### 4.1 Test Payment Flow

1. **View Plans**
   - Go to https://www.droutlier.com/pricing
   - Verify all plans are displayed with correct pricing

2. **Purchase a Plan**
   - Click "Subscribe Now" on any plan
   - Razorpay payment modal should open
   - Use Razorpay test card:
     - Card Number: `4111 1111 1111 1111`
     - CVV: Any 3 digits
     - Expiry: Any future date
   - Complete payment

3. **Verify Subscription**
   - Check Admin Panel → Subscriptions
   - Your subscription should appear as "Active"
   - User should now have access to selected modules

### 4.2 Test Access Control

1. **With Subscription**
   - Navigate to a module you purchased (e.g., /spotters)
   - Content should load normally

2. **Without Subscription**
   - Create/use a user without subscription
   - Try accessing /spotters
   - Should redirect to /pricing with message

3. **Module Not in Plan**
   - Purchase "Spotters Only" plan
   - Try accessing /osce
   - Should show "Subscription Required" message

### 4.3 Test Admin Functions

1. **Manual Subscription**
   - Go to Admin → Subscriptions → Create
   - Assign a plan to a user manually
   - Verify user gets access

2. **Extend Subscription**
   - Select an active subscription
   - Click "Extend"
   - Add days (e.g., 30)
   - Verify expiry date updated

3. **Cancel Subscription**
   - Select an active subscription
   - Click "Cancel"
   - Verify status changes to "Cancelled"
   - User should lose access

---

## 🔐 Step 5: Security Checklist

- [ ] Razorpay keys are in `.env` and not committed to git
- [ ] `.env` file is in `.gitignore`
- [ ] HTTPS is enabled on production
- [ ] CORS is properly configured
- [ ] API rate limiting is enabled
- [ ] Payment verification is done server-side only

---

## 📋 Step 6: Production Deployment

### 6.1 Switch to Live Razorpay Keys

1. In Razorpay Dashboard, switch to "Live Mode"
2. Generate Live API keys
3. Update `.env`:
   ```env
   RAZORPAY_KEY=rzp_live_xxxxxxxxxxxxx
   RAZORPAY_SECRET=your_live_secret_key
   ```

### 6.2 Set Up Cron Job for Expiry Check

Add to your server's crontab:

```bash
* * * * * cd /path/to/admin/application && php artisan schedule:run >> /dev/null 2>&1
```

Then add this to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        \App\Models\UserSubscription::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);
    })->daily();
}
```

### 6.3 Set Up Razorpay Webhooks (Optional but Recommended)

1. Go to Razorpay Dashboard → Webhooks
2. Add webhook URL: `https://admin.droutlier.com/api/razorpay/webhook`
3. Select events: `payment.captured`, `payment.failed`
4. Save webhook secret to `.env`

---

## 🎯 Step 7: User Flow

### For New Users

1. **Sign Up** → User creates account
2. **Browse Content** → Locked content shows "Subscribe" message
3. **View Plans** → Navigate to /pricing
4. **Select Plan** → Choose based on modules needed
5. **Payment** → Complete Razorpay payment
6. **Access Granted** → Immediately access subscribed modules

### For Existing Subscribers

1. **Check Subscription** → View active plan in profile
2. **Access Content** → Browse all included modules
3. **Expiry Reminder** → Get notified before expiry (implement email)
4. **Renew/Upgrade** → Purchase new plan when needed

---

## 📞 Troubleshooting

### Issue: Migrations fail

**Solution:**
```bash
php artisan migrate:fresh
```
⚠️ Warning: This will delete all data!

### Issue: Module access always denied

**Check:**
1. User has active subscription: `SELECT * FROM user_subscriptions WHERE user_id = X AND status = 'active'`
2. Plan has modules mapped: `SELECT * FROM plan_modules WHERE plan_id = Y`
3. Module slug matches exactly in middleware

### Issue: Razorpay payment not working

**Check:**
1. API keys are correct in `.env`
2. Razorpay script is loaded on page
3. Browser console for JavaScript errors
4. Network tab for API call failures

### Issue: Access guard not working on frontend

**Check:**
1. `useSubscription` hook is imported correctly
2. Bearer token is being sent with API calls
3. Module slug matches database exactly (case-sensitive)

---

## 📚 Additional Resources

- **Full Documentation**: `SUBSCRIPTION_SYSTEM_DOCUMENTATION.md`
- **Razorpay Docs**: https://razorpay.com/docs/
- **Laravel Docs**: https://laravel.com/docs

---

## ✅ Final Checklist

- [ ] Migrations run successfully
- [ ] Modules table has 7 pre-seeded modules
- [ ] At least 1 plan created in admin
- [ ] Razorpay keys configured
- [ ] Test payment completed successfully
- [ ] Module access protection working
- [ ] Admin can manage subscriptions
- [ ] Frontend pricing page displays correctly
- [ ] All existing features still work
- [ ] Production deployment planned

---

## 🎉 You're All Set!

Your subscription system is now ready to use. Users can purchase plans, access content based on their subscriptions, and you have full control through the admin panel.

**Need Help?** Check the full documentation in `SUBSCRIPTION_SYSTEM_DOCUMENTATION.md`
