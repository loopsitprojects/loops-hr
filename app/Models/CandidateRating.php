<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CandidateRating extends Model
{
    use HasFactory;

    protected $table = 'candidate_ratings';

    protected $fillable = [
        'candidate_id',
        'user_id',
        'interview_id',
        'overall_score',
        'recommendation',
        'area_ratings',
        'notes',
    ];

    protected $casts = [
        'overall_score' => 'float',
        'area_ratings' => 'array',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function interview()
    {
        return $this->belongsTo(Interview::class);
    }
}
