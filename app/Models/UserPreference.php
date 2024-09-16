<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'sources', 'categories', 'authors'];

    // Specify that these fields are JSON
    protected $casts = [
        'sources' => 'array',
        'categories' => 'array',
        'authors' => 'array',
    ];

    // Relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
