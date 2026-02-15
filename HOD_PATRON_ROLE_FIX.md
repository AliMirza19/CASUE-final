# HOD/Patron Role System - Complete Fix

## Problem (Masla)

### Issue 1: Multiple HOD/Patron Users
- Database mein multiple users ka role directly "hod" ya "patron" set tha
- Purane default users (HOD-001, PAT-001) bhi "hod" aur "patron" role ke saath the
- Har term ke liye newly appointed users ka bhi role "hod"/"patron" set ho raha tha

### Issue 2: Events Wrong Users Ko Ja Rahe The
- Events purane default users (PAT-01, HOD-001) ko ja rahe the
- Newly appointed HOD/Patron ko events nahi mil rahe the
- Multiple users same dashboard access kar sakte the

### Issue 3: No Proper Role Management
- Koi system nahi tha ke sirf current term ke appointed users hi access karein
- Purane term ke HOD/Patron bhi access kar sakte the

---

## Solution (Hal)

### Step 1: Database Cleanup ✅

**All HOD/Patron users converted to Faculty:**
```sql
UPDATE users SET role = 'faculty' WHERE role IN ('hod', 'patron');
```

**Result:**
- HOD-001 (Dr. Ahmed Khan) → faculty
- PAT-001 (Prof. Muhammad Khan) → faculty
- All BFE users who were hod/patron → faculty

### Step 2: Role Enum Updated ✅

**Migration Created:** `2026_01_14_160322_remove_hod_patron_from_user_role_enum.php`

**New Role Enum:**
```php
ENUM('admin', 'student', 'faculty', 'president', 'sa', 'vc', 'gd')
```

**Removed:** 'hod', 'patron'

**Why?**
- HOD aur Patron ab **permanent roles nahi hain**
- Yeh **temporary assignments** hain jo `role_assignments` table mein store hote hain
- Har term ke liye alag HOD/Patron appoint hote hain

### Step 3: RoleMiddleware Enhanced ✅

**File:** `app/Http/Middleware/RoleMiddleware.php`

**Logic:**
```php
// For HOD routes - ONLY appointed HOD can access
if ($role === 'hod') {
    $hasAccess = $user->isAppointedHod();
}

// For Patron routes - ONLY appointed Patron can access
elseif ($role === 'patron') {
    $hasAccess = $user->isAppointedPatron();
}
```

**Benefits:**
- Sirf current term ke appointed users hi access kar sakte hain
- Purane HOD/Patron automatically access kho dete hain
- No manual intervention needed

---

## How It Works Now (Ab Kaise Kaam Karta Hai)

### User Roles in Database
All users have **base roles**:
- `admin` - System administrator
- `student` - Students
- `faculty` - Faculty members (including those who can become HOD/Patron)
- `president` - Society presidents
- `sa`, `vc`, `gd` - Other roles

### HOD/Patron Assignment
HOD aur Patron **role_assignments table** se manage hote hain:

```
role_assignments table:
- user_id: Faculty user ka ID
- term_id: Kis term ke liye
- role: 'hod' ya 'patron'
- is_active: true/false
- assigned_at: Kab appoint hua
```

### Access Control Flow

**When Faculty User Logs In:**
1. System checks: Is user appointed as HOD for active term?
2. If YES → Redirect to HOD dashboard
3. If NO → Check: Is user appointed as Patron?
4. If YES → Redirect to Patron dashboard
5. If NO → Redirect to Faculty dashboard

**When User Tries to Access HOD Dashboard:**
1. RoleMiddleware checks: `$user->isAppointedHod()`
2. This checks `role_assignments` table for active term
3. If appointed → Access granted
4. If not appointed → Unauthorized error

### Event Routing

**Events are filtered by:**
1. **term_id** - Current active term
2. **status** - pending_patron, pending_hod, etc.

**Example:**
```php
// Patron Dashboard
$pendingEvents = Event::where('status', 'pending_patron')
    ->where('term_id', $currentTermId)
    ->get();
```

