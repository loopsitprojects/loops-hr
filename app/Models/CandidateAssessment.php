<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateAssessment extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'candidate_id',
        'test_id',
        'status',
        'sent_at',
        'token',
        'submission_link',
        'file_path'
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}
