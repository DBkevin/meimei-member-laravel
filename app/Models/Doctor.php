<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use SoftDeletes;

    protected $table = 'doctors';

    protected $fillable = [
        'name',
        'title',
        'avatar',
        'intro',
        'status',
        'sort',
    ];

    protected $casts = [
        'status' => 'integer',
        'sort' => 'integer',
    ];

    /**
     * 关系：多个案例
     */
    public function showcases(): HasMany
    {
        return $this->hasMany(Showcase::class);
    }
}
