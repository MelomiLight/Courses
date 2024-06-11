<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'hash',
        'size',
        'path',
    ];

    protected static function booted(): void
    {
        static::deleting(function ($file) {
            Storage::delete($file->path);
        });
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_files');
    }
}
