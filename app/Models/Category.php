<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'order', 'icon', 'cover_image'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    public function artifacts()
    {
        return $this->hasMany(Artifact::class);
    }

    public function getArtifactsCountAttribute($value = null): int
    {
        return $value !== null ? (int) $value : $this->artifacts()->count();
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if (!$this->cover_image) return null;
        if (str_starts_with($this->cover_image, 'http')) return $this->cover_image;
        return Storage::url($this->cover_image);
    }
}
