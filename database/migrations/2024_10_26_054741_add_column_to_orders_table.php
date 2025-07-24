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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('mobile_no')->after('last_name');
            $table->longText('billing_address')->after('mobile_no');
            $table->string('country')->after('shipping_address');
            $table->string('state')->after('country');
            $table->string('city')->after('state');
            $table->string('pincode')->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('mobile_no');
            $table->dropColumn('billing_address');
            $table->dropColumn('country');
            $table->dropColumn('state');
            $table->dropColumn('city');
            $table->dropColumn('pincode');
        });
    }
};
