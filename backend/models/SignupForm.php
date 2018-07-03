<?php
/**
 * @author Jason
 */

namespace backend\models;

use yii\base\Model;
use common\models\Admin;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $mobile;
    public $code;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['mobile', 'filter', 'filter' => 'trim'],
            ['mobile', 'required'],
            ['mobile', 'unique', 'targetClass' => '\common\models\Admin', 'message' => '用户名已经存在'],
            ['mobile', 'string', 'min' => 2, 'max' => 255],
            ['password', 'required','on' => 'signup'],
            ['password', 'string', 'min' => 6,'on' => 'signup'],
        ];
    }


    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new Admin();
        $user->mobile = $this->mobile;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        return $user->save() ? $user : $user->getErrors();
    }

    public function attributeLabels()
    {

    }
}
