# 项目说明

## 概述

1. 当前项目是 Laravel 12 + Filament 重写版。
2. GVA v1 已冻结，只作为业务参考。
3. 当前只做后台管理系统。
4. 不做小程序、视频面诊、支付、短信、微信登录、预约。

## 后续核心模型

- Member
- PointAccount
- PointTransaction
- PointProduct
- RedemptionOrder

## 开发规范

1. 业务逻辑后续放 Service 层。
2. 后台页面用 Filament Resource。
3. 数据库结构用 migration。
4. 表单校验用 Form Request 或 Filament 表单规则。
5. 重要业务必须写测试。

## 开发流程

每阶段完成后执行：

- `php artisan test`
- `npm run build`

## 安全规范

1. 不提交 .env。
2. 不提交真实数据库密码、服务器 IP、密钥。
