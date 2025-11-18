# 🚀 Pricing Page Deployment Checklist

## Pre-Deployment Setup

### 1. Database Setup ✅
- [ ] Run `ADD_VISIBILITY_TYPE_TO_NAVIGATION.sql` on production database
- [ ] Run `ADD_PRICING_NAVIGATION_ITEM.sql` on production database
- [ ] Verify migrations applied successfully:
  ```sql
  SELECT * FROM navigation_items WHERE url = '/pricing';
  DESCRIBE navigation_items; -- Check for visibility_type column
  ```

### 2. Environment Configuration ✅
- [ ] Add Razorpay credentials to `.env`:
  ```env
  RAZORPAY_KEY=rzp_test_xxxxxxxxxxxxx  # Use rzp_live_ for production
  RAZORPAY_SECRET=xxxxxxxxxxxxxxxxxxxxx
  ```
- [ ] Verify credentials work by testing in Razorpay dashboard
- [ ] Ensure database connection is configured

### 3. Admin Panel Setup ✅
- [ ] Login to admin panel: `https://admin.droutlier.com/admin`
- [ ] Navigate to **Subscription Management** → **Modules**
- [ ] Verify all modules exist (Notes, Spotters, OSCE, AI-Rad, etc.)
- [ ] Navigate to **Subscription Management** → **Plans**
- [ ] Create at least 2-3 plans:

**Example Plan 1 - Basic:**
```
Name: Basic Plan
Description: Perfect for beginners
Price: ₹499
Discount: ₹0
Duration: 1 Month
Modules: Notes, Spotters
Featured: No
Features:
- Access to 2 modules
- 1000+ study materials
- Mobile access
```

**Example Plan 2 - Premium:**
```
Name: Premium Plan
Description: Complete exam preparation
Price: ₹1999
Discount: ₹500 (Optional)
Duration: 3 Months
Modules: All modules
Featured: Yes
Features:
- Access to all modules
- 5000+ study materials
- Priority support
- Unlimited practice tests
```

### 4. Navigation Setup ✅
- [ ] Go to **Navigation Manager** in admin panel
- [ ] Verify "Plans" navigation item exists
- [ ] Or create manually:
  - Title: Plans
  - URL: /pricing
  - Icon: fas fa-tags
  - Type: Custom
  - Visibility: Public
  - Active: Yes
- [ ] Drag to desired position in navigation
- [ ] Save changes

---

## Testing Phase

### 5. Frontend Testing ✅
- [ ] Visit `https://www.droutlier.com/pricing`
- [ ] Page loads without errors
- [ ] All plans display correctly
- [ ] Pricing shows properly
- [ ] Module lists appear
- [ ] Features display
- [ ] Featured badge shows on premium plan
- [ ] Responsive design works (test mobile/tablet)

### 6. Authentication Testing ✅
- [ ] Logout and visit `/pricing`
- [ ] Login prompt appears
- [ ] Login works correctly
- [ ] After login, plans are visible
- [ ] Logout works correctly

### 7. Payment Testing (TEST MODE) ✅
- [ ] Ensure using `rzp_test_` credentials
- [ ] Login with test user account
- [ ] Select a plan
- [ ] Click "Subscribe Now"
- [ ] Razorpay modal opens
- [ ] Use test card: `4111 1111 1111 1111`
- [ ] CVV: Any 3 digits (e.g., 123)
- [ ] Expiry: Any future date (e.g., 12/25)
- [ ] Complete payment
- [ ] Success toast appears
- [ ] Page reloads

### 8. Subscription Verification ✅
- [ ] Visit `/subscription` page
- [ ] Subscription status shows "Active"
- [ ] Plan name displays correctly
- [ ] Days remaining shows
- [ ] Module list appears
- [ ] Dates are correct (started/expires)

### 9. Module Access Testing ✅
- [ ] Try accessing modules included in plan
- [ ] Modules should be accessible
- [ ] Try accessing modules NOT in plan
- [ ] Should show upgrade prompt
- [ ] Navigation only shows accessible modules (if visibility = subscription)

### 10. Database Verification ✅
```sql
-- Check subscription created
SELECT * FROM user_subscriptions 
WHERE user_id = YOUR_TEST_USER_ID 
ORDER BY created_at DESC 
LIMIT 1;

-- Verify payment details stored
SELECT 
  id, 
  plan_id, 
  amount_paid, 
  razorpay_payment_id, 
  status, 
  started_at, 
  expires_at
FROM user_subscriptions
WHERE razorpay_payment_id IS NOT NULL
ORDER BY created_at DESC
LIMIT 5;
```

---

## Production Deployment

### 11. Switch to Live Mode ✅
- [ ] Update `.env` with live Razorpay keys:
  ```env
  RAZORPAY_KEY=rzp_live_xxxxxxxxxxxxx
  RAZORPAY_SECRET=xxxxxxxxxxxxxxxxxxxxx
  ```
