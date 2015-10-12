<?php
/**
 * Created by PhpStorm.
 * User: bobroid
 * Date: 07.09.15
 * Time: 11:51
 */

namespace bobroid\yamm;


class blueThemeAsset extends \yii\web\AssetBundle{

    public $depends = [
        'bobroid\yamm\YammAsset',
    ];

    public $sourcePath = '@vendor/bobroid/yamm/assets/themes/blue';

    public $css = [
        'css/theme.css',
    ];

    public $js = [

    ];

}