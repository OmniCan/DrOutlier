# DrOutlier PHP Frontend

Lightweight PHP frontend with Twig templating and custom routing.

## Architecture

- **Templating**: Twig 3.0
- **Routing**: Custom PHP router
- **API Communication**: Guzzle HTTP client to Laravel backend
- **Frontend**: Bootstrap 5 + Vanilla JavaScript

## Setup Instructions

### 1. Install Dependencies

```bash
cd frontend
composer install
```

### 2. Configure Environment

```bash
cp .env.example .env
```

Edit `.env` and set your Laravel API URL:
```
API_BASE_URL=http://localhost/admin/application/public/api
```

### 3. Create Required Directories

```bash
mkdir -p storage/cache
chmod 777 storage/cache
```

### 4. Configure Web Server

#### Apache (.htaccess already included)

Set your document root to `frontend/public/`

#### PHP Built-in Server (Development)

```bash
php -S localhost:8000 -t public
```

#### Nginx

```nginx
server {
    listen 80;
    server_name droutlier.local;
    root /path/to/frontend/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

### 5. Test the Installation

Visit: `http://localhost:8000`

## Folder Structure

```
frontend/
├── public/              # Web root
│   ├── index.php       # Entry point
│   ├── .htaccess       # Apache config
│   ├── css/
│   ├── js/
│   └── images/
├── src/
│   ├── Controllers/    # Page controllers
│   ├── Core/           # Router & View engine
│   ├── Services/       # API service layer
│   └── helpers.php     # Helper functions
├── routes/
│   └── web.php         # Route definitions
├── views/              # Twig templates
│   ├── layouts/
│   ├── components/
│   └── pages/
├── storage/
│   └── cache/          # Twig cache
├── composer.json
└── .env
```

## Creating New Pages

### 1. Add Route

Edit `routes/web.php`:
```php
$router->get('/my-page', [MyController::class, 'index']);
```

### 2. Create Controller

Create `src/Controllers/MyController.php`:
```php
<?php
namespace App\Controllers;

use Add\Core\View;
use App\Services\ApiService;

class MyController {
    public function index() {
        $api = new ApiService();
        $data = $api->get('/my-endpoint');
        
        View::render('my-page.twig', [
            'title' => 'My Page',
            'data' => $data
        ]);
    }
}
```

### 3. Create View

Create `views/my-page.twig`:
```twig
{% extends 'layouts/base.twig' %}

{% block content %}
    <h1>{{ title }}</h1>
    <!-- Your content -->
{% endblock %}
```

## Migration from React

### React → Twig Examples

#### React Component
```jsx
function UserCard({ user }) {
    return (
        <div className="card">
            <h3>{user.name}</h3>
            <p>{user.email}</p>
        </div>
    );
}
```

#### Twig Template
```twig
<div class="card">
    <h3>{{ user.name }}</h3>
    <p>{{ user.email }}</p>
</div>
```

#### React State → JavaScript
```jsx
// React
const [count, setCount] = useState(0);
```

```javascript
// Vanilla JS (or use Alpine.js)
let count = 0;
document.getElementById('increment').onclick = () => {
    count++;
    document.getElementById('count').textContent = count;
};
```

## API Integration

All API calls go through `ApiService`:

```php
$api = new ApiService();

// GET request
$notes = $api->get('/notes');

// POST request
$result = $api->post('/notes', [
    'title' => 'New Note',
    'content' => 'Content here'
]);

// With authentication (uses session token)
$profile = $api->get('/profile');
```

## Next Steps

1. ✅ Setup complete
2. Configure Laravel API endpoints
3. Migrate React pages one by one
4. Test each feature thoroughly
5. Deploy to production

## Troubleshooting

- **Routes not working**: Check `.htaccess` or web server config
- **Twig cache issues**: Delete `storage/cache/*`
- **API errors**: Check Laravel logs and `API_BASE_URL` in `.env`
