# DrOutlier Subscription & Plan Management System

## Overview
This document describes the comprehensive subscription and plan management system implemented for DrOutlier. The system allows you to:

1. **Manage Plans** - Create and manage subscription plans with pricing and duration
2. **Manage Modules** - Define content modules (Notes, Spotters, OSCE, etc.)
3. **Map Plans to Modules** - Control which modules are accessible with each plan
4. **User Subscriptions** - Handle user purchases and subscription lifecycle
5. **Access Control** - Automatically restrict content based on active subscriptions
6. **Razorpay Integration** - Process payments securely through Razorpay

## Database Structure

### Tables Created

#### 1. `modules`
Stores all available content modules in the system.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar(255) | Unique module identifier (e.g., 'notes', 'spotters') |
| display_name | varchar(255) | Display name (e.g., 'Notes', 'Spotters') |
| slug | varchar(255) | URL-friendly identifier |
| frontend_url | varchar(255) | Frontend route (e.g., '/notes') |
| admin_url | varchar(255) | Admin panel route (e.g., '/admin/note') |
| description | text | Module description |
| icon | varchar(255) | Icon class (e.g., 'fas fa-book') |
| is_active | boolean | Whether module is active |
| sort_order | integer | Display order |

**Pre-seeded Modules:**
- Notes (`notes`)
- Spotters (`spotters`)
- OSCE (`osce`)
- AI Rad - Munchies (`ai-rad`)
- Practical Essentials (`practical-essentials`)
- Watch and Learn (`watch-and-learn`)
- Quizora (`quizora`)

#### 2. `plans`
Stores subscription plans.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar(255) | Plan name |
| slug | varchar(255) | Unique URL-friendly identifier |
| description | text | Plan description |
| price | decimal(10,2) | Regular price |
| discount_price | decimal(10,2) | Discounted price (optional) |
| duration_type | enum | 'days', 'months', or 'years' |
| duration_value | integer | Number of duration units |
| razorpay_plan_id | varchar(255) | Razorpay plan identifier |
| is_active | boolean | Whether plan is available for purchase |
| is_featured | boolean | Whether to highlight the plan |
| sort_order | integer | Display order |
| features | json | Array of plan features |

#### 3. `plan_modules`
Maps plans to their accessible modules (many-to-many relationship).

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| plan_id | bigint | Foreign key to plans |
| module_id | bigint | Foreign key to modules |

#### 4. `user_subscriptions`
Tracks user subscription purchases and status.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key to users |
| plan_id | bigint | Foreign key to plans |
| razorpay_subscription_id | varchar(255) | Razorpay subscription ID |
| razorpay_payment_id | varchar(255) | Razorpay payment ID |
| razorpay_order_id | varchar(255) | Razorpay order ID |
| status | enum | 'active', 'expired', 'cancelled', 'pending' |
| amount_paid | decimal(10,2) | Amount paid for subscription |
| started_at | timestamp | Subscription start date |
| expires_at | timestamp | Subscription expiry date |
| cancelled_at | timestamp | Cancellation date (if applicable) |
| payment_details | json | Additional payment information |

## Admin Panel Routes

### Module Management
- **List Modules**: `/admin/modules`
- **Create Module**: `/admin/modules/create`
- **Edit Module**: `/admin/modules/edit/{id}`
- **Delete Module**: POST to `/admin/modules/delete/{id}`
- **Toggle Status**: POST to `/admin/modules/status/{id}`

### Plan Management
- **List Plans**: `/admin/plans`
- **Create Plan**: `/admin/plans/create`
- **Edit Plan**: `/admin/plans/edit/{id}`
- **Delete Plan**: POST to `/admin/plans/delete/{id}`
- **Toggle Status**: POST to `/admin/plans/status/{id}`

### Subscription Management
- **All Subscriptions**: `/admin/subscriptions`
- **Active Subscriptions**: `/admin/subscriptions/active`
- **Expired Subscriptions**: `/admin/subscriptions/expired`
- **Cancelled Subscriptions**: `/admin/subscriptions/cancelled`
- **Pending Subscriptions**: `/admin/subscriptions/pending`
- **View Details**: `/admin/subscriptions/detail/{id}`
- **Create Manual Subscription**: `/admin/subscriptions/create`
- **Cancel Subscription**: POST to `/admin/subscriptions/cancel/{id}`
- **Extend Subscription**: POST to `/admin/subscriptions/extend/{id}`

