<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class FollowUpRecord extends Model
{
    use SoftDeletes;
    protected $fillable = ['member_id', 'sales_rep_id', 'type', 'channel', 'content', 'intention_level', 'next_follow_at', 'status', 'result', 'created_by'];
    protected $casts = ['next_follow_at' => 'datetime'];
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function salesRep(): BelongsTo { return $this->belongsTo(SalesRep::class, 'sales_rep_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
