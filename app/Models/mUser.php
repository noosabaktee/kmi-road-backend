<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class mUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'mUser';
    protected $primaryKey = 'intUser_ID';
    public $timestamps = false;

    protected $fillable = [
        'txtUserName',
        'txtEmail',
        'txtPassword',
        'txtRole',
        'txtPhoneNumber',
        'txtAvatar',
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
}
