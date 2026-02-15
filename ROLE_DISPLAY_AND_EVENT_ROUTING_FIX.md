# Role Display & Event Routing Fix

## Issues Fixed

### Issue 1: Events Not Showing to Current Patron/HOD ✅
**Problem:** Jab President event forward karta tha Patron ko, to current appointed Patron ko event nahi dikhta tha.

**Root Cause:** 
- Patron/HOD dashboards `$user->current_term_id` use kar rahe the
- Agar user ka `current_term_id` active term se match nahi karta to events nahi dikhte

**Solution:**
- Dashboards ab **active term** directly fetch karte hain
- `AcademicTerm::getActive()` use karte hain instead of `$user->current_term_id`
- Yeh ensure karta hai ke hamesha current active term ke events dikhein

### Issue 2: Dashboard Shows "Faculty" Instead of "HOD/Patron" ✅
**Problem:** Jab faculty user HOD ya Patron appoint hota tha, to dashboard pe "Faculty" likha dikhta tha instead of "HOD" ya "Patron".

**Root Cause:**
- Dashboard `auth()->user()->role` display kar raha tha
- Database mein role "faculty" hi tha (jo sahi hai)
- Lekin display ke liye appointed role dikhana chahiye

**Solution:**
- User model mein `getDisplayRole()` method add kiya
- User model mein `getDisplayRoleColor()` method add kiya
- Dashboard layout ab yeh methods use karta hai
- Agar user appointed HOD/Patron hai to wo display hota hai

---

## Technical Implementation

### 1. User Model - Display Role Methods

**File:** `app/Models/User.php`

```php
/**
 * Get the display role for the user.
 * If faculty user is appointed as HOD/Patron, return that role.
 */
public function getDisplayRole(): string
{
    if ($this->isAppointedHod()) {
        return 'HOD';
    }
    
    if ($this->isAppointedPatron()) {
        return 'Patron';
    }
    
    return ucfirst($this->role);
}

/**
 * Get the display role color class for badges.
 */
public function getDisplayRoleColor(): string
{
    if ($this->isAppointedHod()) {
        return 'bg-orange-100 text-orange-800';
    }
    
    if ($this->isAppointedPatron()) {
        return 'bg-purple-100 text-purple-800';
    }
    
    return match($this->role) {
        'admin' => 'bg-red-100 text-red-800',
        'president' => 'bg-blue-100 text-blue-800',
        'student' => 'bg-green-100 text-green-800',
        'sa' => 'bg-indigo-100 text-indigo-800',
        'vc' => 'bg-pink-100 text-pink-800',
        'gd' => 'bg-yellow-100 text-yellow-800',
        'faculty' => 'bg-teal-100 text-teal-800',
        default => 'bg-gray-100 text-gray-800',
    };
}
```

### 2. Dashboard Layout - Display Role

**File:** `resources/views/layouts/dashboard.blade.php`

**Before:**
```blade
<p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role) }}</p>
```

**After:**
```blade
<p class="text-xs text-gray-500">{{ auth()->user()->getDisplayRole() }}</p>
```

**Before:**
```blade
<span class="px-3 py-1 text-sm font-medium rounded-full 
    @switch(auth()->user()->role)
        @case('admin') bg-red-100 text-red-800 @break
        ...
    @endswitch
">
    {{ ucfirst(auth()->user()->role) }}
</span>
```

**After:**
```blade
<span class="px-3 py-1 text-sm font-medium rounded-full {{ auth()->user()->getDisplayRoleColor() }}">
    {{ auth()->user()->getDisplayRole() }}
</span>
```

### 3. Patron Dashboard - Active Term

**File:** `app/Http/Controllers/Patron/DashboardController.php`

**Before:**
```php
public function index()
{
    $user = Auth::user();
    $termId = $user->current_term_id;
    
    $pendingEvents = Event::where('status', 'pending_patron')
        ->where('term_id', $termId)
        ->get();
}
```

**After:**
```php
public function index()
{
    $user = Auth::user();
    
    // Get active term instead of user's current_term_id
    $activeTerm = \App\Models\AcademicTerm::getActive();
    $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
    
    $pendingEvents = Event::where('status', 'pending_patron')
        ->where('term_id', $termId)
        ->get();
}
```

### 4. HOD Dashboard - Active Term

**File:** `app/Http/Controllers/Hod/DashboardController.php`

Same changes as Patron dashboard - now uses active term.

---

## How It Works Now

### Role Display Flow

1. **User Logs In:**
   - System checks if user is appointed HOD/Patron
   - `getDisplayRole()` method called

