<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'color'];

    public function artifacts()
    {
        return $this->morphedByMany(Artifact::class, 'taggable');
    }

    public function threads()
    {
        return $this->morphedByMany(ForumThread::class, 'taggable');
    }
}
