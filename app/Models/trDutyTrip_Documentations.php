<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class trDutyTrip_Documentations extends Model
{
    protected $table = 'trDutyTrip_Documentations';
    protected $primaryKey = 'intDocumentation_ID';
    public $timestamps = false;

    protected $fillable = [
        'intDutyTrip_ID',
        'intDriver_ID',
        'txtCategory', // SEBELUM_BERANGKAT, ISI_BBM, SAMPAI_TUJUAN, SELESAI
        'txtPhotoPath',
        'intOdometer',
        'floatFuelLiters',
        'floatFuelCost',
        'floatLatitude',
        'floatLongitude',
        'txtLocationName',
        'txtNotes',
        'txtInsertedBy',
        'dtmInserted',
    ];

    protected function casts(): array
    {
        return [
            'intOdometer' => 'integer',
            'floatFuelLiters' => 'float',
            'floatFuelCost' => 'float',
            'floatLatitude' => 'float',
            'floatLongitude' => 'float',
            'dtmInserted' => 'datetime',
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
