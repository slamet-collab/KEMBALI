<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_code',
        'lost_item_id',
        'found_item_id',
        'user_id',
        'ownership_proof',
        'special_characteristics',
        'proof_photos',
        'status',
        'rejection_reason',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'proof_photos' => 'array',
        'verified_at' => 'datetime',
    ];

    public function lostItem()
    {
        return $this->belongsTo(LostItem::class);
    }

    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function handoverRecord()
    {
        return $this->hasOne(HandoverRecord::class, 'claim_id');
    }
}
