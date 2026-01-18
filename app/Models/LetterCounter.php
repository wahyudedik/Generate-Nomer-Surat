<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LetterCounter extends Model
{
    use HasFactory;

    protected $fillable = [
        'format_id',
        'period',
        'unit_code',
        'current_number',
    ];

    protected $casts = [
        'current_number' => 'integer',
    ];

    public function format(): BelongsTo
    {
        return $this->belongsTo(LetterFormat::class, 'format_id');
    }
}
