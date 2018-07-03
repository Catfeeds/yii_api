<?php
namespace backend\modules\rabc\controllers;

use backend\modules\rabc\models\Authority;
use backend\modules\rabc\models\Role;
use backend\modules\ucenter\models\CardUser;
use backend\modules\ucenter\models\VipCard;
use backend\modules\ucenter\service\VipCardService;
use common\extensions\Wechat\WechatMedia;
use Yii;
use backend\base\BaseController;
use backend\modules\restaurant\service\CategoryService;
use common\extensions\Wechat\WechatCard;

/**
 * CategoryController implements the CRUD actions for Category model.
 * 会员卡的增删改查
 */
class FindController extends BaseController
{

    // 格式化内容输出
    public function dd($arr)
    {
        echo "<pre>";
        var_dump($arr);
        echo "</pre>";
    }

    //api获取角色和权限
    public function  actionMyRoleAuth()
    {
        $user = $this->getUser();

        $roles = $user->hasMany(Role::className(), ['id' => 'role_id'])
            ->viaTable('pre_admin_user_role', ['user_id' => 'id'])->all();

        foreach ($roles as &$item)
        {

            $authoritys = $item->hasMany(Authority::className(), ['id' => 'authority_id'])
                ->viaTable('pre_admin_role_authority', ['role_id' => 'id'])->asArray()->all();

            $item=$item->toArray();
            $item['authoritys']=$authoritys;

        }

        return $this->jsonSuccess($roles);

    }


    //查看我的角色
    public function MyRole()
    {
        print_r(__DIR__);

        $user = $this->getUser();

        $role = $user->hasMany(Role::className(), ['id' => 'role_id'])
            ->viaTable('pre_admin_user_role', ['user_id' => 'id'])->select('id')->asArray()->all();

        $arr = [];
        foreach ($role as $item) {
            array_push($arr, $item['id']);
        }

        return $arr;
    }

    //查看权限对应的角色
    public function AuthorityRole()
    {

        $authority = Authority::find()->where(['id' => 1])->one();

        $role = $authority->hasMany(Role::className(), ['id' => 'role_id'])
            ->viaTable('pre_admin_role_authority', ['authority_id' => 'id'])->select('id')->asArray()->all();

        $arr = [];
        foreach ($role as $item) {
            array_push($arr, $item['id']);
        }

        return $arr;
    }

    public function actionTest()
    {

//        echo $module .'/'. $controller .'/'. $action;
//
        echo "<pre>";
        $request = \Yii::$app->request;

//        $id = $request->get('acc_token');
        if($request->isPost){
            $module = \Yii::$app->controller->module->id;
            $controller = \Yii::$app->controller->id;
            $action = \Yii::$app->controller->action->id;
            var_dump($module .'/'. $controller .'/'. $action) ;
        }


       $arr=$this->MyRole();
       $arr2=$this->AuthorityRole();

        $arr3 = array_intersect($arr, $arr2);
//        var_dump(array_unique($arr3));

    }

}
