<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'hash',
        'size',
        'path',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
