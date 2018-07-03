<?php

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'plugins/bootstrap/css/bootstrap.min.css',
        'plugins/dist/css/font-awesome.min.css',
        'plugins/dist/css/ionicons.min.css',
        'plugins/select2/select2.min.css',
        'plugins/dist/css/AdminLTE.css',
        'plugins/datepicker/datepicker3.css',
        'plugins/daterangepicker/daterangepicker-bs3.css',
        'plugins/timepicker/bootstrap-timepicker.min.css',
        'plugins/layui/css/layui.css',
        'plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css',
        'plugins/toastr/toastr.css',
        'plugins/nprogress/nprogress.css',
    ];
    public $js = [
        'plugins/jQuery/jQuery-2.2.0.min.js',
        'plugins/layui/layui.js',
//         'plugins/ueditor/ueditor.config.js',
//         'plugins/ueditor/ueditor.all.min.js',
//         'plugins/ueditor/lang/zh-cn/zh-cn.js',
        'plugins/nprogress/nprogress.js',
        'plugins/bootstrap/js/bootstrap.min.js',
        'plugins/select2/select2.full.js',
        'plugins/chartjs/Chart.min.js',
        'plugins/dist/js/moment.min.js',
        'plugins/daterangepicker/daterangepicker.js',
        'plugins/datepicker/bootstrap-datepicker.js',
        'plugins/timepicker/bootstrap-timepicker.min.js',
        'plugins/slimScroll/jquery.slimscroll.min.js',
        'plugins/fastclick/fastclick.min.js',
        'plugins/dist/js/app.js', //重要
        'plugins/toastr/toastr.min.js',
        'plugins/bootbox/bootbox.js',
        'plugins/pjax/jquery.pjax.js',
        'plugins/form/jquery.form.js',
        'plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js',
        'plugins/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js',
        'plugins/dist/js/wemall.js',
    ];
    public $depends = [
    ];

    //定义按需加载JS方法，注意加载顺序在最后
    public static function addScript($view, $jsfile) {
        $view->registerJsFile($jsfile,['depends' => AppAsset::className(),'position' => View::POS_HEAD]);
    }

    //定义按需加载css方法，注意加载顺序在最后
    public static function addCss($view, $cssfile) {
        $view->registerCssFile($cssfile,['depends' => AppAsset::className()]);
    }

}
