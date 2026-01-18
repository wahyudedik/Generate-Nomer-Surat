<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Letter extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_COMPLETE = 'complete';

    protected $fillable = [
        'type',
        'format_id',
        'number',
        'sequence',
        'title',
        'description',
        'scan_path',
        'status',
        'issued_at',
        'created_by',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'sequence' => 'integer',
    ];

    public function format(): BelongsTo
    {
        return $this->belongsTo(LetterFormat::class, 'format_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(LetterActivityLog::class);
    }
}
