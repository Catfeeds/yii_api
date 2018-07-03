<?php
namespace backend\modules\mall\controllers;
use Yii;
use backend\modules\mall\models\Comment;
use backend\base\BaseController;
use yii\data\Pagination;
use yii\filters\VerbFilter;

/**
 * CommentController implements the CRUD actions for Comment model.
 */
class CommentController extends BaseController
{

    /**
     * @inheritdoc
     */
    public function behaviors ()
    {
        return [
                'verbs' => [
                        'class' => VerbFilter::className(),
                        'actions' => [
                                'delete' => [
                                        'POST'
                                ]
                        ]
                ]
        ];
    }

    /**
     * Lists all Comment models.
     * 
     * @return mixed
     */
    public function actionIndex ()
    {
        
        $where = $this->thewhere();
        $query = new Comment();
        $query = $query->find()->where($where);
        $pagination = new Pagination(
                [
                        'totalCount' => $query->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $list = $query->orderBy([
                'comment_id' => SORT_DESC
        ])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        return $this->jsonSuccessWithPage($list, $pagination);
    }

    // 通过goods_id 查看评价
    public function actionCommentwithgoods ()
    {
        
        $where = $this->thewhere();
        $query = new Comment();
        $query = $query->find();
        $goods_id = $this->getParam('goods_id');
        
        $pagination = new Pagination(
                [
                        'totalCount' => $query->where('goods_id=:goods_id', 
                                [
                                        ':goods_id' => $goods_id
                                ])->count(),
                        'defaultPageSize' => empty($this->getParam('per-page')) ? '20' : $this->getParam(
                                'per-page')
                ]);
        $pagination->setPage(
                empty($this->getParam('page')) ? '' : $this->getParam('page'));
        $list = $query->where('goods_id=:goods_id', [
                ':goods_id' => $goods_id
        ])
            ->orderBy([
                'comment_id' => SORT_DESC
        ])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        return $this->jsonSuccessWithPage($list, $pagination);
    }

    // 通过commentid查询具体评论
    public function actionView ()
    {
        
        $where = $this->thewhere();
        $query = new Comment();
        $query = $query->find()->where($where);
        $comment_id = $this->getParam('comment_id');
        
        $comment = $query->where('comment_id=:comment_id', 
                [
                        ':comment_id' => $comment_id
                ])->one();
        return $this->jsonSuccess([
                'comment' => $comment
        ]);
    }

    public static function thewhere ()
    {
        $where = '';
        $deliver_rank = Yii::$app->request->get('deliver_rank');
        $goods_rank = Yii::$app->request->get('goods_rank');
        $service_rank = Yii::$app->request->get('service_rank');
        
        if (! empty($deliver_rank)) {
            $where .= '&deliver_rank = ' . $deliver_rank;
        }
        if (! empty($goods_rank)) {
            $where .= '&goods_rank = ' . $goods_rank;
        }
        if (! empty($service_rank)) {
            $where .= '&service_rank = ' . $service_rank;
        }
        
        return substr($where, 1);
    }
}
