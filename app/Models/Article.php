<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     title="Article",
 *     description="Article model",
 *     required={"id", "title", "content", "created_at"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Breaking News"),
 *     @OA\Property(property="content", type="string", example="This is the content of the article."),
 *     @OA\Property(property="author", type="string", example="John Doe"),
 *     @OA\Property(property="category", type="string", example="Technology"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-12 10:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-12 12:00:00")
 * )
 */
class Article extends Model
{

    protected $fillable = ['title', 'content', 'author', 'category', 'source', 'published_at'];
}
