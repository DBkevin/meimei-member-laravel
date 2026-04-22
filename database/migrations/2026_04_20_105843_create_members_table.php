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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('姓名');
            $table->string('phone')->unique()->comment('手机号');
            $table->string('gender')->nullable()->comment('性别');
            $table->date('birthday')->nullable()->comment('生日');
            $table->string('crm_archive_no')->nullable()->comment('CRM档案号');
            $table->string('source')->nullable()->comment('来源渠道');
            $table->string('level')->nullable()->comment('会员等级');
            $table->tinyInteger('status')->default(1)->comment('状态：1启用，2禁用');
            $table->foreignId('sales_rep_id')->nullable()->constrained('sales_reps')->nullOnDelete()->comment('归属销售');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();

            $table->index('phone');
            $table->index('sales_rep_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};

