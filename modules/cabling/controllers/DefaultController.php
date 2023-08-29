<?php

namespace app\modules\cabling\controllers;

use yii\web\Controller;
use Yii;
/**
 * Default controller for the `cabling` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionAutoList($drum_number,$fromStringing=0){
        echo json_encode(Yii::$app->general->drumAutoList($drum_number,$fromStringing));die;	
    }
}