- [ ] Clear Laravel config cache:
  ```bash
  cd admin/application
  php artisan config:clear
  php artisan cache:clear
  ```
- [ ] Test with small real payment (₹1 or ₹10)
- [ ] Verify payment in Razorpay dashboard
- [ ] Refund test payment if needed

### 12. Production Testing ✅
- [ ] Create real plans with actual pricing
- [ ] Disable/delete test plans
- [ ] Test complete flow with real credentials
- [ ] Verify email notifications (if configured)
- [ ] Test from different devices
- [ ] Test from different browsers
- [ ] Test mobile app (if applicable)

---

## Post-Deployment

### 13. Monitoring Setup ✅
- [ ] Monitor Laravel logs:
  ```bash
  tail -f admin/application/storage/logs/laravel.log
  ```
- [ ] Monitor Razorpay dashboard for payments
- [ ] Set up payment alerts in Razorpay
- [ ] Configure webhook (optional):
  - URL: `https://admin.droutlier.com/api/razorpay/webhook`
  - Events: payment.captured, payment.failed

### 14. Documentation ✅
- [ ] Share `PRICING_PAGE_SETUP.md` with team
- [ ] Share `RAZORPAY_TESTING_GUIDE.md` with team
- [ ] Train support team on:
  - How to view subscriptions in admin
  - How to manually activate/deactivate subscriptions
  - How to handle refund requests
  - How to check payment status

### 15. User Communication ✅
- [ ] Announce new pricing page to users
- [ ] Send email about new subscription plans
- [ ] Update website footer with pricing link
- [ ] Update FAQ/Help section
- [ ] Prepare support articles for common questions

---

## Optional Enhancements

### 16. Future Features (Optional) ⭐
- [ ] Add coupon code system
- [ ] Implement plan comparison table
- [ ] Add testimonials section
- [ ] Create referral program
- [ ] Add auto-renewal option
- [ ] Implement plan upgrade/downgrade
- [ ] Add invoice generation
- [ ] Email notifications for:
  - Successful payment
  - Subscription expiry warnings
  - Renewal reminders
- [ ] SMS notifications via Razorpay
- [ ] Analytics dashboard for subscriptions

---

## Rollback Plan

### If Issues Occur:
1. **Payment not working:**
   - Revert to test mode credentials
   - Check Razorpay dashboard for errors
   - Review Laravel logs

2. **Plans not showing:**
   - Verify plans are active in database
   - Check API endpoint response
   - Clear browser cache

3. **Navigation broken:**
   - Remove navigation item temporarily
   - Users can still access via direct URL: `/pricing`

4. **Database issues:**
   - Have database backup ready
   - Can rollback migrations if needed

---

## Success Criteria

### Page is Ready for Production When:
- ✅ All plans display correctly
- ✅ Payment flow works end-to-end
- ✅ Subscriptions activate properly
- ✅ Module access control works
- ✅ Navigation shows pricing link
- ✅ Responsive design works on all devices
- ✅ No console errors
- ✅ No database errors
- ✅ Razorpay live mode tested
- ✅ Team trained on system
- ✅ Documentation complete

---

## Quick Reference

### Important URLs:
- Pricing Page: `https://www.droutlier.com/pricing`
- Subscription Dashboard: `https://www.droutlier.com/subscription`
- Admin Panel: `https://admin.droutlier.com/admin`
- Razorpay Dashboard: `https://dashboard.razorpay.com`

### Important Files:
- Frontend Pricing: `src/app/pricing/page.js`
- Subscription Dashboard: `src/app/subscription/page.js`
- Subscription Component: `src/components/SubscriptionStatus.js`
- Backend Controller: `admin/application/app/Http/Controllers/Api/SubscriptionController.php`
- API Routes: `admin/application/routes/api.php`

### Database Tables:
- `plans` - Subscription plans
- `modules` - Content modules
- `plan_modules` - Plan-module relationships
- `user_subscriptions` - User subscriptions
- `navigation_items` - Navigation menu items

### Support Contacts:
- Razorpay Support: support@razorpay.com | 1800-102-7000
- Razorpay Dashboard: https://dashboard.razorpay.com/app/dashboard

---

## Sign-off

- [ ] Development Team Lead
- [ ] QA Team Lead
- [ ] Product Manager
- [ ] DevOps Engineer

**Deployment Date:** _______________

**Deployed By:** _______________

**Notes:** _______________________________________________________________

---

## Emergency Contacts

In case of critical issues:
1. Check Laravel logs immediately
2. Check Razorpay dashboard
3. Review database for failed transactions
4. Have database admin on standby
5. Keep this checklist handy

**Everything is ready to go live! 🚀**
