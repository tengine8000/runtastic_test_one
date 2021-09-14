<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Trace;

class GPSPoint extends Model
{
    use HasFactory;

    protected $table = 'gps_points';
    protected $visible = ['latitude', 'longitude', 'distance', 'elevation'];

    protected $fillable = [
        'latitude',
        'longitude',
        'distance',
        'elevation',
        'trace_id'
    ];

    public function trace()
    {
        return $this->belongsTo(Trace::class);
    }
}