## API Endpoints (Frontend Integration)

### Authentication Required (use Bearer token)

#### Subscription & Plan Endpoints

**Get All Available Plans**
```
GET /api/subscription/plans
```
Returns all active plans with their modules and pricing.

**Create Razorpay Order**
```
POST /api/subscription/create-order
Body: {
  "plan_id": 1
}
```
Creates a Razorpay order for plan purchase. Returns order_id and razorpay_key for payment.

**Verify Payment**
```
POST /api/subscription/verify-payment
Body: {
  "razorpay_order_id": "order_xxx",
  "razorpay_payment_id": "pay_xxx",
  "razorpay_signature": "signature_xxx",
  "subscription_id": 1
}
```
Verifies payment and activates subscription.

**Get My Active Subscription**
```
GET /api/subscription/my-subscription
```
Returns user's active subscription with accessible modules.

**Get Subscription History**
```
GET /api/subscription/history
```
Returns all user's subscription history.

**Check Module Access**
```
POST /api/subscription/check-access
Body: {
  "module_slug": "spotters"
}
```
Checks if user has access to a specific module.

**Get Accessible Modules**
```
GET /api/subscription/accessible-modules
```
Returns list of modules user can access based on active subscription.

## Module Access Control

All content API routes are now protected with module access middleware. Users can only access content if they have an active subscription that includes the required module.

### Protected Routes:
- `/api/news/*` - requires `notes` module
- `/api/spotters/*` - requires `spotters` module
- `/api/osce/*` - requires `osce` module
- `/api/munchies/*` - requires `ai-rad` module
- `/api/basic/*` - requires `practical-essentials` module
- `/api/watch-and-learn/*` - requires `watch-and-learn` module
- `/api/quiz/*` - requires `quizora` module

### Error Responses

**No Active Subscription (403)**
```json
{
  "remark": "no_subscription",
  "status": "error",
  "message": {
    "error": ["No active subscription found. Please subscribe to access this content."]
  }
}
```

**No Access to Module (403)**
```json
{
  "remark": "access_denied",
  "status": "error",
  "message": {
    "error": ["You do not have access to this module. Please subscribe to a plan that includes this module."]
  },
  "data": {
    "module": {
      "name": "Spotters",
      "slug": "spotters"
    },
    "required_subscription": true
  }
}
```

## Razorpay Configuration

Add these environment variables to your `.env` file:

```env
RAZORPAY_KEY=your_razorpay_key_id
RAZORPAY_SECRET=your_razorpay_key_secret
RAZORPAY_WEBHOOK_SECRET=your_webhook_secret
RAZORPAY_CURRENCY=INR
RAZORPAY_PAYMENT_CAPTURE=1
```

## Migration Instructions

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```
   This will create all necessary tables and seed the modules.

2. **Verify Module Seeding**
   Check that all 7 modules are created:
   - Notes
   - Spotters
   - OSCE
   - AI Rad (Munchies)
   - Practical Essentials
   - Watch and Learn
   - Quizora

3. **Create Plans in Admin Panel**
   - Go to `/admin/plans/create`
   - Create subscription plans
   - Map modules to each plan

## Usage Flow

### For Admin

1. **Create/Manage Modules** (Auto-created, but can be modified)
   - Navigate to Module Manager
   - Add new modules if needed in future
   - Update URLs, descriptions, icons

2. **Create Plans**
   - Navigate to Plan Manager
   - Create a new plan with pricing and duration
   - Select which modules this plan should include
   - Activate the plan

3. **Manage Subscriptions**
   - View all user subscriptions
   - Filter by status (active, expired, cancelled)
   - Create manual subscriptions for users
   - Cancel or extend subscriptions

### For Users (Frontend)

1. **View Available Plans**
   - Call `/api/subscription/plans`
   - Display plans with features and modules

2. **Purchase Plan**
   - User selects a plan
   - Call `/api/subscription/create-order` with plan_id
   - Initialize Razorpay checkout with returned order_id
   - After successful payment, call `/api/subscription/verify-payment`

3. **Access Content**
   - User calls module APIs (e.g., `/api/spotters/list`)
   - Middleware automatically checks subscription
   - Access granted if subscription includes that module
   - Error returned if no access

4. **Check Subscription Status**
   - Call `/api/subscription/my-subscription`
   - Show subscription details, expiry date, accessible modules

## User Model Methods

The User model now has helper methods for subscription checks:

```php
// Check if user has active subscription
$user->hasActiveSubscription(); // returns boolean

