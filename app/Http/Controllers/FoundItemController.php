<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\FoundItem;
use App\Models\ItemMatch;
use App\Models\LostItem;
use App\Services\GeminiMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FoundItemController extends Controller
{
    public function index()
    {
        $items = FoundItem::with(['category', 'user'])
            ->withCount(['claims', 'handoverRecords'])
            ->with('storage')
            ->latest()
            ->paginate(10);
        return view('found-items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('found-items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'primary_color' => 'required|string|max:100',
            'description' => 'required|string|min:10',
            'found_location' => 'required|string|max:255',
            'found_date' => 'required|date',
            'found_time' => 'nullable',
            'holding_option' => 'required|string',
            'holding_location_name' => 'nullable|string|max:255',
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
            'category_id.required' => 'Pilih kategori barang.',
            'title.required' => 'Nama/judul barang wajib diisi.',
            'primary_color.required' => 'Warna utama barang wajib diisi.',
            'description.required' => 'Deskripsi dan kondisi fisik wajib diisi.',
            'found_location.required' => 'Lokasi ditemukannya barang wajib diisi.',
            'found_date.required' => 'Tanggal ditemukannya barang wajib diisi.',
            'holding_option.required' => 'Pilih opsi penyerahan / penyimpanan barang.',
            'photo_files.max' => 'Maksimal 3 foto yang dapat diunggah.',
            'photo_files.*.image' => 'File yang diunggah harus berupa gambar.',
            'photo_files.*.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
            'photo_files.*.max' => 'Ukuran setiap foto maksimal 5 MB.',
        ]);

        $itemCode = 'FD-' . date('Y') . '-' . str_pad(FoundItem::count() + 1, 4, '0', STR_PAD_LEFT);

        $photos = [];
        foreach ($request->file('photo_files', []) as $photo) {
            $path = $photo->store('found-items', 'public');
            $photos[] = Storage::disk('public')->url($path);
        }

        if ($request->filled('photo_url')) {
            $photos[] = $request->photo_url;
        } elseif (empty($photos)) {
            $photos[] = 'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=500&auto=format&fit=crop';
        }

        $foundItem = FoundItem::create([
            'item_code' => $itemCode,
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'title' => $request->title,
            'brand' => $request->brand,
            'primary_color' => $request->primary_color,
            'description' => $request->description,
            'found_location' => $request->found_location,
            'found_date' => $request->found_date,
            'found_time' => $request->found_time,
            'photos' => $photos,
            'status' => 'MENUNGGU_VERIFIKASI',
            'is_public' => true,
            'holding_option' => $request->holding_option,
            'holding_location_name' => $request->holding_location_name ?? 'Disimpan oleh Penemu',
        ]);

        // Match candidates are generated now and only become public when staff approves this report.
        $this->runAIMatcher($foundItem);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'CREATE_FOUND_ITEM',
            'description' => "Pengguna " . Auth::user()->name . " melaporkan penemuan barang baru ({$itemCode}: {$foundItem->title}).",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('dashboard.redirect')->with('success', "Laporan barang ditemukan {$itemCode} berhasil dikirim dan menunggu konfirmasi staf.");
    }

    public function show($id)
    {
        $item = FoundItem::with(['category', 'user', 'storage', 'matches.lostItem'])
            ->withCount(['claims', 'handoverRecords'])
            ->findOrFail($id);
        return view('found-items.show', compact('item'));
    }

    public function destroy($id)
    {
        $item = FoundItem::findOrFail($id);
        $user = Auth::user();

        $isOwner = (int) $item->user_id === (int) $user->id;
        if (!$isOwner && !in_array($user->role?->code, ['admin', 'super_admin', 'staf_barang'])) {
            abort(403);
        }

        if ($item->claims()->exists() || $item->storage()->exists() || $item->handoverRecords()->exists()) {
            return back()->with('error', 'Laporan tidak dapat dihapus karena sudah memiliki klaim atau catatan penyimpanan/serah terima.');
        }

        $photos = $item->photos ?? [];
        $item->delete();
        $this->deleteUploadedPhotos($photos, 'found-items');

        return redirect()->route('found-items.index')->with('success', 'Laporan barang ditemukan berhasil dihapus.');
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
                Log::warning('Unable to delete uploaded found item photo.', ['path' => $path]);
            }
        }
    }

    private function runAIMatcher(FoundItem $foundItem)
    {
        $geminiService = app(GeminiMatchingService::class);

        $lostCandidates = LostItem::where('category_id', $foundItem->category_id)
            ->whereIn('status', ['DIPUBLIKASIKAN', 'MENUNGGU_VERIFIKASI'])
            ->get();

        foreach ($lostCandidates as $lost) {
            $matchResult = $geminiService->match($lost, $foundItem);

            ItemMatch::updateOrCreate(
                [
                    'lost_item_id' => $lost->id,
                    'found_item_id' => $foundItem->id,
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