**Result:**
- Only current term's events show up
- Only current appointed Patron sees them
- Old Patrons see nothing (they're faculty now)

---

## Example Scenario (Misal)

### Situation 1: New Term Starts

**Before:**
- Spring 2026 term active
- Prof. A is HOD (user_id: 15, term_id: 4)
- Prof. B is Patron (user_id: 16, term_id: 4)

**Admin Creates New Term (Fall 2026):**
1. Admin creates term_id: 5
2. Admin appoints Prof. C as HOD for term 5
3. Prof. C appoints Prof. D as Patron for term 5

**What Happens:**
- Prof. A and Prof. B remain faculty (no change in users table)
- Their role_assignments for term 4 remain (is_active: true)
- New role_assignments created for term 5 (Prof. C, Prof. D)

**Access Control:**
- Prof. A tries to login → Faculty dashboard (term 4 not active)
- Prof. C tries to login → HOD dashboard (term 5 active, appointed)
- Prof. B tries to access Patron dashboard → Unauthorized (not appointed for active term)

### Situation 2: Same Term, New HOD

**Before:**
- Spring 2026 term active (term_id: 4)
- Prof. A is HOD

**Admin Appoints New HOD:**
1. Admin appoints Prof. E as new HOD for term 4
2. System runs: `RoleAssignment::assignHod(prof_e_id, 4, admin_id)`

**What Happens:**
```php
// In assignHod method:
1. Prof. A's assignment: is_active = false
2. Prof. E's assignment: is_active = true
```

**Access Control:**
- Prof. A tries to access HOD dashboard → Unauthorized (is_active: false)
- Prof. A gets Faculty dashboard
- Prof. E gets HOD dashboard

**Events:**
- All pending_hod events for term 4 now show to Prof. E
- Prof. A sees nothing (not appointed anymore)

---

## Benefits (Fayde)

### ✅ Clean Role Management
- No confusion about who is HOD/Patron
- Clear separation between base role (faculty) and assignment (hod/patron)

### ✅ Automatic Access Control
- Purane HOD/Patron automatically lose access
- No manual intervention needed
- System automatically checks role_assignments

### ✅ Term-Based Isolation
- Har term ke events alag hain
- Purane term ke HOD/Patron purane events nahi dekh sakte
- New term ke HOD/Patron sirf new events dekhte hain

### ✅ Data Integrity
- Purane assignments ka history maintain hota hai
- Koi data loss nahi
- Audit trail available

### ✅ Scalability
- Unlimited terms support
- Multiple HOD/Patron changes per term
- No database bloat

---

## Testing Instructions (Test Kaise Karein)

### Test 1: Current Appointed Users
1. Check current term: `SELECT * FROM academic_terms WHERE status = 'active'`
2. Check appointed HOD/Patron:
```sql
SELECT u.name, u.reg_id, u.role, ra.role as assigned_role 
FROM role_assignments ra 
JOIN users u ON ra.user_id = u.id 
WHERE ra.term_id = [active_term_id] AND ra.is_active = 1
```
3. Login with appointed HOD → Should see HOD dashboard
4. Login with appointed Patron → Should see Patron dashboard

### Test 2: Old Users Cannot Access
1. Find old HOD/Patron from previous terms
2. Try to login → Should see Faculty dashboard
3. Try to access /hod/dashboard → Should get Unauthorized error

### Test 3: Event Routing
1. Create new event as student
2. President approves → Status becomes `pending_patron`
3. Check Patron dashboard → Event should appear
4. Old Patron dashboard → Event should NOT appear

### Test 4: Role Change
1. Admin appoints new HOD for current term
2. Old HOD logs out and logs in → Faculty dashboard
3. New HOD logs in → HOD dashboard
4. Pending events show to new HOD only

---

## Database Schema

### users table
```
id | name | reg_id | role (ENUM) | ...
15 | Prof. A | BFE223180 | faculty | ...
16 | Prof. B | BFE223181 | faculty | ...
```

### role_assignments table
```
id | user_id | term_id | role | is_active | assigned_at
7  | 15      | 4       | hod  | 1         | 2026-01-14 15:47:19
8  | 16      | 4       | patron | 1       | 2026-01-14 15:49:18
```

### events table
```
id | title | status | term_id | student_id | ...
1  | Event A | pending_patron | 4 | 3 | ...
```

---

## Files Modified

1. **Database:**
   - `users` table - All hod/patron → faculty
   - `database/migrations/2026_01_14_160322_remove_hod_patron_from_user_role_enum.php`

2. **Middleware:**
   - `app/Http/Middleware/RoleMiddleware.php` - Strict appointment checking

3. **Models:**
   - `app/Models/RoleAssignment.php` - Already had proper methods
   - `app/Models/User.php` - Already had isAppointedHod/Patron methods

4. **Controllers:**
   - Already filtering by term_id (no changes needed)

---

## Important Notes

### ⚠️ Never Set role = 'hod' or 'patron' in users table
- Always use `RoleAssignment::assignHod()` or `assignPatron()`
- Never manually update users.role to 'hod' or 'patron'
- The enum doesn't even allow it anymore

### ⚠️ Always Check Active Term
- HOD/Patron access is term-specific
- Always filter by active term when showing events
- Use `AcademicTerm::getActive()` to get current term

### ⚠️ Deactivation is Automatic
- When new HOD/Patron appointed, old one automatically deactivated
- No need to manually set is_active = false
- `assignHod()` and `assignPatron()` handle it

---

**Date:** January 14, 2026  
**Status:** ✅ COMPLETELY FIXED  
**Impact:** All HOD/Patron role management now works correctly
