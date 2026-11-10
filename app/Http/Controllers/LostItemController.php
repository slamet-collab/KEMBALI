<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\FoundItem;
use App\Models\ItemMatch;
use App\Models\LostItem;
use App\Models\User;
use App\Services\GeminiMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LostItemController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $user = Auth::user();
        $isAdminOrStaff = $user && in_array($user->role?->code, ['admin', 'super_admin', 'staf_barang']);

        // Base Query
        $query = LostItem::with(['category', 'user'])->withCount('claims');

        // Visibility Rule:
        // Admin & staff can view all.
        // Public / regular users can view published items and their own reports.
        // Rejected (DITOLAK) reports are hidden from public list.
        if (!$isAdminOrStaff) {
            $query->where(function ($q) use ($user) {
                $q->where('status', 'DIPUBLIKASIKAN');
                if ($user) {
                    $q->orWhere(function ($own) use ($user) {
                        $own->where('user_id', $user->id)
                            ->where('status', '!=', 'DITOLAK');
                    });
                }
            });
        }

        // Search Keyword
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%")
                  ->orWhere('last_seen_location', 'like', "%{$q}%")
                  ->orWhere('brand', 'like', "%{$q}%")
                  ->orWhere('primary_color', 'like', "%{$q}%")
                  ->orWhere('item_code', 'like', "%{$q}%");
            });
        }

        // Filter Category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter Location
        if ($request->filled('location')) {
            $query->where('last_seen_location', 'like', "%{$request->location}%");
        }

        // Filter Date
        if ($request->filled('date')) {
            $query->whereDate('last_seen_date', $request->date);
        }

        // Filter Status (if specified, e.g. for filtering own reports or admin)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: Tab "Laporan Saya" (my_reports=1)
        if ($request->boolean('my_reports') && $user) {
            $query->where('user_id', $user->id);
        }

        $items = $query->latest()->paginate(10)->withQueryString();

        // Count stats for user tabs
        $myReportsCount = $user ? LostItem::where('user_id', $user->id)->count() : 0;

        return view('lost-items.index', compact('items', 'categories', 'myReportsCount'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('lost-items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();

        $request->validate([
            'identity_no' => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                'max:50',
                Rule::unique('users', 'identity_no')->ignore($currentUser->id),
            ],
            'phone' => [
                'required',
                'string',
                'max:13',
                'regex:/^(\+628[1-9][0-9]{5,9}|628[1-9][0-9]{6,10}|08[1-9][0-9]{6,10})$/',
            ],
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'primary_color' => 'required|string|max:100',
            'description' => 'required|string|min:10',
            'last_seen_location' => 'required|string|max:255',
            'last_seen_date' => 'required|date',
            'last_seen_time' => 'nullable',
            'photo_url' => 'nullable|url',
            'photo_files' => [
                'nullable',
                'array',
                'max:3',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->filled('photo_url') && is_array($value) && count($value) > 2) {
                        $fail('Maksimal 3 foto, termasuk foto dari URL.');
                    }
                },
            ],
            'photo_files.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'identity_no.required' => 'NIM / NIP identitas pelapor wajib diisi.',
            'identity_no.regex' => 'NIM / NIP hanya boleh berisi angka tanpa huruf atau simbol.',
            'identity_no.unique' => 'NIM / NIP sudah terdaftar pada akun lain.',
            'phone.required' => 'Nomor telepon wajib diisi untuk koordinasi pengembalian.',
            'phone.max' => 'Nomor telepon maksimal 13 karakter.',
            'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format yang valid (contoh: 081234567890) maksimal 13 digit.',
            'category_id.required' => 'Pilih kategori barang.',
            'title.required' => 'Nama/judul barang wajib diisi.',
            'primary_color.required' => 'Warna utama barang wajib diisi.',
            'description.required' => 'Deskripsi detail dan ciri khusus wajib diisi.',
            'last_seen_location.required' => 'Lokasi terakhir dilihat wajib diisi.',
            'last_seen_date.required' => 'Tanggal kehilangan wajib diisi.',
            'photo_files.max' => 'Maksimal 3 foto yang dapat diunggah.',
            'photo_files.*.image' => 'File yang diunggah harus berupa gambar.',
            'photo_files.*.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
            'photo_files.*.max' => 'Ukuran setiap foto maksimal 5 MB.',
        ]);

        $itemCode = 'HL-' . date('Y') . '-' . str_pad(LostItem::count() + 1, 4, '0', STR_PAD_LEFT);

        $photos = [];
        foreach ($request->file('photo_files', []) as $photo) {
            $path = $photo->store('lost-items', 'public');
            $photos[] = Storage::disk('public')->url($path);
        }

        if ($request->filled('photo_url')) {
            $photos[] = $request->photo_url;
        } elseif (empty($photos)) {
            $photos[] = 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=500&auto=format&fit=crop';
        }

        $lostItem = DB::transaction(function () use ($request, $currentUser, $itemCode, $photos) {
            // Update user profile contact info
            $currentUser->update([
                'identity_no' => $request->identity_no,
                'phone' => $request->phone,
            ]);

            return LostItem::create([
                'item_code' => $itemCode,
                'user_id' => $currentUser->id,
                'category_id' => $request->category_id,
                'title' => $request->title,
                'brand' => $request->brand,
                'primary_color' => $request->primary_color,
                'description' => $request->description,
                'last_seen_location' => $request->last_seen_location,
                'last_seen_date' => $request->last_seen_date,
                'last_seen_time' => $request->last_seen_time,
                'photos' => $photos,
                'status' => 'DIPUBLIKASIKAN',
            ]);
        });

        // Auto-run AI Matcher engine safely (failure here must not break report creation)
        try {
            $this->runAIMatcher($lostItem);
        } catch (\Throwable $e) {
            Log::warning('AI Matcher failed for lost item: ' . $lostItem->id . ' - ' . $e->getMessage());
        }

        ActivityLog::create([
            'user_id' => $currentUser->id,
            'action' => 'CREATE_LOST_ITEM',
            'description' => "Pengguna {$currentUser->name} membuat laporan barang hilang baru ({$itemCode}: {$lostItem->title}).",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('lost-items.index')->with('created_item', [
            'id' => $lostItem->id,
            'item_code' => $lostItem->item_code,
            'title' => $lostItem->title,
            'created_at' => $lostItem->created_at->format('d M Y, H:i'),
            'status' => $lostItem->status,
        ])->with('success', 'Laporan barang hilang berhasil dikirim!');
    }

    public function show($id)
    {
        $item = LostItem::with(['category', 'user', 'matches.foundItem'])
            ->withCount('claims')
            ->findOrFail($id);
        return view('lost-items.show', compact('item'));
    }

    public function destroy($id)
    {
        $item = LostItem::findOrFail($id);
        $user = Auth::user();

        $isOwner = (int) $item->user_id === (int) $user->id;
        if (!$isOwner && !in_array($user->role?->code, ['admin', 'super_admin', 'staf_barang'])) {
            abort(403);
        }

        if ($item->claims()->exists()) {
            return back()->with('error', 'Laporan tidak dapat dihapus karena sudah memiliki klaim terkait.');
        }

        $photos = $item->photos ?? [];
        $item->delete();
        $this->deleteUploadedPhotos($photos, 'lost-items');

        return redirect()->route('lost-items.index')->with('success', 'Laporan barang hilang berhasil dihapus.');
    }

    private function deleteUploadedPhotos(array $photos, string $directory): void
    {
        $disk = Storage::disk('public');
        $urlPrefix = $disk->url($directory . '/');

        foreach ($photos as $photo) {
            if (!is_string($photo) || !Str::startsWith($photo, $urlPrefix)) {
                continue;
            }

            $filename = substr($photo, strlen($urlPrefix));
            $path = $directory . '/' . $filename;

            if ($filename !== '' && basename($filename) === $filename && $disk->url($path) === $photo && !$disk->delete($path)) {
                Log::warning('Unable to delete uploaded lost item photo.', ['path' => $path]);
            }
        }
    }

    private function runAIMatcher(LostItem $lostItem)
    {
        $geminiService = app(GeminiMatchingService::class);

        $foundCandidates = FoundItem::where('category_id', $lostItem->category_id)
            ->whereIn('status', ['DIPUBLIKASIKAN', 'DITAMPUNG', 'MENUNGGU_KLAIM'])
            ->get();

        foreach ($foundCandidates as $found) {
            $matchResult = $geminiService->match($lostItem, $found);

            ItemMatch::updateOrCreate(
                [
                    'lost_item_id' => $lostItem->id,
                    'found_item_id' => $found->id,
                ],
                [
                    'match_score' => $matchResult['score'],
                    'confidence_level' => $matchResult['confidence'],
                    'match_reasons' => $matchResult['reasons'],
                    'status' => 'PENDING',
                ]
            );
        }
    }
}
