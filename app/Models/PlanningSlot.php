<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanningSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'planning_id',
        'slot_order',
        'slot_name',
        'original_quantity',
        'balanced_quantity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'original_quantity' => 'integer',
        'balanced_quantity' => 'integer',
    ];

    public function planning(): BelongsTo
    {
        return $this->belongsTo(Planning::class);
    }
}
