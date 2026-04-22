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
        Schema::create('point_products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('商品名称');
            $table->string('cover_url')->nullable()->comment('封面图');
            $table->string('category')->nullable()->comment('分类');
            $table->integer('points_price')->comment('积分价格');
            $table->integer('stock')->default(0)->comment('库存');
            $table->tinyInteger('status')->default(1)->comment('状态：1上架，2下架');
            $table->integer('sort')->default(0)->comment('排序');
            $table->text('description')->nullable()->comment('描述');
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_products');
    }
};
