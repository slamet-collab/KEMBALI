<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemStorage extends Model
{
    use HasFactory;

    protected $fillable = [
        'found_item_id',
        'storage_code',
        'building',
        'room',
        'cupboard',
        'shelf',
        'qr_code_path',
        'stored_by',
        'received_at',
        'notes',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class);
    }

    public function storedBy()
    {
        return $this->belongsTo(User::class, 'stored_by');
    }
}
