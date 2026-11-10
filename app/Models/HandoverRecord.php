<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HandoverRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'handover_code',
        'claim_id',
        'found_item_id',
        'claimed_by',
        'handed_over_by',
        'handover_date',
        'recipient_identity_no',
        'notes',
        'photos',
    ];

    protected $casts = [
        'photos' => 'array',
        'handover_date' => 'datetime',
    ];

    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }

    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class);
    }

    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    public function claimedByUser()
    {
        return $this->claimedBy();
    }

    public function handedOverBy()
    {
        return $this->belongsTo(User::class, 'handed_over_by');
    }

    public function handedOverByUser()
    {
        return $this->handedOverBy();
    }
}
