<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPerformance extends Model
{
    protected $fillable = [
        'distributor_id',
        'amount',
        'period',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
