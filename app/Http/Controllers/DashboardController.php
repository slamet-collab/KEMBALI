<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Claim;
use App\Models\FoundItem;
use App\Models\HandoverRecord;
use App\Models\ItemMatch;
use App\Models\ItemStorage;
use App\Models\LostItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function redirectUser()
    {
        $user = Auth::user();
        return match ($user->role?->code) {
            'mahasiswa' => redirect()->route('dashboard.mahasiswa'),
            'dosen' => redirect()->route('dashboard.dosen'),
            'staf_barang' => redirect()->route('dashboard.staf'),
            'admin' => redirect()->route('dashboard.admin'),
            'super_admin' => redirect()->route('dashboard.superadmin'),
            default => redirect()->route('landing'),
        };
    }

    public function mahasiswa()
    {
        $user = Auth::user();

        $myLostCount = LostItem::where('user_id', $user->id)->count();
        $myFoundCount = FoundItem::where('user_id', $user->id)->count();
        
        $myLostIds = LostItem::where('user_id', $user->id)->pluck('id');
        $matchesCount = ItemMatch::whereIn('lost_item_id', $myLostIds)->where('status', 'PENDING')->count();
        
        $activeClaimsCount = Claim::where('user_id', $user->id)->whereIn('status', ['DIAJUKAN', 'DIPERIKSA', 'DISETUJUI', 'SIAP_DIAMBIL'])->count();

        // Top AI Match Candidate for Andi Pratama / Student
        $topMatch = ItemMatch::with(['lostItem', 'foundItem.user', 'foundItem.category'])
            ->whereIn('lost_item_id', $myLostIds)
            ->latest()
            ->first();

        // Recent items feed
        $recentLost = LostItem::with('category')->latest()->take(4)->get();
        $recentFound = FoundItem::with('category')->where('is_public', true)->latest()->take(4)->get();

        return view('dashboard.mahasiswa', compact(
            'user', 'myLostCount', 'myFoundCount', 'matchesCount', 'activeClaimsCount', 'topMatch', 'recentLost', 'recentFound'
        ));
    }

    public function dosen()
    {
        $user = Auth::user();

        $myReportsCount = LostItem::where('user_id', $user->id)->count() + FoundItem::where('user_id', $user->id)->count();
        $activeClaimsCount = Claim::where('user_id', $user->id)->whereIn('status', ['DIAJUKAN', 'DIPERIKSA', 'DISETUJUI', 'SIAP_DIAMBIL'])->count();
        
        $myLostIds = LostItem::where('user_id', $user->id)->pluck('id');
        $matchesCount = ItemMatch::whereIn('lost_item_id', $myLostIds)->count();

        $myReports = LostItem::where('user_id', $user->id)->latest()->take(5)->get();

        return view('dashboard.dosen', compact('user', 'myReportsCount', 'activeClaimsCount', 'matchesCount', 'myReports'));
    }

    public function staf()
    {
        $user = Auth::user();

        $incomingCount = FoundItem::where('holding_option', 'satpam')->whereDoesntHave('storage')->count();
        $storedCount = ItemStorage::count();
        $readyHandoverCount = Claim::where('status', 'SIAP_DIAMBIL')->count();
        $completedHandoverCount = HandoverRecord::count();

        $recentStorages = ItemStorage::with(['foundItem.category', 'storedBy'])->latest()->take(5)->get();

        return view('dashboard.staf', compact(
            'user', 'incomingCount', 'storedCount', 'readyHandoverCount', 'completedHandoverCount', 'recentStorages'
        ));
    }

    public function admin()
    {
        $user = Auth::user();

        $pendingReportVerifications = FoundItem::where('status', 'MENUNGGU_VERIFIKASI')->count();
        $pendingClaimsVerifications = Claim::whereIn('status', ['DIAJUKAN', 'DIPERIKSA'])->count();
        $highConfidenceMatches = ItemMatch::where('confidence_level', 'SANGAT_TINGGI')->count();
        $totalHandovers = HandoverRecord::count();

        $unverifiedLost = LostItem::with(['user', 'category'])->where('status', 'MENUNGGU_VERIFIKASI')->latest()->take(5)->get();
        $unverifiedClaims = Claim::with(['user', 'foundItem'])->whereIn('status', ['DIAJUKAN', 'DIPERIKSA'])->latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'user', 'pendingReportVerifications', 'pendingClaimsVerifications', 'highConfidenceMatches', 'totalHandovers', 'unverifiedLost', 'unverifiedClaims'
        ));
    }

    public function superadmin()
    {
        $user = Auth::user();

        $totalUsers = User::count();
        $totalAdmins = User::whereHas('role', fn($q) => $q->where('code', 'admin'))->count();
        $totalStaff = User::whereHas('role', fn($q) => $q->where('code', 'staf_barang'))->count();
        $totalCategories = Category::count();
        $todayLogsCount = ActivityLog::whereDate('created_at', today())->count();

        $recentLogs = ActivityLog::with('user')->latest()->take(8)->get();
        $roles = Role::withCount('users')->get();

        return view('dashboard.superadmin', compact(
            'user', 'totalUsers', 'totalAdmins', 'totalStaff', 'totalCategories', 'todayLogsCount', 'recentLogs', 'roles'
        ));
    }
}
