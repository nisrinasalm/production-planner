<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PlanningSlot;

class Planning extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_code',
        'candidate_token',
        'status',
    ];

    public function slots(): HasMany
    {
        return $this->hasMany(PlanningSlot::class);
    }

    public function originalTotal(): int
    {
        return $this->slots->sum('original_quantity');
    }

    public function balancedTotal(): int
    {
        return $this->slots->sum('balanced_quantity');
    }
}
