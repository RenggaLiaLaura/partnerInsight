<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClusteringResult extends Model
{
    protected $fillable = ['distributor_id', 'cluster_group', 'score_satisfaction', 'score_sales'];
    
    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
