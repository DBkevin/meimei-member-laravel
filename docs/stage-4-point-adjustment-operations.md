# Stage 4：积分获取与人工运营闭环

## 本阶段目标

补齐"积分从哪里来"的后台运营闭环，让运营人员可以在后台给会员发放积分、扣减积分、退还积分、冻结积分、解冻积分，并且所有操作都要生成积分流水。

## 新增服务说明

### PointAdjustmentService

位置：app/Services/PointAdjustmentService.php

提供以下方法：

| 方法 | 说明 |
|------|------|
| earn() | 发放积分 |
| spend() | 扣减积分 |
| refund() | 退还积分 |
| freeze() | 冻结积分 |
| unfreeze() | 解冻积分 |

所有方法都使用数据库事务保证数据一致性。

## 积分原因/来源说明

### PointTransactionReason 枚举

位置：app/Enums/PointTransactionReason.php

包含：到店奖励、消费奖励、老客复购、转介绍奖励、活动奖励、手动调整、订单取消退还、兑换扣减、系统修正

## 会员详情页优化说明

位置：app/Filament/Resources/MemberResource/Pages/ViewMember.php

### 积分操作按钮
- 发放积分
- 扣减积分
- 退还积分
- 冻结积分
- 解冻积分

### 积分账户信息展示
- 可用积分
- 冻结积分
- 累计获得
- 累计消耗

### 最近积分流水
展示最近 10 条积分流水记录

## 测试覆盖情况

位置：tests/Feature/PointAdjustmentWorkflowTest.php

覆盖 12 个测试场景

## 验证结果

| 验证项 | 结果 |
|--------|------|
| php artisan migrate:fresh --seed | 通过 |
| php artisan test | 26 个测试通过 |
| npm run build | 构建成功 |

## 下一阶段建议

1. 自动积分获取渠道（签到、邀请、积分规则）
2. 订单物流信息
3. 数据统计与报表
4. 消息通知