// Check if user has access to specific module
$user->hasAccessToModule('spotters'); // returns boolean

// Get user's active subscription
$user->activeSubscription; // returns UserSubscription or null

// Get all accessible modules
$user->getAccessibleModules(); // returns Collection of Modules
```

## Frontend Integration Example (Next.js)

### 1. Fetch Plans
```javascript
const fetchPlans = async () => {
  const response = await fetch('https://admin.droutlier.com/api/subscription/plans', {
    headers: {
      'Authorization': `Bearer ${userToken}`
    }
  });
  const data = await response.json();
  return data.data.plans;
};
```

### 2. Purchase Plan with Razorpay
```javascript
const purchasePlan = async (planId) => {
  // Create order
  const orderResponse = await fetch('https://admin.droutlier.com/api/subscription/create-order', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${userToken}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ plan_id: planId })
  });
  
  const orderData = await orderResponse.json();
  
  // Initialize Razorpay
  const options = {
    key: orderData.data.razorpay_key,
    amount: orderData.data.amount,
    currency: orderData.data.currency,
    order_id: orderData.data.order_id,
    handler: async function (response) {
      // Verify payment
      await fetch('https://admin.droutlier.com/api/subscription/verify-payment', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${userToken}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          razorpay_order_id: response.razorpay_order_id,
          razorpay_payment_id: response.razorpay_payment_id,
          razorpay_signature: response.razorpay_signature,
          subscription_id: orderData.data.subscription_id
        })
      });
    }
  };
  
  const rzp = new Razorpay(options);
  rzp.open();
};
```

### 3. Check Access Before Loading Module
```javascript
const checkModuleAccess = async (moduleSlug) => {
  const response = await fetch('https://admin.droutlier.com/api/subscription/check-access', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${userToken}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ module_slug: moduleSlug })
  });
  
  const data = await response.json();
  return data.data.has_access;
};

// Usage
const hasAccess = await checkModuleAccess('spotters');
if (!hasAccess) {
  // Show subscription required message
  router.push('/pricing');
} else {
  // Load content
}
```

## Important Notes

1. **Module Slugs Must Match**: Ensure the module slugs in the database match the route parameters in middleware.

2. **Auto-Update Modules**: The modules are automatically seeded. If you add new content sections in the future, create a new migration to add them to the `modules` table.

3. **Plan Features**: Store plan features as JSON array for flexible display on frontend.

4. **Subscription Expiry**: Implement a scheduled job to automatically mark expired subscriptions:
   ```php
   // In app/Console/Kernel.php
   $schedule->call(function () {
       UserSubscription::where('status', 'active')
           ->where('expires_at', '<=', now())
           ->update(['status' => 'expired']);
   })->daily();
   ```

5. **Testing**: Always test with Razorpay test mode before going live.

## Security Considerations

1. Always verify payment signatures on the server side
2. Never trust client-side payment verification
3. Use HTTPS for all API communications
4. Store Razorpay keys securely in environment variables
5. Implement rate limiting on payment endpoints

## Future Enhancements

1. **Webhook Integration**: Add Razorpay webhook handler for automatic payment status updates
2. **Email Notifications**: Send emails on subscription purchase, expiry, renewal
3. **Subscription Renewal**: Implement automatic renewal logic
4. **Discount Codes**: Add coupon/promo code functionality
5. **Trial Periods**: Add free trial support
6. **Analytics**: Track popular plans and module usage

## Support

For issues or questions about the subscription system, please contact the development team.
