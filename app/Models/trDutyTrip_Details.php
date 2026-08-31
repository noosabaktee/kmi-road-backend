<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class trDutyTrip_Details extends Model
{
    protected $table = 'trDutyTrip_Details';
    protected $primaryKey = 'intDutyTrip_Detail_ID';
    public $timestamps = false;

    protected $fillable = [
        'intDutyTrip_ID',
        'txtEmployeeName',
        'txtEmployeeNIK',
        'txtDepartment',
        'txtPhoneNumber',
        'dtmTripDate',
        'intRequestedVehicle_ID',
        'txtDestination',
        'txtPurpose',
        'txtNotes',
        'txtBookingStatus', // PENDING, ASSIGNED, COMPLETED, CANCELLED
        'txtInsertedBy',
        'dtmInserted',
    ];

    protected function casts(): array
    {
        return [
            'dtmTripDate' => 'date',
            'dtmInserted' => 'datetime',
        ];
    }

    public function trip()
    {
        return $this->belongsTo(trDutyTrip::class, 'intDutyTrip_ID', 'intDutyTrip_ID');
    }

    public function requestedVehicle()
    {
        return $this->belongsTo(mVehicle::class, 'intRequestedVehicle_ID', 'intVehicle_ID');
    }
}
