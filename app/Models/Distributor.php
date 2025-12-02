<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    protected $fillable = ['name', 'region', 'address', 'phone'];

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
