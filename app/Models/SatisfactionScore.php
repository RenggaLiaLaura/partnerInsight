<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SatisfactionScore extends Model
{
    protected $fillable = [
        'distributor_id',
        'quality_product',
        'spec_conformity',
        'quality_consistency',
        'price_quality',
        'product_condition',
        'packaging_condition',
        'score',
        'period',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
