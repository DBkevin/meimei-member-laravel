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
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete()->comment('会员');
            $table->foreignId('point_account_id')->constrained('point_accounts')->cascadeOnDelete()->comment('积分账户');
            $table->string('type')->comment('类型：earn/spend/adjust/refund');
            $table->integer('points')->comment('积分');
            $table->integer('before_balance')->comment('变更前余额');
            $table->integer('after_balance')->comment('变更后余额');
            $table->string('ref_type')->nullable()->comment('关联类型');
            $table->unsignedBigInteger('ref_id')->nullable()->comment('关联ID');
            $table->unsignedBigInteger('operator_id')->nullable()->comment('操作人ID');
            $table->string('operator_name')->nullable()->comment('操作人姓名');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();

            $table->index('member_id');
            $table->index('point_account_id');
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
