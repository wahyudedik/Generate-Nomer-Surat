<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LetterFormat extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'period_mode',
        'counter_scope',
        'name',
        'description',
    ];

    public function segments(): HasMany
    {
        return $this->hasMany(LetterFormatSegment::class, 'format_id')->orderBy('order');
    }

    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class, 'format_id');
    }
}
