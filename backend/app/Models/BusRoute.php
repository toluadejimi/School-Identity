<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'fare_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'fare_amount' => 'decimal:2',
        ];
    }

    public function fareTransactions(): HasMany
    {
        return $this->hasMany(FareTransaction::class);
    }
}
