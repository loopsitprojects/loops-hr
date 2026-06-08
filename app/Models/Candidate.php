<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    const STAGE_TEST = 'test';
    const STAGE_FIRST_INTERVIEW = '1st_interview';
    const STAGE_SECOND_INTERVIEW = '2nd_interview';
    const STAGE_OFFER_SENT = 'offer_sent';
    const STAGE_OFFER_ACCEPTED = 'offer_accepted';
    const STAGE_HIRED = 'hired';
    const STAGE_REJECTED = 'rejected';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'designation',
        'designation_id',
        'department_id',
        'cv_path',
        'parsed_content',
        'status',
        'stage',
        'hod_comment',
        'is_archived',
        'finalized_at',
        'rating',
        'expected_salary',
        'portfolio'
    ];

    protected $casts = [
        'finalized_at' => 'datetime',
        'is_archived' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function assessments()
    {
        return $this->hasMany(CandidateAssessment::class);
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    public function getFirstNameAttribute()
    {
        return explode(' ', trim($this->name))[0];
    }
}
