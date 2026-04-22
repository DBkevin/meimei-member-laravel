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
        Schema::create('showcases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete()->comment('医生');
            $table->string('title')->comment('案例标题');
            $table->string('cover_url')->nullable()->comment('封面图');
            $table->string('media_type')->default('image')->comment('媒体类型：image/video');
            $table->string('media_url')->nullable()->comment('媒体地址');
            $table->text('content')->nullable()->comment('案例内容');
            $table->string('project_name')->nullable()->comment('项目名称');
            $table->tinyInteger('status')->default(1)->comment('状态：1展示，2隐藏');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes();

            $table->index('doctor_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showcases');
    }
};
