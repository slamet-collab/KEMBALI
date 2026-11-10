<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundItem extends Model
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
        'found_location',
        'found_date',
        'found_time',
        'photos',
        'status',
        'is_public',
        'holding_option',
        'holding_location_name',
        'verification_note',
        'verified_by',
    ];

    protected $casts = [
        'photos' => 'array',
        'found_date' => 'date',
        'is_public' => 'boolean',
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

    public function storage()
    {
        return $this->hasOne(ItemStorage::class, 'found_item_id');
    }

    public function matches()
    {
        return $this->hasMany(ItemMatch::class, 'found_item_id');
    }

    public function claims()
    {
        return $this->hasMany(Claim::class, 'found_item_id');
    }

    public function handoverRecords()
    {
        return $this->hasMany(HandoverRecord::class, 'found_item_id');
    }
}
