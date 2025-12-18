<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    protected $fillable = [
        'code', 
        'name', 
        'region', 
        'address', 
        'phone',
        'province_id',
        'regency_id',
        'district_id',
        'village_id'
    ];

    public function satisfactionScores()
    {
        return $this->hasMany(SatisfactionScore::class);
    }

    public function salesPerformances()
    {
        return $this->hasMany(SalesPerformance::class);
    }

    public function clusteringResult()
    {
        return $this->hasOne(ClusteringResult::class);
    }
}
