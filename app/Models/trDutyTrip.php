<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class trDutyTrip extends Model
{
    protected $table = 'trDutyTrip';
    protected $primaryKey = 'intDutyTrip_ID';
    public $timestamps = false;

    protected $fillable = [
        'txtTripCode',
        'intVehicle_ID',
        'intDriver_ID',
        'dtmTripDate',
        'dtmDepartureTime',
        'dtmArrivalTime',
        'txtDestination',
        'txtPurpose',
        'txtTripStatus', // PENDING, SCHEDULED, IN_PROGRESS, REFUELING, ARRIVED, COMPLETED, CANCELLED
        'intStartOdometer',
        'intEndOdometer',
        'floatTotalFuelCost',
        'floatTotalFuelLiters',
        'txtNotes',
        'txtInsertedBy',
        'dtmInserted',
    ];

    protected function casts(): array
    {
        return [
            'dtmTripDate' => 'date',
            'dtmDepartureTime' => 'datetime',
            'dtmArrivalTime' => 'datetime',
            'dtmInserted' => 'datetime',
            'intStartOdometer' => 'integer',
            'intEndOdometer' => 'integer',
            'floatTotalFuelCost' => 'float',
            'floatTotalFuelLiters' => 'float',
        ];
    }

    public function vehicle()
    {
        return $this->belongsTo(mVehicle::class, 'intVehicle_ID', 'intVehicle_ID');
    }

    public function driver()
    {
        return $this->belongsTo(mDriver::class, 'intDriver_ID', 'intDriver_ID');
    }

    public function passengers()
    {
        return $this->hasMany(trDutyTrip_Details::class, 'intDutyTrip_ID', 'intDutyTrip_ID');
    }

    public function documentations()
    {
        return $this->hasMany(trDutyTrip_Documentations::class, 'intDutyTrip_ID', 'intDutyTrip_ID')
                    ->orderBy('dtmInserted', 'asc');
    }

    public function telemetry()
    {
        return $this->hasMany(dtLocationTracking::class, 'intDutyTrip_ID', 'intDutyTrip_ID')
                    ->orderBy('dtmTracked', 'asc');
    }

    public function latestLocation()
    {
        return $this->hasOne(dtLocationTracking::class, 'intDutyTrip_ID', 'intDutyTrip_ID')
                    ->latestOfMany('dtmTracked');
    }

    public function statusLogs()
    {
        return $this->hasMany(logTripStatus::class, 'intDutyTrip_ID', 'intDutyTrip_ID')
                    ->orderBy('dtmInserted', 'desc');
    }
}
