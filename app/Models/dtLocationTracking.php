<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class dtLocationTracking extends Model
{
    protected $table = 'dtLocationTracking';
    protected $primaryKey = 'intTracking_ID';
    public $timestamps = false;

    protected $fillable = [
        'intDutyTrip_ID',
        'intDriver_ID',
        'floatLatitude',
        'floatLongitude',
        'floatSpeed',
        'floatHeading',
        'floatAccuracy',
        'dtmTracked',
    ];

    protected function casts(): array
    {
        return [
            'floatLatitude' => 'float',
            'floatLongitude' => 'float',
            'floatSpeed' => 'float',
            'floatHeading' => 'float',
            'floatAccuracy' => 'float',
            'dtmTracked' => 'datetime',
        ];
    }

    public function trip()
    {
        return $this->belongsTo(trDutyTrip::class, 'intDutyTrip_ID', 'intDutyTrip_ID');
    }

    public function driver()
    {
        return $this->belongsTo(mDriver::class, 'intDriver_ID', 'intDriver_ID');
    }
}
