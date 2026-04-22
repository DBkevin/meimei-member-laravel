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
        Schema::create('redemption_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique()->comment('订单号');
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete()->comment('会员');
            $table->foreignId('point_product_id')->constrained('point_products')->cascadeOnDelete()->comment('积分商品');
            $table->string('product_name')->comment('商品名称');
            $table->integer('quantity')->default(1)->comment('数量');
            $table->integer('unit_points')->comment('单价积分');
            $table->integer('total_points')->comment('总积分');
            $table->string('status')->default('pending')->comment('状态：pending/completed/cancelled');
            $table->string('receiver_name')->nullable()->comment('收货人姓名');
            $table->string('receiver_phone')->nullable()->comment('收货人电话');
            $table->foreignId('verify_sales_rep_id')->nullable()->constrained('sales_reps')->nullOnDelete()->comment('核销销售');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamp('completed_at')->nullable()->comment('完成时间');
            $table->timestamp('cancelled_at')->nullable()->comment('取消时间');
            $table->timestamps();

            $table->index('order_no');
            $table->index('member_id');
            $table->index('status');
            $table->index('verify_sales_rep_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redemption_orders');
    }
};
