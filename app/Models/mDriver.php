<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class mDriver extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'mDriver';
    protected $primaryKey = 'intDriver_ID';
    public $timestamps = false;

    protected $fillable = [
        'txtDriverName',
        'txtPhoneNumber',
        'txtLicenseNumber',
        'txtEmail',
        'txtPassword',
        'txtAvatar',
        'txtStatus', // AVAILABLE, ON_DUTY, OFF
        'txtInsertedBy',
        'dtmInserted',
        'txtUpdatedBy',
        'dtmUpdated',
        'bitActive',
    ];

    protected $hidden = [
        'txtPassword',
    ];

    protected function casts(): array
    {
        return [
            'txtPassword' => 'hashed',
            'dtmInserted' => 'datetime',
            'dtmUpdated' => 'datetime',
            'bitActive' => 'integer',
        ];
    }

    public function getAuthPassword()
    {
        return $this->txtPassword;
    }

    public function trips()
    {
        return $this->hasMany(trDutyTrip::class, 'intDriver_ID', 'intDriver_ID');
    }

    public function telemetry()
    {
        return $this->hasMany(dtLocationTracking::class, 'intDriver_ID', 'intDriver_ID');
    }

    public function documentations()
    {
        return $this->hasMany(trDutyTrip_Documentations::class, 'intDriver_ID', 'intDriver_ID');
    }
}
