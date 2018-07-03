<?php

/**
 * @date        : 2018年1月29日
 */
namespace api\modules\v1\service;

use api\modules\v1\models\FoodCar;
use backend\modules\restaurant\models\FoodSKU;
use api\modules\v1\models\FoodCarNum;
use backend\modules\restaurant\models\Food;
use backend\modules\restaurant\models\Tables;

class FoodCarService
{

    // 计算购物车价格
    // 不作为最终价格 前台展示价格
    public static function theprice($table_id)
    {
        return Tables::findOne([
            'id' => $table_id
        ])->price;
    }

    // 购物车添加商品
    public static function addcar($table_id, $user_id, $food_id, $num, $sku = '', $pro = '')
    {
        if (empty($table_id) || empty($food_id)) {
            return '参数不完整';
        }
        $tables = Tables::find()->where('id = :id', [
            ':id' => $table_id
        ])->one();
        if (empty($tables)) {
            return '未查询到桌位';
        }
        $site_id = $tables->site_id;
        
        // 判断是否是本店的菜品
        if (! empty($sku)) {
            $sku = FoodSKU::findOne(['food_id' => $food_id,'id' => $sku]);
            // 库存判断
            if (empty($sku)) {
                return '未查询到sku属性';
            } elseif ($sku->infinite_count == FoodSKU::NOT_INFINITE && $num > $sku->store_count) {
                return '库存不足';
            }
            // 转换sku的格式
            if (! empty($sku)) {
                $sku = $sku->id . "";
            }
        }
        if (! empty($pro)) {
            $pros = '';
            foreach ($pro as $v) {
                $pros .= $v['pro_id'] . '_';
                $pros .= $v['id'] . ";";
            }
        }
        $car = FoodCar::findOne([
            'table_id' => $table_id,
            'food_id' => $food_id,
            'sku' => $sku
        ]);
        $tables = Tables::find()->where('id = :id', [':id' => $table_id])->one();
        // 购物车中没有重复商品 创建购物车商品
        if (empty($car)) {
            $car = new FoodCar();
            $car->table_id = $table_id;
            $car->food_id = $food_id;
            $car->user_id = $user_id;
            $car->number = $num;
            $car->sku = $sku;
            if (! empty($pro)) {
                $car->pro = '';
                foreach ($pro as $v) {
                    $car->pro .= $v['pro_id'] . '_';
                    $car->pro .= $v['id'] . ";";
                }
                $car->pro = $pro;
            }
            $car->create_at = time();
        } else {
            $car->number += $num;
        }
        $thefood = Food::find()->select('cat_id,price,store_count,infinite_count')
            ->where('food_id = :food_id', [':food_id' => $food_id])
            ->andWhere('site_id = :site_id', [':site_id' => $site_id])
            ->one();
        if (empty($thefood)) {
            return '未查询到食品';
        } elseif (empty($sku) && $thefood->infinite_count == FoodSKU::NOT_INFINITE && $num > $thefood->store_count) {
            return '库存不足';
        }
        $car->cat_id = $thefood['cat_id'];
   
        $carfoodnum = FoodCarNum::findOne(['tableid' => $table_id,'catid' => $thefood['cat_id']]);
        if (empty($carfoodnum)) {
            $carfoodnum = new FoodCarNum();
            $carfoodnum->num = $num;
        } else {
            $carfoodnum->num += $num;
        }
        
        $carfoodnum->tableid = $table_id;
        $carfoodnum->catid = $thefood['cat_id'];
        $carfoodnum->save();
        $price = 0;
        if (empty($sku)) {
            $price = $thefood['price'];
        } else {
            $sku = FoodSKU::findOne(['id' => $sku]);
            $price = $sku->price;
        }
        
        $tables->price += $price * $num;
        
        if ($tables->save() && $car->save()) {
            return 'SUCCESS';
        } else {
            return '添加失败';
        }
    }

    // 购物车删除商品
    public static function deletecar($table_id, $user_id, $food_id, $num = 1, $sku = '', $pro = '')
    {
        $tables = Tables::find()->where('id = :id', [
            ':id' => $table_id
        ])->one();
        if (empty($tables)) {
            return false;
        }
        if (! empty($sku)) {
            $sku = FoodSKU::findOne([
                'food_id' => $food_id,
                'id' => $sku
            ]);
            if (! empty($sku)) {
                $sku = $sku->id . "";
            }
        }
        $car = FoodCar::findOne([
            'table_id' => $table_id,
            'user_id' => $user_id,
            'food_id' => $food_id,
            'sku' => $sku
        ]);
        if (empty($car)) {
            return false;
        }
        
        $thefood = Food::find()->select('cat_id,price')
            ->where('food_id = :food_id', [
            ':food_id' => $food_id
        ])->one();
        
        $price = 0;
        if (empty($sku)) {
            $price = $thefood['price'];
        } else {
            $sku = FoodSKU::findOne([
                'id' => $sku
            ]);
            $price = $sku->price;
        }
        
        if ($car->number <= $num) {
            $num = $car->number;
            $car->number = 0;
        } else {
            $car->number -= $num;
        }
        // 修改购物车分类数量
        
        $carfoodnum = FoodCarNum::findOne([
            'tableid' => $table_id,
            'catid' => $thefood['cat_id']
        ]);
        if (empty($carfoodnum)) {
            $carfoodnum = new FoodCarNum();
        }
        $carfoodnum->tableid = $table_id;
        if ($carfoodnum->num <= $num) {
            $carfoodnum->delete();
        } else {
            $carfoodnum->num -= $num;
            $carfoodnum->catid = $thefood['cat_id'];
            $carfoodnum->save();
        }
        
        $tables->price -= $price * $num;
        $tables->save();
        if ($car->number == 0) {
            return $car->delete();
        }
        return $car->save();
    }

    // 购物车的所有商品
    public static function showall($table_id)
    {
        return FoodCar::find()->where('table_id = :table_id', [
            ':table_id' => $table_id
        ])
            ->joinWith('food')
            ->joinWith('sku')
            ->asArray()
            ->all();
    }

    // 清空购物车
    public static function clearcar($table_id)
    {
        // 购物车价格清空
        $tables = Tables::findOne([
            'id' => $table_id
        ]);
        $tables->price = 0;
        $tables->save();
        // 清空购物车分类数量
        FoodCarNum::deleteAll([
            'tableid' => $table_id
        ]);
        return FoodCar::deleteAll([
            'table_id' => $table_id
        ]);
    }

    public static function getnum($table_id, $food_id)
    {
        return FoodCar::find(',num')->where([
            'table_id' => $table_id,
            'food_id' => $food_id
        ])->all();
    }
}


