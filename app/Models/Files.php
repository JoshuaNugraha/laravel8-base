<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;
    protected $table = "files";
    protected $fillable = [
        'realname',
        'filename',
        'author_id',
        'path'
    ];
}
