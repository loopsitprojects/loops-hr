<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'content',
        'attachment_path'
    ];

    public function assessments()
    {
        return $this->hasMany(CandidateAssessment::class);
    }
}
