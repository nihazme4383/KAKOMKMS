<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamRegistration extends Model
{
    use HasFactory;

    protected $fillable = ['college_id', 'sport_event_id', 'notes'];

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function event()
    {
        return $this->belongsTo(SportEvent::class, 'sport_event_id');
    }

    public function officials()
    {
        return $this->hasMany(TeamOfficial::class);
    }

    public function students()
    {
        return $this->hasMany(TeamStudent::class);
    }
}
