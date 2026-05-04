<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ForumCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'icon', 'color', 'image', 'order'];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) return null;
        return str_starts_with($this->image, 'http')
            ? $this->image
            : asset('storage/' . $this->image);
    }

    public function threads()
    {
        return $this->hasMany(ForumThread::class)->orderByDesc('is_pinned')->orderByDesc('last_reply_at');
    }

    public function latestThread()
    {
        return $this->hasOne(ForumThread::class)->latestOfMany('last_reply_at');
    }
}
