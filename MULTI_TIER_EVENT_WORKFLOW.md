# Multi-Tier Event Proposal & Approval Workflow

## Overview
Yeh system different roles ke liye flexible event creation aur approval workflow provide karta hai.

## Event Creation Flows

### 1. **Student/Team Members (GD, Photo, Video, SMT, Doc, Deco)**
**Flow:** Student → President → Patron → HOD

- Student ya team member event request karta hai
- President review karta hai
- Patron review karta hai
- HOD final approval deta hai

### 2. **President**
**Flow:** President → Patron → HOD

- President khud event request karta hai
- **President approval skip hota hai** (kyunki wo khud hai)
- Seedha Patron ko jata hai
- Phir HOD final approval deta hai

### 3. **Patron**
**Flow:** Patron → HOD

- Patron khud event request karta hai
- **President aur Patron approval skip hota hai**
- Seedha HOD ko jata hai for final approval

## Database Changes

### New Column: `created_by_role`
```sql
ALTER TABLE events ADD COLUMN created_by_role ENUM(
    'student', 'president', 'patron', 
    'gd', 'photo', 'video', 'smt', 'doc', 'deco'
) DEFAULT 'student';
```

Yeh column track karta hai ke event kis role ne create kiya.

## Files Created/Modified

### Controllers
1. **`app/Http/Controllers/President/EventController.php`** - President event creation
2. **`app/Http/Controllers/Patron/EventController.php`** - Patron event creation
3. **`app/Http/Controllers/Student/EventController.php`** - Updated to track created_by_role

### Models
1. **`app/Models/Event.php`** - Updated with:
   - `created_by_role` in fillable
   - `getInitialStatus()` - Dynamic initial status based on creator
   - `getNextStatus()` - Smart next status based on creator
   - `needsPresidentApproval()` - Check if president approval needed

### Routes
**President Routes:**
```php
Route::get('/my-events', [EventController::class, 'index'])->name('my-events.index');
Route::get('/my-events/create', [EventController::class, 'create'])->name('my-events.create');
Route::post('/my-events', [EventController::class, 'store'])->name('my-events.store');
Route::get('/my-events/{id}', [EventController::class, 'show'])->name('my-events.show');
```

**Patron Routes:**
```php
Route::get('/my-events', [EventController::class, 'index'])->name('my-events.index');
Route::get('/my-events/create', [EventController::class, 'create'])->name('my-events.create');
Route::post('/my-events', [EventController::class, 'store'])->name('my-events.store');
Route::get('/my-events/{id}', [EventController::class, 'show'])->name('my-events.show');
```

### Sidebars
1. **`resources/views/partials/president-sidebar.blade.php`** - "My Events" option added
2. **`resources/views/partials/patron-sidebar.blade.php`** - "My Events" option added

## How It Works

### Event Creation Logic

```php
// In Event Model
public static function getInitialStatus(string $creatorRole): string
{
    return match ($creatorRole) {
        'president' => 'pending_patron',  // Skip president approval
        'patron' => 'pending_hod',        // Skip president & patron approval
        default => 'pending_president',   // Normal flow
    };
}
```

### Approval Flow Logic

```php
public function getNextStatus(): ?string
{
    // President created event
    if ($this->created_by_role === 'president') {
        return match ($this->status) {
            'pending_patron' => 'pending_hod',
            'pending_hod' => 'approved',
            default => null,
        };
    }
    
    // Patron created event
    if ($this->created_by_role === 'patron') {
        return match ($this->status) {
            'pending_hod' => 'approved',
            default => null,
        };
    }
    
    // Normal student flow
    return match ($this->status) {
        'pending_president' => 'pending_patron',
        'pending_patron' => 'pending_hod',
        'pending_hod' => 'approved',
        default => null,
    };
}
```

## Usage Examples

### President Creating Event:
1. President logs in
2. Clicks "My Events" in sidebar
3. Clicks "Create Event"
4. Fills form and submits
5. Event status: `pending_patron` (skips president approval)
6. Patron reviews and approves → `pending_hod`
7. HOD reviews and approves → `approved`

### Patron Creating Event:
1. Patron logs in
2. Clicks "My Events" in sidebar
3. Clicks "Create Event"
4. Fills form and submits
5. Event status: `pending_hod` (skips president & patron approval)
6. HOD reviews and approves → `approved`

### Team Member (e.g., Graphic Designer) Creating Event:
1. GD logs in as student with role 'gd'
2. Goes to "Request Event"
3. Fills form and submits
4. Event status: `pending_president` (normal flow)
5. President → Patron → HOD approval chain

## Benefits

✅ **Flexible Workflow** - Different roles have different approval paths
✅ **No Self-Approval** - President/Patron can't approve their own events
✅ **Efficient** - Skips unnecessary approval steps
✅ **Trackable** - `created_by_role` tracks who created the event
✅ **Scalable** - Easy to add more roles or modify flows

## Migration Command

```bash
php artisan migrate
```

## Testing Checklist

- [ ] Student creates event → Goes to President
- [ ] President creates event → Goes to Patron (skips president)
- [ ] Patron creates event → Goes to HOD (skips president & patron)
- [ ] GD/Photo/Video team member creates event → Goes to President
- [ ] Approval flow works correctly for each creator type
- [ ] Event list shows correct events for each role
- [ ] Sidebar options visible for President and Patron

## Future Enhancements

1. Add edit functionality for President/Patron events
2. Add event templates for quick creation
3. Add bulk event creation
4. Add event cloning feature
5. Add event analytics by creator role
