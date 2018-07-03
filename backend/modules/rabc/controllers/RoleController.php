<?php
namespace backend\modules\rabc\controllers;

use backend\modules\rabc\models\Authority;
use backend\modules\rabc\models\Role;
use backend\modules\rabc\models\UserRole;
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
class RoleController extends BaseController
{


    //api增加角色
    public function actionAdd()
    {
        $request_data = VipCardService::wl_validate(['name']);

        if ($request_data == "false") {

            return $this->jsonFail("参数缺失", "参数缺失");
        }

        $role = new Role();

        $role->name = $request_data['name'];

        $isok = $role->save();

        return $this->jsonSuccess("添加成功!");

    }

    //api删除角色
    public function actionDelete()
    {
        $request_data = VipCardService::wl_validate(['id']);

        if ($request_data == "false") {

            return $this->jsonFail("参数缺失", "参数缺失");
        }

        $tr = Yii::$app->db->beginTransaction();

        try{

            $role=Role::find()->where(['id'=>$request_data['id']])->one();

            $role->delete();

            Authority::find()->where(['id' => $request_data['id']])->one();

            UserRole::deleteAll(['role_id' => $request_data['id']]);

            $tr->commit();

        }catch (\yii\db\Exception $exception)
        {
            $tr->rollBack();
            return $this->jsonFail("删除失败!");
        }

        return $this->jsonSuccess("删除成功!");

    }


    //api 给角色分配权限
    public function actionSendAuthority()
    {

    }


    //api 给角色删除权限
    public function actionDeleteAuthority()
    {

    }

}
