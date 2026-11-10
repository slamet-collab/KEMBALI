<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoundItemController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\LostItemController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| KEMBALI — Campus Lost & Found
| Route Definitions
|--------------------------------------------------------------------------
*/

// =============================================
// 1. PUBLIC / GUEST ROUTES
// =============================================
Route::get('/', function () {
    return view('welcome');
})->name('landing');

// Public Found Items (viewable without login)
Route::get('/barang-ditemukan', [GuestController::class, 'publicFoundItems'])->name('public.found-items');
Route::get('/barang-ditemukan/{id}', [GuestController::class, 'showPublicFoundItem'])->name('public.found-items.show');


// =============================================
// 2. AUTHENTICATION ROUTES (Guest only)
// =============================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


// =============================================
// 3. AUTHENTICATED ROUTES (All Roles)
// =============================================
Route::middleware('auth')->group(function () {

    // Dashboard Redirect (auto-redirect by role)
    Route::get('/dashboard', [DashboardController::class, 'redirectUser'])->name('dashboard.redirect');

    // ---- Role-Specific Dashboards ----
    Route::get('/dashboard/mahasiswa', [DashboardController::class, 'mahasiswa'])
        ->middleware('role:mahasiswa')
        ->name('dashboard.mahasiswa');

    Route::get('/dashboard/dosen', [DashboardController::class, 'dosen'])
        ->middleware('role:dosen')
        ->name('dashboard.dosen');

    Route::get('/dashboard/staf', [DashboardController::class, 'staf'])
        ->middleware('role:staf_barang')
        ->name('dashboard.staf');

    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->middleware('role:admin')
        ->name('dashboard.admin');

    Route::get('/dashboard/superadmin', [DashboardController::class, 'superadmin'])
        ->middleware('role:super_admin')
        ->name('dashboard.superadmin');


    // ---- Lost Items (Barang Hilang) ----
    Route::get('/lost-items', [LostItemController::class, 'index'])->name('lost-items.index');
    Route::get('/lost-items/create', [LostItemController::class, 'create'])->name('lost-items.create');
    Route::post('/lost-items', [LostItemController::class, 'store'])->name('lost-items.store');
    Route::delete('/lost-items/{id}', [LostItemController::class, 'destroy'])->name('lost-items.destroy');
    Route::get('/lost-items/{id}', [LostItemController::class, 'show'])->name('lost-items.show');

    // ---- Found Items (Barang Ditemukan) ----
    Route::get('/found-items', [FoundItemController::class, 'index'])->name('found-items.index');
    Route::get('/found-items/create', [FoundItemController::class, 'create'])->name('found-items.create');
    Route::post('/found-items', [FoundItemController::class, 'store'])->name('found-items.store');
    Route::delete('/found-items/{id}', [FoundItemController::class, 'destroy'])->name('found-items.destroy');
    Route::get('/found-items/{id}', [FoundItemController::class, 'show'])->name('found-items.show');


    // ---- AI Matches / Kandidat Kecocokan ----
    Route::get('/matches', function () {
        $user = auth()->user();
        $myLostIds = \App\Models\LostItem::where('user_id', $user->id)->pluck('id');
        $matches = \App\Models\ItemMatch::with(['lostItem', 'foundItem.category', 'foundItem.user'])
            ->whereIn('lost_item_id', $myLostIds)
            ->orderByDesc('match_score')
            ->latest()
            ->paginate(10);
        return view('matches.index', compact('matches'));
    })->name('matches.index');

    Route::get('/matches/{id}', function ($id) {
        $match = \App\Models\ItemMatch::with(['lostItem.user', 'lostItem.category', 'foundItem.user', 'foundItem.category', 'foundItem.storage'])
            ->findOrFail($id);
        return view('matches.show', compact('match'));
    })->name('matches.show');


    // ---- Claims / Klaim ----
    Route::get('/claims', function () {
        $claims = \App\Models\Claim::with(['lostItem', 'foundItem.category', 'user'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
        return view('claims.index', compact('claims'));
    })->name('claims.index');

    Route::get('/claims/{id}', function ($id) {
        $claim = \App\Models\Claim::with(['lostItem', 'foundItem', 'user'])->findOrFail($id);
        return view('claims.show', compact('claim'));
    })->name('claims.show');

    Route::get('/found-items/{id}/claim', function ($id) {
        $item = \App\Models\FoundItem::with('category')->findOrFail($id);
        return view('claims.create', compact('item'));
    })->name('claims.create');

    Route::post('/claims', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'found_item_id' => 'required|exists:found_items,id',
            'lost_item_id' => 'nullable|exists:lost_items,id',
            'ownership_proof' => 'required|string|min:20',
            'special_characteristics' => 'required|string|min:10',
        ]);

        $claimCode = 'CLM-' . date('Y') . '-' . str_pad(\App\Models\Claim::count() + 1, 4, '0', STR_PAD_LEFT);

        \App\Models\Claim::create([
            'claim_code' => $claimCode,
            'found_item_id' => $request->found_item_id,
            'lost_item_id' => $request->lost_item_id,
            'user_id' => auth()->id(),
            'ownership_proof' => $request->ownership_proof,
            'special_characteristics' => $request->special_characteristics,
            'proof_photos' => [],
            'status' => 'DIAJUKAN',
        ]);

        return redirect()->route('claims.index')->with('success', "Klaim {$claimCode} berhasil diajukan! Mohon tunggu proses verifikasi admin.");
    })->name('claims.store');


    // ---- Notifications ----
    Route::get('/notifications', function () {
        $notifications = \App\Models\Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);
        return view('notifications.index', compact('notifications'));
    })->name('notifications.index');

    Route::put('/notifications/{id}/read', function ($id) {
        $notif = \App\Models\Notification::where('user_id', auth()->id())->findOrFail($id);
        $notif->update(['is_read' => true]);
        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    })->name('notifications.read');


    // ---- Profile & Settings ----
    Route::get('/profile', function () {
        return view('profile.show', ['user' => auth()->user()]);
    })->name('profile.show');

    Route::get('/settings', function () {
        return view('settings.show', ['user' => auth()->user()]);
    })->name('settings.show');
});


