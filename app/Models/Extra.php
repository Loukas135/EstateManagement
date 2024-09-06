<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Extra extends Model
{
    use HasFactory;
    protected $guarded = [
        'created_at',
        'updated_at'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function works(): HasMany
    {
        return $this->hasMany(Work::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
