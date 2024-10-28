<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Category extends Model
{
    use HasFactory; use HasApiTokens;

    protected $guarded = ['id'];

      // Relation avec les posts : une catégorie peut avoir plusieurs posts
      public function posts()
      {
          return $this->hasMany(Post::class);
      }
}
