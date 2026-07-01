<?php

namespace App\Models;

class DocPage extends BaseModel
{
    protected $fillable = [
        'doc_category_id',
        'slug',
        'title',
        'content_html',
        'content_json',
        'order',
        'status',
    ];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DocCategory::class, 'doc_category_id');
    }

    public function revisions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DocRevision::class);
    }

    public function latestRevision(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DocRevision::class)->latestOfMany();
    }
}
