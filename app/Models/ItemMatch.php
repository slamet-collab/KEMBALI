<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'lost_item_id',
        'found_item_id',
        'match_score',
        'confidence_level',
        'match_reasons',
        'status',
    ];

    protected $casts = [
        'match_reasons' => 'array',
    ];

    public function lostItem()
    {
        return $this->belongsTo(LostItem::class);
    }

    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class);
    }
}
