# Stage 6: 案例素材运营闭环

## 本阶段目标

把 Showcase 从基础 CRUD 模型升级为"案例素材运营资产"，实现：
- 授权管理
- 渠道用途管理
- 项目归类
- 医生关联
- 素材状态管理

## Showcase 字段设计

| 字段 | 类型 | 说明 |
|------|------|------|
| member_id | foreignId | 关联会员（可空） |
| sales_rep_id | foreignId | 关联销售（可空） |
| project_type | string | 项目类型 |
| authorization_status | string | 授权状态 |
| content_status | string | 素材状态 |
| is_featured | boolean | 重点案例 |
| is_public | boolean | 公开 |
| usable_for_wechat | boolean | 朋友圈可用 |
| usable_for_article | boolean | 公众号可用 |
| usable_for_xiaohongshu | boolean | 小红书可用 |
| before_after_type | string | 术前术后类型 |
| tags | json | 标签 |
| remark | text | 备注 |

## 枚举说明

### ShowcaseAuthorizationStatus (授权状态)
- `pending` - 待授权
- `authorized` - 已授权
- `rejected` - 已拒绝
- `expired` - 已过期
- `withdrawn` - 已撤回

### ShowcaseContentStatus (素材状态)
- `draft` - 草稿
- `editing` - 编辑中
- `ready` - 待发布
- `archived` - 已归档

### ShowcaseBeforeAfterType (术前术后类型)
- `before` - 术前
- `during` - 术中
- `after` - 术后
- `comparison` - 对比照

## 后台页面说明

### 案例管理
- 路径: `/admin/showcases`
- 功能: 列表、新建、编辑、查看
- 筛选: 项目类型、医生、授权状态、素材状态、渠道用途、重点案例

### 会员详情页
- 路径: `/admin/members/{id}`
- 功能: RelationManager 展示该会员关联的案例素材

### 医生详情页
- 路径: `/admin/doctors/{id}`
- 功能: RelationManager 展示该医生关联的案例素材

## Dashboard 新增统计

- 案例总数
- 已授权案例数
- 可朋友圈使用案例数
- 可公众号使用案例数
- 可小红书使用案例数
- 重点案例数
- 最近新增案例

## DemoDataSeeder 更新

新增 `createShowcases` 方法:
- 生成 30 条案例素材
- 覆盖 6 种项目类型: 祛痘、痘坑、疤痕、光子嫩肤、黄金微针、眼整形
- 覆盖不同授权状态、素材状态、渠道用途
- 部分案例关联医生、会员、销售

## 测试覆盖

`tests/Feature/ShowcaseWorkflowTest.php`:

1. ✅ 可以创建案例素材
2. ✅ 案例素材可以关联医生
3. ✅ 案例素材可以关联会员
4. ✅ 案例素材可以设置授权状态
5. ✅ 案例素材可以设置渠道用途
6. ✅ Dashboard 可以统计已授权案例数
7. ✅ Dashboard 可以统计可朋友圈使用案例数
8. ✅ 医生详情页可以查询到案例
9. ✅ 会员详情页可以查询到案例

## 验证结果

| 命令 | 结果 |
|------|------|
| `php artisan migrate:fresh --seed` | ✅ |
| `php artisan test` | ✅ |
| `npm run build` | ✅ |

## 下一阶段建议

1. 案例素材批量导入导出
2. 案例素材下载功能
3. 案例素材水印处理
4. 案例素材使用统计
5. 授权到期提醒
