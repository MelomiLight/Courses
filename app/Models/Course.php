<?php

namespace App\Models;

use App\Jobs\DeleteCommentsJob;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_kk',
        'title_ru',
        'title_en',
        'description_en',
        'description_kk',
        'description_ru',
        'start_date',
        'end_date',
        'format',
        'author',
    ];
    protected $casts = [
        'start_date' => 'datetime:Y-m-d H:i:s',
        'end_date' => 'datetime:Y-m-d H:i:s',
    ];
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected static function booted(): void
    {
        static::deleting(function ($course) {
            $course->files->each->delete();
            DeleteCommentsJob::dispatch($course->id);
        });
    }


    public function getTitleAttribute()
    {
        $locale = app()->currentLocale();
        $titleField = "title_$locale";

        return $this->$titleField;
    }

    public function getDescriptionAttribute()
    {
        $locale = app()->currentLocale();
        $descriptionField = "description_$locale";

        return $this->$descriptionField;
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
