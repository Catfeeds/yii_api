<?php

// 分类管理
namespace backend\modules\mall\service;

use yii\helpers\ArrayHelper;
use backend\modules\mall\models\Category;

class Goods_category extends \yii\db\ActiveRecord {
	
	// 获取栏目子集
	public function getchild($pid) {
		$child = Category::find ()->where ( [ 
				'parentid' => $pid 
		] )->asArray ()->all ();
		return $child;
	}
	// 根据catid获得所有子分类的catid//想法同过家族图谱来确定//效率较慢//获得方法？？？
	public static function getallchildcatid($catid) {
		$data = ArrayHelper::getColumn ( $categorys = Category::find ()->select ( 'catid' )->where ( 'parent_id_path like :parent_id_path', [ 
				':parent_id_path' => '%_' . $catid . '_%' 
		] )->all (), 'catid' );
		return $data;
	}
	// 获取父节点
	public static function get_father($myid) {
		$category = Category::find ()->select ( 'catid,name,level,parentid,parent_id_path' )->where ( [ 
				'catid' => $myid 
		] )->one ();
		return $category;
	}
	// 获取自身
	public static function get_my($myid) {
		return Category::find ()->where ( [ 
				'catid' => $myid 
		] )->one ();
	}
	// 获取一个子
	public static function get_my_child_api($myid) {
		return Category::find ()->select ( 'catid,name,level,parentid' )->where ( [ 
				'catid' => $myid 
		] )->one ();
	}
	// 根基pid获取子集
	public static function get_child_api($myid) {
		return Category::find ()->select ( 'catid,name,level,parentid' )->where ( [ 
				'parentid' => $myid 
		] )->all ();
	}
	// 根据level获取子集
	public static function get_child_level_api($myid) {
		return Category::find ()->select ( 'catid,name' )->where ( [ 
				'level' => $myid 
		] )->all ();
	}
}