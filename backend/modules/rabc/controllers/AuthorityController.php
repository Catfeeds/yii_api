<?php
namespace backend\modules\rabc\controllers;

use backend\modules\rabc\models\Authority;
use backend\modules\rabc\models\Role;
use backend\modules\rabc\models\RoleAuthority;
use backend\modules\ucenter\models\CardUser;
use backend\modules\ucenter\models\VipCard;
use backend\modules\ucenter\service\UserService;
use backend\modules\ucenter\service\VipCardService;
use common\extensions\Wechat\WechatMedia;
use Yii;
use backend\base\BaseController;
use backend\modules\restaurant\service\CategoryService;
use common\extensions\Wechat\WechatCard;
use yii\base\Exception;

/**
 * CategoryController implements the CRUD actions for Category model.
 * 权限的的增删改查
 */
class AuthorityController extends BaseController
{

    //api增加权限
    public function actionAdd()
    {
        $request_data = VipCardService::wl_validate(['name', 'router']);

        if ($request_data == "false") {

            return $this->jsonFail("参数缺失", "参数缺失");
        }

        $authority = new Authority();

        $authority->name = $request_data['name'];
        $authority->router = $request_data['router'];

        $isok = $authority->save();

        return $this->jsonSuccess($isok);

    }

    //api删除权限
    public function actionDelete()
    {
        $request_data = VipCardService::wl_validate(['id']);

        if ($request_data == "false") {

            return $this->jsonFail("参数缺失", "参数缺失");
        }

        $tr = Yii::$app->db->beginTransaction();

        try{

            $authority=Authority::find()->where(['id'=>$request_data['id']])->one();

            $authority->delete();

            $role_authority = Authority::find()->where(['id' => $request_data['id']])->one();

            $isok = RoleAuthority::deleteAll(['authority_id' => $request_data['id']]);

            $tr->commit();

        }catch (\yii\db\Exception $exception)
        {
            $tr->rollBack();
            return $this->jsonFail("删除失败!");
        }

        return $this->jsonSuccess("删除成功!");
    }


}
