<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GPSPoint;

class Trace extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_at',
        'updated_at',
    ];

    public function gps_points()
    {
        return $this->hasMany(GPSPoint::class);
    }
}
