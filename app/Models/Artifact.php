<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Artifact extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'summary', 'description', 'type',
        'file_path', 'file_name', 'file_size', 'file_mime',
        'external_url', 'thumbnail', 'user_id', 'category_id',
        'is_featured', 'is_published', 'language', 'source', 'published_date',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id')->orderBy('created_at');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return asset('storage/' . $this->file_path);
        }
        return $this->external_url;
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'document', 'report', 'brief' => '📄',
            'video' => '🎥',
            'image' => '🖼️',
            'link' => '🔗',
            'dataset' => '📊',
            default => '📁',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'document' => 'Document',
            'report' => 'Report',
            'brief' => 'Brief',
            'video' => 'Video',
            'image' => 'Image',
            'link' => 'Link',
            'dataset' => 'Dataset',
            default => 'File',
        };
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementDownloads(): void
    {
        $this->increment('downloads_count');
    }
}
