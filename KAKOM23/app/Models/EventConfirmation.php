<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventConfirmation extends Model
{
    use HasFactory;

    protected $fillable = ['college_id', 'sport_event_id'];

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function event()
    {
        return $this->belongsTo(SportEvent::class, 'sport_event_id');
    }
}
