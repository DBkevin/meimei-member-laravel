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
        Schema::create('sales_reps', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('姓名');
            $table->string('phone')->nullable()->comment('手机号');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('关联后台账号');
            $table->tinyInteger('status')->default(1)->comment('状态：1启用，2禁用');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_reps');
    }
};
