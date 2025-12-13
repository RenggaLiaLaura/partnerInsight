<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Distributor extends Model
{
    use Auditable;
    
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
