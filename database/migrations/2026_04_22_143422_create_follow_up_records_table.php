<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_up_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('sales_rep_id')->nullable()->constrained()->onDelete('set null');
            $table->string('type')->comment('跟进类型');
            $table->string('channel')->comment('跟进渠道');
            $table->text('content')->comment('跟进内容');
            $table->string('intention_level')->nullable()->comment('意向等级');
            $table->timestamp('next_follow_at')->nullable()->comment('下次跟进时间');
            $table->string('status')->default('pending')->comment('状态');
            $table->text('result')->nullable()->comment('跟进结果');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index('member_id');
            $table->index('sales_rep_id');
            $table->index('status');
            $table->index('next_follow_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_up_records');
    }
};
