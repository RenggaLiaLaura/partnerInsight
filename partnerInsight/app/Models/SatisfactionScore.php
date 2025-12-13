<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SatisfactionScore extends Model
{
    protected $fillable = [
        'distributor_id',
        'score',
        'period',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
