<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mDepartment extends Model
{
    protected $table = 'mDepartment';
    protected $primaryKey = 'intDepartment_ID';
    public $timestamps = false;

    protected $fillable = [
        'txtDepartmentName',
        'txtDepartmentCode',
        'txtInsertedBy',
        'dtmInserted',
        'txtUpdatedBy',
        'dtmUpdated',
        'bitActive',
    ];

    protected function casts(): array
    {
        return [
            'dtmInserted' => 'datetime',
            'dtmUpdated' => 'datetime',
            'bitActive' => 'integer',
        ];
    }
}
