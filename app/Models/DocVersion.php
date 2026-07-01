<?php

namespace App\Models;

// Assuming BaseModel is in App\Models
// Assuming DocProject is in App\Models
// Assuming DocCategory is in App\Models
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocVersion extends BaseModel
{
    protected $fillable = ['doc_project_id', 'slug', 'name', 'is_default', 'is_archived'];

    protected $casts = [
        'is_default' => 'boolean',
        'is_archived' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(DocProject::class, 'doc_project_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(DocCategory::class);
    }
}
