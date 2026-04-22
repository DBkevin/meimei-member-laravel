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
        Schema::create('point_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->unique()->constrained('members')->cascadeOnDelete()->comment('会员');
            $table->integer('balance')->default(0)->comment('余额');
            $table->integer('total_earned')->default(0)->comment('总获得');
            $table->integer('total_spent')->default(0)->comment('总消费');
            $table->integer('frozen_points')->default(0)->comment('冻结积分');

            $table->index('member_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_accounts');
    }
};
