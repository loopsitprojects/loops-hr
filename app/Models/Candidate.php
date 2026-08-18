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
        'rating' => 'float',
    ];

    protected static function booted()
    {
        static::created(function ($candidate) {
            if ($candidate->hod_comment) {
                $user = User::whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_HR_ADMIN])->first()
                    ?? User::where('role', User::ROLE_HOD)->first()
                    ?? User::first();

                if ($user) {
                    $candidate->feedbacks()->create([
                        'user_id' => $user->id,
                        'feedback' => $candidate->hod_comment,
                    ]);
                }
            }
        });
    }

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

    public function feedbacks()
    {
        return $this->hasMany(CandidateFeedback::class)->with('user')->orderBy('created_at', 'desc');
    }

    public function ratings()
    {
        return $this->hasMany(CandidateRating::class)->with('user')->orderBy('created_at', 'desc');
    }

    public function updateAggregateRating()
    {
        $avgScore = $this->ratings()->whereNotNull('overall_score')->avg('overall_score');
        $this->update([
            'rating' => $avgScore !== null ? round($avgScore, 1) : null
        ]);
        return $this->rating;
    }

    public function hasRating(): bool
    {
        return (!is_null($this->rating) && $this->rating > 0) || $this->ratings()->exists();
    }


    public function getFirstNameAttribute()
    {
        return explode(' ', trim($this->name))[0];
    }
}
