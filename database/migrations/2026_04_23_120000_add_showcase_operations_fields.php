<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{ public function up(): void { Schema::table('showcases', function (Blueprint $table) {
    $table->foreignId('member_id')->nullable()->after('doctor_id')->constrained()->nullOnDelete();
    $table->foreignId('sales_rep_id')->nullable()->after('member_id')->constrained()->nullOnDelete();
    $table->string('project_type')->nullable()->after('project_name');
    $table->string('authorization_status')->default('pending')->after('sort');
    $table->string('content_status')->default('draft')->after('authorization_status');
    $table->boolean('is_featured')->default(false)->after('content_status');
    $table->boolean('is_public')->default(true)->after('is_featured');
    $table->boolean('usable_for_wechat')->default(false)->after('is_public');
    $table->boolean('usable_for_article')->default(false)->after('usable_for_wechat');
    $table->boolean('usable_for_xiaohongshu')->default(false)->after('usable_for_article');
    $table->string('before_after_type')->nullable()->after('usable_for_xiaohongshu');
    $table->json('tags')->nullable()->after('before_after_type');
    $table->text('remark')->nullable()->after('tags');
}); }
    public function down(): void { Schema::table('showcases', function (Blueprint $table) {
    $table->dropForeign(['member_id']); $table->dropForeign(['sales_rep_id']);
    $table->dropColumn(['member_id', 'sales_rep_id', 'project_type', 'authorization_status', 'content_status', 'is_featured', 'is_public', 'usable_for_wechat', 'usable_for_article', 'usable_for_xiaohongshu', 'before_after_type', 'tags', 'remark']);
}); }
};
