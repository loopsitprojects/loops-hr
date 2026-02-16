<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RejectionTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'type'];
}
