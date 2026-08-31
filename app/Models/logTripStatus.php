<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class logTripStatus extends Model
{
    protected $table = 'logTripStatus';
    protected $primaryKey = 'intLog_ID';
    public $timestamps = false;

    protected $fillable = [
        'intDutyTrip_ID',
        'txtPreviousStatus',
        'txtNewStatus',
        'txtActionNotes',
        'txtInsertedBy',
        'dtmInserted',
        'txtUpdatedBy',
        'dtmUpdated',
    ];

    protected function casts(): array
    {
        return [
            'dtmInserted' => 'datetime',
            'dtmUpdated' => 'datetime',
        ];
    }

    public function trip()
    {
        return $this->belongsTo(trDutyTrip::class, 'intDutyTrip_ID', 'intDutyTrip_ID');
    }
}