// =============================================
// 4. STAF BARANG ROUTES
// =============================================
Route::middleware(['auth', 'role:staf_barang'])->prefix('staff')->name('staff.')->group(function () {

    Route::get('/verifications', function () {
        $foundItems = \App\Models\FoundItem::with(['user', 'category'])
            ->where('status', 'MENUNGGU_VERIFIKASI')
            ->latest()
            ->paginate(10);

        return view('admin.verifications', compact('foundItems'));
    })->name('verifications');

    Route::put('/verify-found/{id}', function ($id, \Illuminate\Http\Request $request) {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $item = \App\Models\FoundItem::where('status', 'MENUNGGU_VERIFIKASI')->findOrFail($id);
        $item->update([
            'status' => $request->action === 'approve' ? 'DIPUBLIKASIKAN' : 'DITOLAK',
            'verified_by' => auth()->id(),
        ]);

        $actionLabel = $request->action === 'approve' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Laporan barang ditemukan {$item->item_code} berhasil {$actionLabel}.");
    })->name('verify-found');

    Route::get('/incoming', function () {
        $items = \App\Models\FoundItem::with(['category', 'user'])
            ->where('holding_option', 'satpam')
            ->whereDoesntHave('storage')
            ->latest()
            ->paginate(10);
        return view('staff.incoming', compact('items'));
    })->name('incoming');

    Route::get('/storage', function () {
        $storages = \App\Models\ItemStorage::with(['foundItem.category', 'storedBy'])
            ->latest()
            ->paginate(10);
        return view('staff.storage', compact('storages'));
    })->name('storage');

    Route::get('/handover', function () {
        $claims = \App\Models\Claim::with(['foundItem', 'user'])
            ->where('status', 'SIAP_DIAMBIL')
            ->latest()
            ->paginate(10);
        return view('staff.handover', compact('claims'));
    })->name('handover');

    Route::get('/locations', function () {
        $storages = \App\Models\ItemStorage::with('foundItem')
            ->select('building', 'room')
            ->distinct()
            ->get();
        return view('staff.locations', compact('storages'));
    })->name('locations');

    Route::get('/qr-scan', function () {
        return view('staff.qr-scan');
    })->name('qr-scan');

    Route::get('/handover-history', function () {
        $records = \App\Models\HandoverRecord::with(['claim', 'foundItem', 'claimedByUser', 'handedOverByUser'])
            ->latest()
            ->paginate(10);
        return view('staff.handover-history', compact('records'));
    })->name('handover-history');

    Route::post('/storage', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'found_item_id' => 'required|exists:found_items,id',
            'building' => 'required|string|max:100',
            'room' => 'required|string|max:100',
            'cupboard' => 'nullable|string|max:50',
            'shelf' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $storageCode = 'STR-' . date('Y') . '-' . str_pad(\App\Models\ItemStorage::count() + 1, 4, '0', STR_PAD_LEFT);

        \App\Models\ItemStorage::create([
            'found_item_id' => $request->found_item_id,
            'storage_code' => $storageCode,
            'building' => $request->building,
            'room' => $request->room,
            'cupboard' => $request->cupboard,
            'shelf' => $request->shelf,
            'stored_by' => auth()->id(),
            'received_at' => now(),
            'notes' => $request->notes,
        ]);

        return redirect()->route('staff.storage')->with('success', "Barang berhasil disimpan di loker {$storageCode}!");
    })->name('storage.store');

    Route::post('/handover', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'claim_id' => 'required|exists:claims,id',
            'recipient_identity_no' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $claim = \App\Models\Claim::with('foundItem', 'lostItem')->findOrFail($request->claim_id);
        $handoverCode = 'BA-' . date('Y') . '-' . str_pad(\App\Models\HandoverRecord::count() + 1, 4, '0', STR_PAD_LEFT);

        \App\Models\HandoverRecord::create([
            'handover_code' => $handoverCode,
            'claim_id' => $claim->id,
            'found_item_id' => $claim->found_item_id,
            'claimed_by' => $claim->user_id,
            'handed_over_by' => auth()->id(),
            'handover_date' => now(),
            'recipient_identity_no' => $request->recipient_identity_no,
            'notes' => $request->notes,
        ]);

        $claim->update(['status' => 'SELESAI']);
        if ($claim->foundItem) {
            $claim->foundItem->update(['status' => 'SELESAI']);
        }
        if ($claim->lostItem) {
            $claim->lostItem->update(['status' => 'SELESAI']);
        }

        return redirect()->route('staff.handover-history')->with('success', "Serah terima {$handoverCode} berhasil diselesaikan!");
    })->name('handover.process');
});


