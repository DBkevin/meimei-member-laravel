# Stage 1：大系统基础骨架 - 阶段验收文档

## 一、阶段目标

搭建医美私域运营与积分商城系统的后台基础骨架，核心目标是围绕：
- 客户管理
- 销售归属
- 医生案例
- 积分资产
- 商品兑换
- 订单核销
- 数据概览

## 二、已完成模块

### 1. 客户与客情模块
- **Member（会员）**：会员基本信息、归属销售、手机号唯一
- **SalesRep（销售）**：销售基本信息、关联后台账号

### 2. 医生与案例模块
- **Doctor（医生）**：医生信息、职称、头像、介绍
- **Showcase（案例）**：医生案例、媒体类型、项目名称

### 3. 积分资产模块
- **PointAccount（积分账户）**：会员积分余额、总获得、总消费、冻结积分
- **PointTransaction（积分流水）**：积分增减记录、账本类型

### 4. 积分商品与订单模块
- **PointProduct（积分商品）**：商品信息、积分价格、库存、状态
- **RedemptionOrder（兑换订单）**：订单信息、商品明细、核销状态

### 5. 核销记录模块
- **VerificationRecord（核销记录）**：订单核销信息、核销时间、核销人

## 三、已完成技术内容

### 1. 数据库 Migration（9个）
- members
- sales_reps
- doctors
- point_accounts
- point_products
- point_transactions
- showcases
- redemption_orders
- verification_records

### 2. Eloquent 模型与关系
- Member: belongsTo SalesRep, hasOne PointAccount, hasMany PointTransaction, hasMany RedemptionOrder
- SalesRep: hasMany Member
- Doctor: hasMany Showcase
- Showcase: belongsTo Doctor
- PointAccount: belongsTo Member
- PointTransaction: belongsTo Member, belongsTo PointAccount
- PointProduct: hasMany RedemptionOrder
- RedemptionOrder: belongsTo Member, belongsTo PointProduct, belongsTo SalesRep (verifier)
- VerificationRecord: belongsTo RedemptionOrder, belongsTo Member, belongsTo SalesRep

### 3. Enum/常量（8个）
- MemberStatus: 1启用，2禁用
- SalesRepStatus: 1启用，2禁用
- DoctorStatus: 1启用，2禁用
- ShowcaseStatus: 1展示，2隐藏
- ProductStatus: 1上架，2下架
- RedemptionOrderStatus: pending/completed/cancelled
- PointTransactionType: earn/spend/adjust/refund
- MediaType: image/video

### 4. Filament Resource 后台页面（9个）
- MemberResource：会员管理（列表、编辑、查看）
- SalesRepResource：销售管理
- DoctorResource：医生管理
- ShowcaseResource：案例管理
- PointAccountResource：积分账户
- PointTransactionResource：积分流水（只读）
- PointProductResource：商品管理
- RedemptionOrderResource：兑换订单
- VerificationRecordResource：核销记录

### 5. 后台菜单结构
- 会员管理
- 销售管理
- 医生管理
  - 案例管理
- 积分账户
- 积分流水
- 商品管理
- 兑换订单
- 核销记录

### 6. 自动化逻辑
- 创建 Member 时自动创建 PointAccount

### 7. 测试
- CoreModelRelationshipTest.php：11个测试用例

## 四、验证结果

| 验证项 | 结果 |
|--------|------|
| php artisan migrate:fresh | 通过 |
| php artisan test | 13个测试通过 |
| npm run build | 构建成功 |

## 五、当前系统定位

**医美私域运营与积分商城系统 - 后台管理系统**

技术栈：
- Laravel 12
- Filament 5
- SQLite（开发环境）

## 六、暂不做内容

- 微信登录
- 小程序端
- 视频面诊
- TRTC 实时音视频
- 拼团
- T+1 积分导入
- 复杂营销活动
- 支付
- 短信
- 复杂权限
- 部署
- 数据迁移（GVA）
- Excel 导入导出
- 真实扫码核销

## 七、下一阶段建议

### Stage 2：积分兑换业务闭环

**目标**：实现完整的积分兑换业务流程

**待实现功能**：
1. 会员创建后自动生成积分账户（已完成）
2. 积分流水可以影响积分账户余额
3. 兑换商品时校验积分和库存
4. 兑换成功后扣减积分和库存
5. 订单支持状态流转
6. 订单核销后生成核销记录
7. 为以上核心业务逻辑补充测试

---
**文档创建时间**：2026-04-22
**阶段状态**：已完成
