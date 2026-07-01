<?php

namespace App\Models;

class DocRevision extends BaseModel
{
    protected $fillable = [
        'doc_page_id',
        'user_id',
        'content_json',
        'content_html',
        'change_summary',
        'is_snapshot',
    ];

    protected $casts = [
        'is_snapshot' => 'boolean',
    ];

    public function page(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DocPage::class, 'doc_page_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
