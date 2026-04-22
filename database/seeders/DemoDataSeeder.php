<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\SalesRep;
use App\Models\Doctor;
use App\Models\Showcase;
use App\Models\PointAccount;
use App\Models\PointTransaction;
use App\Models\PointProduct;
use App\Models\RedemptionOrder;
use App\Models\VerificationRecord;
use App\Enums\RedemptionOrderStatus;
use App\Enums\ProductStatus;
use App\Enums\MemberStatus;
use App\Enums\SalesRepStatus;
use App\Enums\DoctorStatus;
use App\Enums\ShowcaseStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. 创建 3 个销售
        $salesReps = [];
        $salesRepNames = ['张经理', '李经理', '王经理'];
        foreach ($salesRepNames as $i => $name) {
            $salesReps[] = SalesRep::create([
                'name' => $name,
                'phone' => '138000000' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'status' => SalesRepStatus::ACTIVE,
            ]);
        }

        // 2. 创建 3 个医生
        $doctors = [];
        $doctorData = [
            ['name' => '王医生', 'title' => '主任医师', 'intro' => '从事皮肤美容15年，擅长痘坑修复、疤痕治疗'],
            ['name' => '李医生', 'title' => '主治医师', 'intro' => '专注光电美肤，擅长光子嫩肤、黄金微针'],
            ['name' => '赵医生', 'title' => '执业医师', 'intro' => '擅长面部年轻化、注射美容'],
        ];
        foreach ($doctorData as $data) {
            $doctors[] = Doctor::create([
                'name' => $data['name'],
                'title' => $data['title'],
                'intro' => $data['intro'],
                'status' => DoctorStatus::ACTIVE,
            ]);
        }

        // 3. 创建 10 个案例
        $showcaseData = [
            ['doctor' => 0, 'title' => '痘坑修复案例', 'project' => '痘坑'],
            ['doctor' => 0, 'title' => '面部疤痕治疗', 'project' => '疤痕'],
            ['doctor' => 1, 'title' => '光子嫩肤体验', 'project' => '光电'],
            ['doctor' => 1, 'title' => '黄金微针祛斑', 'project' => '光电'],
            ['doctor' => 1, 'title' => '敏感肌修复', 'project' => '光电'],
            ['doctor' => 2, 'title' => '眼整形术后恢复', 'project' => '眼整形'],
            ['doctor' => 2, 'title' => '玻尿酸填充', 'project' => '注射'],
            ['doctor' => 0, 'title' => '痘坑二次治疗', 'project' => '痘坑'],
            ['doctor' => 1, 'title' => '色斑淡化治疗', 'project' => '光电'],
            ['doctor' => 2, 'title' => '面部轮廓调整', 'project' => '注射'],
        ];
        foreach ($showcaseData as $data) {
            Showcase::create([
                'doctor_id' => $doctors[$data['doctor']]->id,
                'title' => $data['title'],
                'media_type' => 'image',
                'project_name' => $data['project'],
                'status' => ShowcaseStatus::VISIBLE,
            ]);
        }

        // 4. 创建 8 个积分商品
        $products = [];
        $productData = [
            ['name' => '面部深层补水护理', 'category' => '皮肤护理', 'points' => 500, 'stock' => 20],
            ['name' => '光子嫩肤体验券', 'category' => '光电项目', 'points' => 2000, 'stock' => 10],
            ['name' => '术后修复护理包', 'category' => '术后护理', 'points' => 800, 'stock' => 30],
            ['name' => '黄金微针体验', 'category' => '光电项目', 'points' => 3000, 'stock' => 5],
            ['name' => '医美会员礼品套装', 'category' => '会员礼品', 'points' => 1500, 'stock' => 15],
            ['name' => '皮肤检测仪检测', 'category' => '检测服务', 'points' => 300, 'stock' => 50],
            ['name' => '医用面膜套装', 'category' => '护肤产品', 'points' => 400, 'stock' => 3], // 低库存
            ['name' => '防晒护理套装', 'category' => '护肤产品', 'points' => 600, 'stock' => 0], // 库存为0
        ];
        foreach ($productData as $i => $data) {
            $products[] = PointProduct::create([
                'name' => $data['name'],
                'category' => $data['category'],
                'points_price' => $data['points'],
                'stock' => $data['stock'],
                'status' => $data['stock'] > 0 ? ProductStatus::LISTED : ProductStatus::DELISTED,
            ]);
        }

        // 5. 创建 20 个会员
        $members = [];
        $memberNames = [
            '会员A', '会员B', '会员C', '会员D', '会员E',
            '会员F', '会员G', '会员H', '会员I', '会员J',
            '会员K', '会员L', '会员M', '会员N', '会员O',
            '会员P', '会员Q', '会员R', '会员S', '会员T',
        ];
        foreach ($memberNames as $i => $name) {
            $member = Member::create([
                'name' => $name,
                'phone' => '139' . str_pad($i + 1, 8, '0', STR_PAD_LEFT),
                'gender' => $i % 2 === 0 ? '女' : '男',
                'sales_rep_id' => $salesReps[$i % 3]->id,
                'status' => MemberStatus::ACTIVE,
                'source' => ['线上推广', '朋友推荐', '老客介绍', '线下活动'][array_rand(['线上推广', '朋友推荐', '老客介绍', '线下活动'])],
            ]);
            $members[] = $member;

            // 6. 为每个会员创建积分账户并添加积分
            $pointAccount = PointAccount::create([
                'member_id' => $member->id,
                'balance' => rand(500, 5000),
                'total_earned' => rand(1000, 8000),
                'total_spent' => rand(100, 3000),
                'frozen_points' => 0,
            ]);

            // 7. 为部分会员添加积分流水
            $transactionTypes = ['earn', 'spend', 'refund'];
            for ($j = 0; $j < rand(1, 5); $j++) {
                $type = $transactionTypes[array_rand($transactionTypes)];
                $points = rand(100, 1000);
                $beforeBalance = $pointAccount->balance;
                
                if ($type === 'spend') {
                    $afterBalance = max(0, $beforeBalance - $points);
                } else {
                    $afterBalance = $beforeBalance + $points;
                }

                PointTransaction::create([
                    'member_id' => $member->id,
                    'point_account_id' => $pointAccount->id,
                    'type' => $type,
                    'points' => $points,
                    'before_balance' => $beforeBalance,
                    'after_balance' => $afterBalance,
                    'operator_name' => '系统',
                    'remark' => match ($type) {
                        'earn' => '新用户注册赠送',
                        'spend' => '兑换商品',
                        'refund' => '订单取消退还',
                        default => '其他',
                    },
                ]);

                // 更新账户余额
                $pointAccount->balance = $afterBalance;
                if ($type === 'earn') {
                    $pointAccount->total_earned += $points;
                } elseif ($type === 'spend') {
                    $pointAccount->total_spent += $points;
                }
                $pointAccount->save();
            }
        }

        // 8. 创建若干兑换订单
        $orderStatuses = [
            RedemptionOrderStatus::PENDING,
            RedemptionOrderStatus::VERIFIED,
            RedemptionOrderStatus::VERIFIED,
            RedemptionOrderStatus::VERIFIED,
            RedemptionOrderStatus::CANCELLED,
            RedemptionOrderStatus::REJECTED,
        ];

        for ($i = 0; $i < 15; $i++) {
            $member = $members[array_rand($members)];
            $product = $products[array_rand($products)];
            $status = $orderStatuses[array_rand($orderStatuses)];
            
            $order = RedemptionOrder::create([
                'order_no' => 'RD' . date('YmdHis') . Str::random(6),
                'member_id' => $member->id,
                'point_product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => 1,
                'unit_points' => $product->points_price,
                'total_points' => $product->points_price,
                'status' => $status,
                'receiver_name' => $member->name,
                'receiver_phone' => $member->phone,
            ]);

            // 如果已核销，创建核销记录
            if ($status === RedemptionOrderStatus::VERIFIED) {
                $order->completed_at = now()->subDays(rand(1, 10));
                $order->verify_sales_rep_id = $salesReps[array_rand($salesReps)]->id;
                $order->save();

                VerificationRecord::create([
                    'redemption_order_id' => $order->id,
                    'member_id' => $member->id,
                    'sales_rep_id' => $order->verify_sales_rep_id,
                    'verified_at' => $order->completed_at,
                    'remark' => '后台核销',
                ]);
            }

            // 如果已取消或已拒绝
            if (in_array($status, [RedemptionOrderStatus::CANCELLED, RedemptionOrderStatus::REJECTED])) {
                $order->cancelled_at = now()->subDays(rand(1, 5));
                $order->save();
            }
        }

        $this->command->info('演示数据创建完成！');
        $this->command->info('- 销售: ' . count($salesReps) . ' 个');
        $this->command->info('- 医生: ' . count($doctors) . ' 个');
        $this->command->info('- 案例: ' . Showcase::count() . ' 个');
        $this->command->info('- 会员: ' . count($members) . ' 个');
        $this->command->info('- 积分商品: ' . count($products) . ' 个');
        $this->command->info('- 兑换订单: ' . RedemptionOrder::count() . ' 个');
    }
}