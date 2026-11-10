<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FoundItem;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function publicFoundItems(Request $request)
    {
        $categories = Category::all();

        $query = FoundItem::with(['category', 'user'])
            ->where('is_public', true)
            ->whereIn('status', ['DIPUBLIKASIKAN', 'DITAMPUNG', 'MENUNGGU_KLAIM', 'KLAIM_DIPROSES', 'SIAP_DIAMBIL']);

        // Search Keyword
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%")
                  ->orWhere('found_location', 'like', "%{$q}%")
                  ->orWhere('brand', 'like', "%{$q}%")
                  ->orWhere('primary_color', 'like', "%{$q}%");
            });
        }

        // Filter Category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter Location
        if ($request->filled('location')) {
            $query->where('found_location', 'like', "%{$request->location}%");
        }

        // Filter Color
        if ($request->filled('color')) {
            $query->where('primary_color', 'like', "%{$request->color}%");
        }

        // Filter Date
        if ($request->filled('date')) {
            $query->whereDate('found_date', $request->date);
        }

        $items = $query->latest()->paginate(12);

        return view('guest.found-items', compact('items', 'categories'));
    }

    public function showPublicFoundItem($id)
    {
        $item = FoundItem::with(['category', 'storage'])->findOrFail($id);

        return view('guest.found-item-detail', compact('item'));
    }
}
