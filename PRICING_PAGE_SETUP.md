# Pricing/Plans Page Setup Guide

## Overview
The pricing page allows users to view and purchase subscription plans for DrOutlier. It's integrated with Razorpay for payment processing.

## Page Location
- **URL**: `https://www.droutlier.com/pricing`
- **File**: `src/app/pricing/page.js`

## Features
✅ Display all active subscription plans with pricing
✅ Show plan modules and features
✅ Highlight featured plans (most popular)
✅ Razorpay payment integration
✅ Responsive design matching DrOutlier theme
✅ Login prompt for non-authenticated users
✅ Real-time payment verification
✅ Toast notifications for success/error messages

## Setup Instructions

### 1. Run Database Migrations

Execute the following SQL files on your production database:

```sql
-- Add visibility_type column to navigation_items table
-- File: admin/application/database/migrations/ADD_VISIBILITY_TYPE_TO_NAVIGATION.sql

-- Add Pricing link to navigation
-- File: admin/application/database/migrations/ADD_PRICING_NAVIGATION_ITEM.sql
```

### 2. Configure Razorpay Keys

Ensure your `.env` file has the Razorpay credentials:

```env
RAZORPAY_KEY=your_razorpay_key_id
RAZORPAY_SECRET=your_razorpay_secret
```

### 3. Create Plans in Admin Panel

1. Login to admin panel: `https://admin.droutlier.com/admin`
2. Navigate to **Subscription Management** → **Plans**
3. Create your subscription plans with:
   - Plan name and description
   - Pricing (with optional discount)
   - Duration (days/months/years)
   - Select modules to include
   - Mark featured plans (will be highlighted)
   - Add custom features list

### 4. Add Navigation Item (Optional)

If you want "Plans" to appear in the navbar:

**Option A: Via Admin Panel**
1. Go to **Navigation Manager**
2. Click **Add Navigation Item**
3. Fill in:
   - Title: `Plans`
   - URL: `/pricing`
   - Icon: `fas fa-tags`
   - Type: `Custom`
   - Visibility: `Public` (so everyone can see it)
4. Save and reorder if needed

**Option B: Via SQL**
Run `ADD_PRICING_NAVIGATION_ITEM.sql` (already provided)

### 5. Test the Payment Flow

1. Visit `https://www.droutlier.com/pricing`
2. Login if not already authenticated
3. Select a plan and click "Subscribe Now"
4. Complete payment using Razorpay test mode
5. Verify subscription is activated in user account

## API Endpoints Used

All endpoints are in `admin/application/app/Http/Controllers/Api/SubscriptionController.php`:

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/subscription/plans` | GET | Fetch all active plans with modules |
| `/api/subscription/create-order` | POST | Create Razorpay order for payment |
| `/api/subscription/verify-payment` | POST | Verify payment and activate subscription |
| `/api/subscription/my-subscription` | GET | Get user's active subscription |
| `/api/subscription/history` | GET | Get subscription history |

## Payment Flow

1. **User selects plan** → Click "Subscribe Now"
2. **Create order** → API call to create Razorpay order
3. **Open Razorpay checkout** → User enters payment details
4. **Payment success** → Razorpay callback with payment ID
5. **Verify payment** → Backend verifies signature and activates subscription
6. **Success message** → User sees success toast and page reloads

## Design Features

### Color Scheme (matches DrOutlier theme)
- Background: `#1B1E27` (dark)
- Cards: `#282D41` (gray)
- Primary button: `#126E97` (blue)
- Featured plan: Gradient blue with orange "Most Popular" badge
- Accent: `#FFA500` (orange)

### Responsive Layout
- Desktop: 3-column grid
- Tablet: 2-column grid
- Mobile: Single column

### Plan Card Components
- Plan name and description
- Pricing with strikethrough for discounts
- Duration display
- Module list with icons
- Features list with checkmarks
- Subscribe button with hover effects

## Troubleshooting

### Plans not showing
- Check if user is logged in
- Verify plans are active in admin panel
- Check API endpoint returns data
- Check browser console for errors

### Payment not working
- Verify Razorpay keys in `.env`
- Check Razorpay script loads (check console)
- Ensure user is authenticated
- Check payment gateway is in test/live mode

### Navigation link not showing
- Run `ADD_PRICING_NAVIGATION_ITEM.sql`
- Or add manually via admin Navigation Manager
- Set visibility to "Public"
- Ensure `is_active` = 1

## Admin Panel Access

To manage plans and subscriptions:
1. **Plans Management**: Admin → Subscription Management → Plans
2. **Subscriptions**: Admin → Subscription Management → Subscriptions
3. **Navigation**: Admin → Navigation Manager

## Security Notes

- All payment operations require authentication
- Razorpay signature verification prevents payment tampering
- CORS configured for cross-origin API calls
- Sanctum token authentication for API access

## Future Enhancements

- [ ] Add plan comparison table
- [ ] Add FAQ section
- [ ] Implement coupon codes
- [ ] Add testimonials section
- [ ] Implement plan upgrade/downgrade
- [ ] Add refund policy page link

## Support

For issues or questions:
- Check admin panel logs
- Review Laravel logs: `admin/application/storage/logs/`
- Check browser console for frontend errors
- Verify Razorpay dashboard for payment issues
