<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
	
	//	"https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900",
		'../web/css/font.css',
		"../web/app-assets/fonts/feather/style.min.css",
		"../web/app-assets/css/jquery-ui.css",
		"../web/app-assets/fonts/simple-line-icons/style.css",
		"../web/app-assets/fonts/font-awesome/css/font-awesome.min.css",
		"../web/app-assets/vendors/css/perfect-scrollbar.min.css",
		"../web/app-assets/vendors/css/prism.min.css",
		"../web/app-assets/css/app.css",
		"../web/app-assets/css/pickadate.css",
		"../web/css/groovy.css",
		'../web/css/sweetalert/sweetalert.css',
		'../web/app-assets/vendors/lightbox/dist/css/lightbox.min.css',
		'../web/app-assets/css/select2.min.css',
		'../web/app-assets/vendors/css/toastr.css',
		'../web/css/responsive.css'
    ];	
    public $js = [
		'../web/app-assets/vendors/js/core/popper.min.js',
		'../web/app-assets/js/jquery-ui.js',
		'../web/app-assets/vendors/js/core/bootstrap.min.js',
		'../web/app-assets/vendors/js/perfect-scrollbar.jquery.min.js',
		'../web/app-assets/vendors/js/prism.min.js',
		'../web/app-assets/vendors/js/jquery.matchHeight-min.js',
		'../web/app-assets/vendors/js/screenfull.min.js',
		'../web/app-assets/vendors/js/pace/pace.min.js',
		'../web/app-assets/js/app-sidebar.js',
		'../web/app-assets/js/notification-sidebar.js',
		'../web/app-assets/js/customizer.js',		
		'../web/app-assets/js/picker.js',
		'../web/app-assets/js/picker.date.js',	
		'../web/app-assets/js/legacy.js',
		'../web/app-assets/vendors/lightbox/dist/js/lightbox.min.js',
		'../web/js/validate.min.js',
		'../web/js/groovy.js?t=f34534534',
		'../web/js/sweetalert/sweetalert.min.js',
		'../web/js/locationpicker.jquery.min.js',
		"../web/app-assets/js/select2.min.js",
		'../web/app-assets/vendors/js/toastr.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
