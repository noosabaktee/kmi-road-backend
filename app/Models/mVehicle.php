<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mVehicle extends Model
{
    protected $table = 'mVehicle';
    protected $primaryKey = 'intVehicle_ID';
    public $timestamps = false;

    protected $fillable = [
        'txtVehicleName',
        'txtPlateNumber',
        'txtBrandModel',
        'txtVehicleType',
        'intMaxSeat',
        'intCurrentOdometer',
        'txtFuelType',
        'txtVehiclePhoto',
        'txtStatus', // AVAILABLE, IN_USE, MAINTENANCE
        'txtInsertedBy',
        'dtmInserted',
        'txtUpdatedBy',
        'dtmUpdated',
        'bitActive',
    ];

    protected function casts(): array
    {
        return [
            'intMaxSeat' => 'integer',
            'intCurrentOdometer' => 'integer',
            'dtmInserted' => 'datetime',
            'dtmUpdated' => 'datetime',
            'bitActive' => 'integer',
        ];
    }

    public function trips()
    {
        return $this->hasMany(trDutyTrip::class, 'intVehicle_ID', 'intVehicle_ID');
    }

    /**
     * Calculate remaining seats for a given date
     */
    public function getRemainingSeatsAttribute()
    {
        return $this->getRemainingSeatsForDate(now()->toDateString());
    }

    public function getRemainingSeatsForDate($date = null)
    {
        $dateStr = $date ? (is_string($date) ? $date : (is_object($date) ? $date->toDateString() : now()->toDateString())) : now()->toDateString();
        
        // Count passengers in active/scheduled trips on this date
        $bookedSeats = trDutyTrip_Details::whereDate('dtmTripDate', $dateStr)
            ->where(function($query) {
                $query->where('intRequestedVehicle_ID', $this->intVehicle_ID)
                      ->orWhereHas('trip', function($q) {
                          $q->where('intVehicle_ID', $this->intVehicle_ID)
                            ->whereNotIn('txtTripStatus', ['CANCELLED', 'COMPLETED']);
                      });
            })
            ->whereNotIn('txtBookingStatus', ['CANCELLED'])
            ->count();

        return max(0, (int)$this->intMaxSeat - $bookedSeats);
    }
}
