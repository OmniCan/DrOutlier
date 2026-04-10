# Google OAuth Setup Guide

## Current Status
✅ **Login Modal** - Working with email/password authentication  
⚠️ **Google OAuth** - Placeholder only (requires additional setup)

---

## Why Google Login Isn't Working Yet

The Google OAuth login requires:

1. **Google Cloud Console Setup**
   - Create a project at [Google Cloud Console](https://console.cloud.google.com)
   - Enable Google OAuth 2.0 API
   - Create OAuth 2.0 credentials (Client ID + Secret)
   - Add authorized redirect URIs

2. **Laravel Backend Configuration**
   - The Laravel API must have a `/api/google-login` endpoint
   - Backend needs to verify Google tokens
   - Must be configured with Google Client ID and Secret

3. **Frontend JavaScript Library**
   - Need to load Google Sign-In JavaScript library
   - Initialize with your Client ID
   - Handle the OAuth flow

---

## What Works Now

✅ **Email/Password Login** - Fully functional
- Login modal opens when clicking "Login" button
- Authenticates via Laravel API (`/api/login`)
- Stores session in PHP
- Redirects on success

✅ **Registration** - Fully functional
- Signup modal with form validation
- Creates account via Laravel API (`/api/register`)
- Links to login modal after success

✅ **Forgot Password** - Partially functional
- Modal form ready
- Requires Laravel API endpoint `/api/forgot-password`

---

## To Enable Google OAuth (Optional)

### Step 1: Google Cloud Setup
1. Go to https://console.cloud.google.com
2. Create new project "Dr Outlier Radiology"
3. Enable "Google+ API" and "Google Identity"
4. Create OAuth 2.0 credentials
5. Add these authorized redirect URIs:
   - `https://droutlier.com`
   - `https://admin.droutlier.com/api/google-callback`

### Step 2: Update Laravel Backend
Check if your Laravel admin has:
```php
// routes/api.php
Route::post('/google-login', [AuthController::class, 'googleLogin']);
```

The endpoint should:
- Accept Google access token
- Verify token with Google
- Create/find user in database
- Return Laravel auth token

### Step 3: Update Frontend
Replace the placeholder in `includes/login-modals.php`:

```javascript
// Add Google Sign-In library
<script src="https://accounts.google.com/gsi/client" async defer></script>

// Update the click handler
googleLoginBtn.addEventListener('click', function() {
    // Initialize Google Sign-In
    google.accounts.id.initialize({
        client_id: 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com',
        callback: handleGoogleResponse
    });
    
    google.accounts.id.prompt();
});

async function handleGoogleResponse(response) {
    // Send token to backend
    const formData = new FormData();
    formData.append('token', response.credential);
    
    const apiResponse = await fetch('https://admin.droutlier.com/api/google-login', {
        method: 'POST',
        body: formData
    });
    
    const data = await apiResponse.json();
    
    if (data.success && data.data?.access_token) {
        // Store session via login-handler.php
        await fetch('/login-handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                token: data.data.access_token,
                user: data.data.user
            })
        });
        
        window.location.reload();
    }
}
```

---

## Recommendation

**For now:** Use email/password login - it's fully functional and secure.

**For production:** Setting up Google OAuth is optional. Most medical education platforms use email/password auth successfully. Only add Google login if:
- Users request it
- You want to reduce signup friction
- You have time to configure Google Cloud Console properly

The current modal system works great without Google OAuth! 🎉

---

## Testing the Modals

1. Visit https://droutlier.com
2. Click "Login" button in navbar
3. Modal should appear with login form
4. Click "Sign Up" link to switch to registration modal
5. Click "Forgot Password" to test password reset modal

All modals have:
- Dark theme matching your design
- Smooth animations
- Form validation
- Error/success messages
- Mobile responsive layout
