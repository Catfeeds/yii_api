<?php
namespace api\modules\v1\models;

use Yii;
use yii\base\Model;
use backend\modules\content\models\Position_data;
/**
 * Login form
 */
class IndexForm extends Model
{
    public $focusid;
    public $headid;

    public function rules()
    {
        return [];
        return [
            [['headid', 'focusid'], 'required'],
        ];
    }

    public function getpositiondata($posid)
    {
        if($this->validate())
        {
            return Position_data::getPositiondata($posid);
        }else{
            return null;
        }
    }
}
