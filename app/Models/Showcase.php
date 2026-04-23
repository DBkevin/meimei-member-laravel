<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; use Illuminate\Database\Eloquent\SoftDeletes;
class Showcase extends Model { use SoftDeletes; protected $table = 'showcases'; protected $fillable = ['doctor_id', 'member_id', 'sales_rep_id', 'title', 'cover_url', 'media_type', 'media_url', 'content', 'project_name', 'project_type', 'status', 'sort', 'authorization_status', 'content_status', 'is_featured', 'is_public', 'usable_for_wechat', 'usable_for_article', 'usable_for_xiaohongshu', 'before_after_type', 'tags', 'remark']; protected $casts = ['status' => 'integer', 'sort' => 'integer', 'is_featured' => 'boolean', 'is_public' => 'boolean', 'usable_for_wechat' => 'boolean', 'usable_for_article' => 'boolean', 'usable_for_xiaohongshu' => 'boolean', 'tags' => 'array'];
    public function doctor(): BelongsTo { return $this->belongsTo(Doctor::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function salesRep(): BelongsTo { return $this->belongsTo(SalesRep::class, 'sales_rep_id'); }
}
