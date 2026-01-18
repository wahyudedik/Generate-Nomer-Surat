<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LetterFormatSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'format_id',
        'order',
        'kind',
        'value',
        'padding',
    ];

    protected $casts = [
        'order' => 'integer',
        'padding' => 'integer',
    ];

    public function format(): BelongsTo
    {
        return $this->belongsTo(LetterFormat::class, 'format_id');
    }
}
