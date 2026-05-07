<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportEvent extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'requires_jersey_no', 'max_students', 'sort_order'];

    protected $casts = [
        'requires_jersey_no' => 'boolean',
    ];

    public function getColorClassAttribute()
    {
        $name = strtolower($this->name);

        if (str_contains($name, 'bola sepak')) {
            return 'event-football';
        }

        if (str_contains($name, 'bola jaring')) {
            return 'event-netball';
        }

        if (str_contains($name, 'bola tampar')) {
            return 'event-volleyball';
        }

        if (str_contains($name, 'sepak takraw')) {
            return 'event-takraw';
        }

        if (str_contains($name, 'petanque')) {
            return 'event-petanque';
        }

        if (str_contains($name, 'tenis')) {
            return 'event-tennis';
        }

        if (str_contains($name, 'skuasy')) {
            return 'event-squash';
        }

        if (str_contains($name, 'bola keranjang')) {
            return 'event-basketball';
        }

        if (str_contains($name, 'badminton')) {
            return 'event-badminton';
        }

        return 'event-default';
    }

    public function usesHomeAwayJerseys()
    {
        return $this->slug === 'bola-sepak';
    }

    public function registrations()
    {
        return $this->hasMany(TeamRegistration::class);
    }

    public function confirmations()
    {
        return $this->hasMany(EventConfirmation::class);
    }
}
