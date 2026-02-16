<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $fillable = [
        'candidate_id',
        'hod_id',
        'scheduled_at',
        'duration',
        'meet_link',
        'google_event_id',
        'additional_guests',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function interviewer() // Primary interviewer (backwards compatibility)
    {
        return $this->belongsTo(User::class, 'hod_id');
    }

    public function interviewers()
    {
        return $this->belongsToMany(User::class, 'interview_interviewer');
    }
}