// =============================================
// 5. ADMIN ROUTES
// =============================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/verifications', function () {
        $lostItems = \App\Models\LostItem::with(['user', 'category'])
            ->where('status', 'MENUNGGU_VERIFIKASI')
            ->latest()
            ->paginate(10);
        $foundItems = \App\Models\FoundItem::with(['user', 'category'])
            ->where('status', 'MENUNGGU_VERIFIKASI')
            ->latest()
            ->paginate(10);
        return view('admin.verifications', compact('lostItems', 'foundItems'));
    })->name('verifications');

    Route::put('/verify-lost/{id}', function ($id, \Illuminate\Http\Request $request) {
        $item = \App\Models\LostItem::findOrFail($id);
        $item->update([
            'status' => $request->action === 'approve' ? 'DIPUBLIKASIKAN' : 'DITOLAK',
            'verified_by' => auth()->id(),
        ]);
        $actionLabel = $request->action === 'approve' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Laporan barang hilang {$item->item_code} berhasil {$actionLabel}.");
    })->name('verify-lost');

    Route::put('/verify-found/{id}', function ($id, \Illuminate\Http\Request $request) {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $item = \App\Models\FoundItem::where('status', 'MENUNGGU_VERIFIKASI')->findOrFail($id);
        $item->update([
            'status' => $request->action === 'approve' ? 'DIPUBLIKASIKAN' : 'DITOLAK',
            'verified_by' => auth()->id(),
        ]);
        $actionLabel = $request->action === 'approve' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Laporan barang ditemukan {$item->item_code} berhasil {$actionLabel}.");
    })->name('verify-found');

    Route::get('/claims', function () {
        $claims = \App\Models\Claim::with(['user', 'foundItem', 'lostItem'])
            ->whereIn('status', ['DIAJUKAN', 'DIPERIKSA', 'DISETUJUI', 'SIAP_DIAMBIL'])
            ->latest()
            ->paginate(10);
        return view('admin.claims', compact('claims'));
    })->name('claims');

    Route::put('/claims/{id}/update-status', function ($id, \Illuminate\Http\Request $request) {
        $claim = \App\Models\Claim::findOrFail($id);
        $claim->update([
            'status' => $request->status,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);
        return back()->with('success', "Status klaim {$claim->claim_code} berhasil diubah ke {$request->status}.");
    })->name('claims.update-status');

    Route::get('/ai-matches', function () {
        $matches = \App\Models\ItemMatch::with(['lostItem.user', 'foundItem.user', 'lostItem.category'])
            ->latest()
            ->paginate(10);
        return view('admin.ai-matches', compact('matches'));
    })->name('ai-matches');

    Route::get('/items', function () {
        $lostItems = \App\Models\LostItem::with(['user', 'category'])->latest()->paginate(10, ['*'], 'lost_page');
        $foundItems = \App\Models\FoundItem::with(['user', 'category'])->latest()->paginate(10, ['*'], 'found_page');
        return view('admin.items', compact('lostItems', 'foundItems'));
    })->name('items');

    Route::get('/users', [UserManagementController::class, 'index'])->name('users');

    Route::put('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.update-role');
});


