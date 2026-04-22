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
        Schema::create('verification_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('redemption_order_id')->constrained('redemption_orders')->cascadeOnDelete()->comment('兑换订单');
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete()->comment('会员');
            $table->foreignId('sales_rep_id')->nullable()->constrained('sales_reps')->nullOnDelete()->comment('销售');
            $table->timestamp('verified_at')->comment('核销时间');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();

            $table->index('redemption_order_id');
            $table->index('member_id');
            $table->index('sales_rep_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_records');
    }
};
