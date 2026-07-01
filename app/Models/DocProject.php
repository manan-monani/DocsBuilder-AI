<?php

namespace App\Models;

class DocProject extends BaseModel
{
    protected $fillable = ['slug', 'name', 'description', 'is_active', 'logo'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function versions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DocVersion::class)->latest();
    }

    public function defaultVersion(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DocVersion::class)->where('is_default', true);
    }
}
