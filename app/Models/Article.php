<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    protected $fillable = ['title', 'content', 'author', 'category', 'source', 'published_at'];
}
