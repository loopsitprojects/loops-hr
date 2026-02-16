<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name'];

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function designations()
    {
        return $this->hasMany(Designation::class);
    }
}
