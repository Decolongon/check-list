<?php

namespace App\Models;

use App\Models\CheckList;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['author_id', 'category_name', 'category_slug'])]
class Category extends Model
{
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(CheckList::class);
    }
}
