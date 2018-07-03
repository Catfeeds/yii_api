<?php
/*
 *
use common\widgets\FirstWidget;
?>
<?= FirstWidget::widget() ?>
*/
namespace common\widgets;
use yii\base\Widget;

class FirstWidget extends Widget{
    public $mes;
    public $wife;

    public function init()
    {
        parent::init();
        if ($this->mes === null) {
            $this->mes = '第一个Widget';
        }
    }

    public function run()
    {
        return "<h1>$this->mes</h1> $this->wife";
    }
}