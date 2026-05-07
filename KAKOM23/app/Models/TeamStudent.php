<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_registration_id',
        'name',
        'matrix_no',
        'ic_no',
        'jersey_no',
        'jersey_no_away',
        'identity_document_path',
    ];

    public function registration()
    {
        return $this->belongsTo(TeamRegistration::class, 'team_registration_id');
    }
}
