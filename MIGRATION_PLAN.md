# Migration Plan: Next.js React → Laravel Blade Frontend

## Current Architecture
- **Backend**: Laravel 10 (`admin/application/`)
- **Frontend**: Next.js 13 + React (`src/`)
- **Problem**: Two separate codebases to maintain

## Target Architecture
- **Single Laravel Application** with Blade templates
- **Location**: `admin/application/`
- **Frontend Tech**: Blade + Alpine.js/Livewire + Bootstrap

---

## Phase 1: Setup & Preparation

### 1.1 Update Laravel Configuration
```bash
cd admin/application
composer require laravel/ui
php artisan ui bootstrap --auth
composer require livewire/livewire  # For reactive components
npm install alpinejs  # For lightweight JavaScript
```

### 1.2 Create Frontend Routes Structure
- Move all Next.js routes to Laravel routes
- Create controllers for each section
- Set up Blade view hierarchy

### 1.3 Asset Management
- Configure Vite for Laravel (already in `vite.config.js`)
- Move CSS/JS assets to `resources/` folder
- Set up asset compilation pipeline

---

## Phase 2: Page-by-Page Migration

### Priority Order (Based on your features):

#### **Tier 1 - Core Pages** (Start Here)
1. ✅ Homepage (`src/app/page.js` → `resources/views/home.blade.php`)
2. ✅ Profile (`src/app/profile/` → `resources/views/profile/`)
3. ✅ Subscription (`src/app/subscription/` → `resources/views/subscription/`)
4. ✅ Pricing (`src/app/pricing/` → `resources/views/pricing/`)

#### **Tier 2 - Learning Modules**
5. ✅ Notes (`src/app/notes/` → `resources/views/notes/`)
6. ✅ Spotters (`src/app/new-spotters/` → `resources/views/spotters/`)
7. ✅ OSCE (`src/app/new-osce/` → `resources/views/osce/`)
8. ✅ Table Viva (`src/app/new-table-viva/` → `resources/views/viva/`)
9. ✅ Exam Cases (`src/app/new-exam-cases/` → `resources/views/exam-cases/`)

#### **Tier 3 - Advanced Features**
10. ✅ AI Radiology (`src/app/ai-rad/` → `resources/views/ai-rad/`)
11. ✅ Quiz System (`src/app/quiz/` → `resources/views/quiz/`)
12. ✅ Bookmarks (`src/app/bookmarks/` → `resources/views/bookmarks/`)
13. ✅ Blog (`src/app/radiology-blog/` → `resources/views/blog/`)

---

## Phase 3: Component Migration Strategy

### React Components → Blade Components

**React Component Example:**
```jsx
// src/components/Button.jsx
export default function Button({ text, onClick }) {
  return <button onClick={onClick}>{text}</button>
}
```

**Blade Component:**
```php
<!-- resources/views/components/button.blade.php -->
<button {{ $attributes->merge(['class' => 'btn btn-primary']) }}>
    {{ $slot }}
</button>
```

**Usage:**
```blade
<x-button class="btn-lg">Click Me</x-button>
```

### Interactive Components → Alpine.js or Livewire

**React State Example:**
```jsx
const [count, setCount] = useState(0);
```

**Alpine.js:**
```blade
<div x-data="{ count: 0 }">
    <span x-text="count"></span>
    <button @click="count++">Increment</button>
</div>
```

**Livewire (for complex interactions):**
```php
// app/Livewire/Counter.php
class Counter extends Component {
    public $count = 0;
    
    public function increment() {
        $this->count++;
    }
}
```

---

## Phase 4: API Integration

### Current: Axios calls from React
```javascript
// src/app/api/...
const response = await axios.get('/api/notes');
```

### New: Laravel Controller + Blade
```php
// routes/web.php
Route::get('/notes', [NotesController::class, 'index']);

// app/Http/Controllers/NotesController.php
public function index() {
    $notes = Note::all();
    return view('notes.index', compact('notes'));
}
```

### For AJAX (keep API if needed):
```javascript
// Using Alpine.js with Axios
fetch('/api/notes')
    .then(response => response.json())
    .then(data => this.notes = data);
```

---

## Phase 5: Authentication Migration

### Current: Firebase Auth + Custom JWT
### New: Laravel Sanctum/Session (Already installed)

- Migrate user authentication to Laravel's built-in system
- Keep Firebase for notifications only
- Use `auth()` middleware for protected routes

---

## Phase 6: File Structure

```
admin/application/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php
│   │   │   ├── NotesController.php
│   │   │   ├── SpottersController.php
│   │   │   ├── OsceController.php
│   │   │   └── ... (one per feature)
│   │   └── Livewire/  (for interactive components)
│   └── Models/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php  (main layout)
│   │   │   └── guest.blade.php
│   │   ├── components/
│   │   │   ├── navbar.blade.php
│   │   │   ├── footer.blade.php
│   │   │   └── ... (reusable components)
│   │   ├── home.blade.php
│   │   ├── notes/
│   │   ├── spotters/
│   │   ├── osce/
│   │   └── ... (one folder per feature)
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
├── routes/
│   └── web.php  (all frontend routes)
└── public/
    ├── images/
    ├── css/
    └── js/
```

---

## Phase 7: Deployment Changes

### Before (Two Deployments):
1. Deploy Laravel backend
2. Build & deploy Next.js frontend

### After (Single Deployment):
```bash
composer install
npm install
npm run build  # Compiles assets
php artisan migrate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Benefits of This Migration

✅ **Single Codebase**: Easier maintenance
✅ **Faster Development**: No API layer needed for every page
✅ **Better SEO**: Server-side rendering by default
✅ **Reduced Complexity**: One framework to master
✅ **Cost Effective**: Single server deployment
✅ **Laravel Ecosystem**: Access to packages, debugbar, etc.

---

## Risks & Considerations

⚠️ **Large Migration Effort**: 13+ feature areas to migrate
⚠️ **Learning Curve**: Team needs Blade/Alpine.js knowledge
⚠️ **Loss of React Features**: No component state management like React
⚠️ **Interactivity**: Need to adapt to Alpine.js/Livewire patterns

---

## Recommended Timeline

- **Week 1-2**: Setup Laravel frontend structure, migrate homepage
- **Week 3-4**: Migrate Tier 1 pages (Profile, Subscription, Pricing)
- **Week 5-8**: Migrate Tier 2 learning modules
- **Week 9-12**: Migrate Tier 3 advanced features
- **Week 13-14**: Testing, bug fixes, optimization
- **Week 15**: Parallel run both systems
- **Week 16**: Switch to PHP frontend, monitor

---

## Alternative: Hybrid Approach

Instead of full migration, consider **Inertia.js**:

```bash
composer require inertiajs/inertia-laravel
npm install @inertiajs/react
```

**Benefits:**
- Keep React components as-is
- Single Laravel codebase
- No API layer needed
- Use Laravel routing & controllers

This might be **faster and less risky** than full Blade migration.

---

## Next Steps

1. **Decision**: Full Blade migration OR Inertia.js hybrid?
2. **Pilot**: Migrate ONE page (e.g., homepage) to test approach
3. **Evaluate**: Compare effort vs. benefits
4. **Plan**: Create detailed task breakdown
5. **Execute**: Incremental migration with parallel running

Would you like me to:
- A) Start with a pilot migration (homepage)?
- B) Set up Inertia.js as alternative?
- C) Create detailed code examples for specific pages?
