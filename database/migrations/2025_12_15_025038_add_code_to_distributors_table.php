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
        Schema::table('distributors', function (Blueprint $table) {
            $table->string('code')->after('id')->nullable();
        });

        // Generate codes for existing distributors
        $distributors = DB::table('distributors')->get();
        foreach ($distributors as $index => $distributor) {
            DB::table('distributors')
                ->where('id', $distributor->id)
                ->update(['code' => 'DIST-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)]);
        }

        // Make it unique and not nullable after population
        Schema::table('distributors', function (Blueprint $table) {
            $table->string('code')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
