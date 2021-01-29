<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'provider_id',
        'user_id',
        'avatar'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
