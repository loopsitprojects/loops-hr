<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CandidateFeedback extends Model
{
    use HasFactory;

    protected $table = 'candidate_feedbacks';

    protected $fillable = [
        'candidate_id',
        'user_id',
        'feedback'
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
