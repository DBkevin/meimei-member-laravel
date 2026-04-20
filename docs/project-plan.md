# 项目阶段计划

## Stage 0：Laravel 12 + Filament 初始化

已完成：
- 安装 PHP 8.3.6
- 安装 Composer 2.7.1
- 安装 Laravel 12.12.2
- 安装 Filament 5.5.2
- 配置 SQLite 数据库
- 创建管理员用户

## Stage 1：核心模型和数据库迁移

待实现：
- 创建会员(Member)模型和迁移
- 创建积分账户(PointAccount)模型和迁移
- 创建积分流水(PointTransaction)模型和迁移
- 创建积分商品(PointProduct)模型和迁移
- 创建兑换订单(RedemptionOrder)模型和迁移

## Stage 2：会员管理 + 自动创建积分账户

待实现：
- 会员列表页面
- 会员详情页面
- 新增会员时自动创建积分账户

## Stage 3：手动加减积分 + 积分流水

待实现：
- 手动增加积分功能
- 手动扣减积分功能
- 积分流水记录查询

## Stage 4：积分商品管理

待实现：
- 积分商品 CRUD
- 商品上下架

## Stage 5：兑换订单事务

待实现：
- 积分兑换商品
- 订单状态管理
- 库存扣减事务

## Stage 6：Dashboard

待实现：
- 会员统计
- 积分统计
- 订单统计

## Stage 7：会员导入

待实现：
- Excel 批量导入会员

## Stage 8：导出

待实现：
- 会员导出
- 积分流水导出
- 订单导出

## Stage 9：从 GVA v1 迁移数据

待实现：
- 数据迁移脚本
- 数据校验
