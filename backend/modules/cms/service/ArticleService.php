<?php

/**
 * @date        : 2018年1月29日
 */
namespace backend\modules\cms\service;

use backend\modules\cms\models\Article;
use backend\modules\cms\models\ArticleData;
use backend\modules\admin\models\Admin;

class ArticleService {
	// 分页查看所有文章
	public static function showbysite($site_id, $offset, $limit) {
		return Article::find ()->where ( 'site_id = :site_id', [ 
				':site_id' => $site_id 
		] )->orderby ( 'created_at' )->offset ( $offset )->limit ( $limit )->all ();
	}
	// 所有文章数统计
	public static function showbysitecount($site_id) {
		return Article::find ()->where ( 'site_id = :site_id', [ 
				':site_id' => $site_id 
		] )->count ();
	}
	// 分类查看文章
	public static function showbycat($site_id, $cat_id, $offset, $limit) {
		return Article::find ()->where ( 'site_id = :site_id and cat_id = :cat_id', [ 
				':site_id' => $site_id,
				':cat_id' => $cat_id 
		] )->orderby ( 'created_at' )->offset ( $offset )->limit ( $limit )->all ();
	}
	// 分类文章统计
	public static function showbycatcount($site_id, $cat_id) {
		return Article::find ()->where ( 'site_id = :site_id and cat_id = :cat_id', [ 
				':site_id' => $cat_id,
				':cat_id' => $cat_id 
		] )->count ();
	}
	// 查看文章具体内容
	public static function showarticle($id) {
		return Article::getView ( $id );
	}
	// 新建文章
	public static function addarticle($data, $user_id, $site_id) {
		$author = Admin::showname ( $user_id );
		if (empty ( $author )) {
			return false;
		}
		$article = new Article ();
		$article->user_id = $user_id;
		$article->site_id = $site_id;
		$article->created_at = time ();
		$article->updated_at = time ();
		$article->author = $author->username;
		if ($article->load ( $data, '' ) && $article->save ()) {
			$id = $article->id;
		} else {
			return null;
		}
		$articledata = new ArticleData ();
		$articledata->id = $id;
		$articledata->content = $data ['content'];
		if ($articledata->load ( $data, '' ) && $articledata->save ()) {
			return $article;
		} else {
			$article->delete ();
			return null;
		}
	}
	// 修改文章
	public static function updatearticle($data, $user_id, $site_id) {
		$article = Article::getOne ( $data ['id'] );
		if ($article->user_id != $user_id) {
			return false;
		}
		$article->site_id = $site_id;
		$article->updated_at = time ();
		if ($article->load ( $data, '' ) && $article->save ()) {
			$id = $article->id;
		} else {
			return null;
		}
		$articledata = ArticleData::findOne ( [ 
				'id' => $id 
		] );
		$articledata->content = $data ['content'];
		if ($articledata->load ( $data, '' ) && $articledata->save ()) {
			return $article;
		} else {
			return null;
		}
	}
	// 删除文章
	public static function deletearticle($id) {
		$article = Article::getOne ( $id );
		$articledata = ArticleData::findOne ( [ 
				'id' => $id 
		] );
		if (empty ( $article )) {
			return false;
		}
		return ($article->delete () && $articledata->delete ());
	}
}


