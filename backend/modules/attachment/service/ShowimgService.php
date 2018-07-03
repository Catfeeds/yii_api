<?php
/**
 * @date        : 2018年1月29日
 */
namespace backend\modules\attachment\service;
use backend\modules\attachment\models\Attachment;

class ShowimgService
{

    public static function showmypic ($site_id, $offset, $limit)
    {
        return Attachment::find()->where(
                'site_id=:site_id and isimage = :isimage', 
                [
                        ':site_id' => $site_id,
                        ':isimage' => Attachment::STATUS_IS_IMAGE
                ])
            ->andWhere('status = :status', 
                [
                        ':status' => Attachment::STATUS_IS_SHOW
                ])
            ->orderBy('uploadtime DESC')
            ->offset($offset)
            ->limit($limit)
            ->all();
    }

    public static function mypiccount ($site_id)
    {
        return Attachment::find()->where(
                'site_id=:site_id and isimage = :isimage', 
                [
                        ':site_id' => $site_id,
                        ':isimage' => Attachment::STATUS_IS_IMAGE
                ])
            ->andWhere('status = :status', 
                [
                        ':status' => Attachment::STATUS_IS_SHOW
                ])
            ->count();
    }

    public static function showmypicwithcatid ($site_id, $catid, $name = '', 
            $offset, $limit)
    {
        return Attachment::find()->where(
                'site_id=:site_id and isimage = :isimage', 
                [
                        ':site_id' => $site_id,
                        ':isimage' => Attachment::STATUS_IS_IMAGE
                ])
            ->andFilterWhere([
                'catid' => $catid
        ])
            ->andFilterWhere([
                'like',
                'filename',
                $name
        ])
            ->andWhere('status = :status', 
                [
                        ':status' => Attachment::STATUS_IS_SHOW
                ])
            ->orderBy('uploadtime DESC')
            ->offset($offset)
            ->limit($limit)
            ->all();
    }

    public static function mypicwithcatidcount ($site_id, $catid, $name = '')
    {
        return Attachment::find()->where(
                'site_id=:site_id and isimage = :isimage', 
                [
                        ':site_id' => $site_id,
                        ':isimage' => Attachment::STATUS_IS_IMAGE
                ])
            ->andFilterWhere([
                'catid' => $catid
        ])
            ->andFilterWhere([
                'like',
                'filename',
                $name
        ])
            ->andWhere('status = :status', 
                [
                        ':status' => Attachment::STATUS_IS_SHOW
                ])
            ->count();
    }

    public static function showpic ($id)
    {
        return Attachment::find()->where('aid = :aid and isimage = :isimage', 
                [
                        ':aid' => $id,
                        ':isimage' => Attachment::STATUS_IS_IMAGE
                ])
            ->andWhere('status = :status', 
                [
                        ':status' => Attachment::STATUS_IS_SHOW
                ])
            ->one();
    }

    public static function updatepic ($id, $catid = '', $name = '')
    {
        $model = Attachment::find()->where('aid = :aid and isimage = :isimage',[':aid' => $id, ':isimage' => Attachment::STATUS_IS_IMAGE ])->andWhere('status = :status',[':status' => Attachment::STATUS_IS_SHOW])->one();
        
        if (empty($model)) {
            return false;
        }
        if (! empty($catid)) {
            $model->catid = $catid;
        }
        if (! empty($name)) {
            $model->filename = $name;
        }
        
        if ($model->save()) {
            return $model->save();
        } else {
            return false;
        }
    }

    public static function deletethis ($id)
    {
        $model = Attachment::updateAll([
                'status' => Attachment::STATUS_IS_DELETE
        ], [
                'aid' => $id
        ]);
        return ! empty($model);
    }
}