2. **Display Logic:**
   ```
   IF user.isAppointedHod() → Display "HOD"
   ELSE IF user.isAppointedPatron() → Display "Patron"
   ELSE → Display user.role (Faculty, Student, etc.)
   ```

3. **Color Badge:**
   - HOD → Orange badge
   - Patron → Purple badge
   - Faculty → Teal badge
   - Other roles → Their respective colors

### Event Routing Flow

1. **President Approves Event:**
   - Status changes to `pending_patron`
   - Event has `term_id` = current active term

2. **Patron Dashboard Loads:**
   - Gets active term: `AcademicTerm::getActive()`
   - Filters events: `WHERE status = 'pending_patron' AND term_id = active_term_id`
   - Shows all matching events

3. **Result:**
   - Current appointed Patron sees the event
   - Old Patrons don't see it (they're not appointed for active term)

---

## Example Scenarios

### Scenario 1: Faculty User Appointed as Patron

**User:** Prof. B (BFE223181)
**Database Role:** faculty
**Appointed:** Patron for Term 4

**Dashboard Display:**
- Sidebar: "Patron" (not "Faculty")
- Header Badge: Purple "Patron" badge
- Access: Patron dashboard

### Scenario 2: Event Forwarded to Patron

**Event:** "Annual Day 2026"
**Status:** pending_patron
**Term ID:** 4 (active term)

**Patron Dashboard (Prof. B):**
- Active term fetched: Term 4
- Query: `WHERE status = 'pending_patron' AND term_id = 4`
- Result: Event shows up ✅

**Old Patron Dashboard (Prof. from Term 3):**
- Not appointed for Term 4
- Cannot access Patron dashboard (unauthorized)
- Even if they could, events filtered by term_id

### Scenario 3: Multiple Terms

**Term 3 (Inactive):**
- Patron: Prof. X
- Events: 5 pending_patron events

**Term 4 (Active):**
- Patron: Prof. B
- Events: 2 pending_patron events

**Prof. B Dashboard:**
- Shows only Term 4 events (2 events)
- Term 3 events not visible

**Prof. X:**
- Cannot access Patron dashboard (not appointed for active term)
- Sees Faculty dashboard

---

## Benefits

### ✅ Correct Role Display
- Users see their actual functional role
- HOD/Patron clearly identified
- No confusion about permissions

### ✅ Accurate Event Routing
- Events always go to current appointed users
- No events lost or misrouted
- Active term automatically detected

### ✅ Clean Separation
- Database role remains "faculty" (correct)
- Display role shows appointment (HOD/Patron)
- Best of both worlds

### ✅ Automatic Updates
- When new HOD/Patron appointed, display updates automatically
- No manual intervention needed
- System always shows current state

---

## Testing Instructions

### Test 1: Role Display
1. Login as appointed Patron (Prof. B)
2. Check sidebar → Should show "Patron"
3. Check header badge → Should show purple "Patron" badge
4. Logout and login as non-appointed faculty
5. Check sidebar → Should show "Faculty"

### Test 2: Event Routing
1. Login as Student
2. Create and submit event
3. Login as President
4. Approve event (no revision)
5. Login as current Patron
6. Check dashboard → Event should appear
7. Login as old Patron (from previous term)
8. Should see Faculty dashboard (no Patron access)

### Test 3: Term Change
1. Admin creates new term (Term 5)
2. Admin activates Term 5
3. Admin appoints new Patron for Term 5
4. Old Patron (Term 4) logs in → Faculty dashboard
5. New Patron (Term 5) logs in → Patron dashboard
6. New events go to Term 5 Patron only

---

## Files Modified

1. **app/Models/User.php**
   - Added `getDisplayRole()` method
   - Added `getDisplayRoleColor()` method

2. **resources/views/layouts/dashboard.blade.php**
   - Updated role display to use `getDisplayRole()`
   - Updated badge color to use `getDisplayRoleColor()`

3. **app/Http/Controllers/Patron/DashboardController.php**
   - Changed to use active term instead of user's current_term_id

4. **app/Http/Controllers/Hod/DashboardController.php**
   - Changed to use active term instead of user's current_term_id

---

## Important Notes

### ⚠️ Database Role vs Display Role
- **Database role:** Always "faculty" for HOD/Patron
- **Display role:** Shows "HOD" or "Patron" when appointed
- This is correct and intentional

### ⚠️ Active Term is Key
- All dashboards now use active term
- Events filtered by active term
- Ensures correct routing

### ⚠️ Automatic Detection
- System automatically detects appointments
- No manual configuration needed
- Display updates in real-time

---

**Date:** January 14, 2026  
**Status:** ✅ BOTH ISSUES FIXED  
**Impact:** Role display accurate, events route correctly
