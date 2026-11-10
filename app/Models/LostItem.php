<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_code',
        'user_id',
        'category_id',
        'title',
        'brand',
        'primary_color',
        'description',
        'last_seen_location',
        'last_seen_date',
        'last_seen_time',
        'photos',
        'status',
        'verification_note',
        'verified_by',
    ];

    protected $casts = [
        'photos' => 'array',
        'last_seen_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function matches()
    {
        return $this->hasMany(ItemMatch::class, 'lost_item_id');
    }

    public function claims()
    {
        return $this->hasMany(Claim::class, 'lost_item_id');
    }
}
