<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamOfficial extends Model
{
    use HasFactory;

    protected $fillable = ['team_registration_id', 'role', 'name', 'ic_no', 'position', 'phone_no'];

    public function registration()
    {
        return $this->belongsTo(TeamRegistration::class, 'team_registration_id');
    }
}
