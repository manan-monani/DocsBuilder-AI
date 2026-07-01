<?php

namespace App\Models;

class DocCategory extends BaseModel
{
    protected $fillable = [
        'doc_version_id',
        'slug',
        'name',
        'order',
        'icon',
    ];

    public function version(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DocVersion::class, 'doc_version_id');
    }

    public function pages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DocPage::class);
    }
}
