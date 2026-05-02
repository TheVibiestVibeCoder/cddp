<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ForumCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'icon', 'color', 'order'];

    public function threads()
    {
        return $this->hasMany(ForumThread::class)->orderByDesc('is_pinned')->orderByDesc('last_reply_at');
    }

    public function latestThread()
    {
        return $this->hasOne(ForumThread::class)->latestOfMany('last_reply_at');
    }
}
