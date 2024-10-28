<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Post extends Model
{
    use HasFactory; use HasApiTokens;

    protected $guarded = ['id'];

    // Relation avec l'utilisateur (auteur du post)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec la catégorie
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