// =============================================
// 6. SUPER ADMIN ROUTES
// =============================================
Route::middleware(['auth', 'role:super_admin'])->prefix('superadmin')->name('superadmin.')->group(function () {

    Route::get('/users', [UserManagementController::class, 'index'])->name('users');
    Route::put('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.update-role');

    Route::get('/admins', function () {
        $admins = \App\Models\User::with('role')
            ->whereHas('role', fn($q) => $q->where('code', 'admin'))
            ->latest()
            ->paginate(10);
        return view('superadmin.admins', compact('admins'));
    })->name('admins');

    Route::get('/staff', function () {
        $staff = \App\Models\User::with('role')
            ->whereHas('role', fn($q) => $q->where('code', 'staf_barang'))
            ->latest()
            ->paginate(10);
        return view('superadmin.staff', compact('staff'));
    })->name('staff');

    Route::get('/roles', function () {
        $roles = \App\Models\Role::withCount('users')->with('permissions')->get();
        return view('superadmin.roles', compact('roles'));
    })->name('roles');

    Route::get('/categories', function () {
        $categories = \App\Models\Category::withCount(['lostItems', 'foundItems'])->get();
        return view('superadmin.categories', compact('categories'));
    })->name('categories');

    Route::get('/ai-config', function () {
        return view('superadmin.ai-config');
    })->name('ai-config');

    Route::post('/categories', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        \App\Models\Category::create([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'icon' => $request->icon ?: 'tag',
            'description' => $request->description,
        ]);

        return redirect()->route('superadmin.categories')->with('success', "Kategori '{$request->name}' berhasil ditambahkan!");
    })->name('categories.store');

    Route::post('/ai-config', function (\Illuminate\Http\Request $request) {
        return redirect()->route('superadmin.ai-config')->with('success', 'Konfigurasi bobot AI matching berhasil disimpan!');
    })->name('ai-config.update');

    Route::get('/logs', function () {
        $logs = \App\Models\ActivityLog::with('user')->latest()->paginate(20);
        return view('superadmin.logs', compact('logs'));
    })->name('logs');
});
