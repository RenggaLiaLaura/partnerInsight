<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('satisfaction_scores', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn([
                'quality_product',
                'spec_conformity',
                'quality_consistency',
                'price_quality',
                'product_condition',
                'packaging_condition'
            ]);

            // Add new TP columns
            $table->integer('tp1')->after('distributor_id');
            $table->integer('tp2')->after('tp1');
            $table->integer('tp3')->after('tp2');
            $table->integer('tp4')->after('tp3');
            $table->integer('tp5')->after('tp4');

            // Add new TN columns
            $table->integer('tn1')->after('tp5');
            $table->integer('tn2')->after('tn1');
            $table->integer('tn3')->after('tn2');
            $table->integer('tn4')->after('tn3');
            $table->integer('tn5')->after('tn4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('satisfaction_scores', function (Blueprint $table) {
            $table->dropColumn([
                'tp1', 'tp2', 'tp3', 'tp4', 'tp5',
                'tn1', 'tn2', 'tn3', 'tn4', 'tn5'
            ]);

            $table->integer('quality_product');
            $table->integer('spec_conformity');
            $table->integer('quality_consistency');
            $table->integer('price_quality');
            $table->integer('product_condition');
            $table->integer('packaging_condition');
        });
    }
};
