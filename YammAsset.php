<?php
/**
 * Created by PhpStorm.
 * User: bobroid
 * Date: 31.08.15
 * Time: 18:08
 */

namespace bobroid\yamm;


class YammAsset extends \yii\web\AssetBundle
{
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public $sourcePath = '@vendor/bobroid/yamm/assets/';

    public $css = [
        'css/style.css',
    ];

    public $js = [
        'js/main.js',
        'js/modernizr.js',
        'js/jquery.mobile.custom.min.js'
    ];
}