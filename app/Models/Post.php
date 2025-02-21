<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'title', 'content'];

    // the relation between posts and the user are one to many

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
