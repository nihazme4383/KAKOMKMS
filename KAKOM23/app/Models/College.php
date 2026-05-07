<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'access_code', 'role'];

    public function registrations()
    {
        return $this->hasMany(TeamRegistration::class);
    }

    public function eventConfirmations()
    {
        return $this->hasMany(EventConfirmation::class);
    }

    public function isSecretariat()
    {
        return $this->role === 'secretariat';
    }
}
