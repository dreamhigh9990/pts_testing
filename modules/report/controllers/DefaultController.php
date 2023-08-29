<?php

namespace app\modules\report\controllers;

use yii\web\Controller;
use yii;

/**
 * Default controller for the `report` module
 */
class DefaultController extends Controller
{
  
    public function actionWeldDropdown($kp,$type)
    {
        $Kplist = \yii\helpers\ArrayHelper::map(\app\models\Welding::find()->select('weld_number')->where(['kp'=>$kp])->active()->asArray()->all(),'weld_number','weld_number');
        if($Kplist){
            $html = '<label class="control-label" for="clearancereport-from_kp">'.Yii::$app->trans->getTrans('Weld Number').'</label>';
            $html .='<select  class="form-control" name="ClearanceReport['.$type.']" aria-required="true" aria-invalid="true">';
            foreach( $Kplist as $op){
                $html.='<option>'.$op.'</option>';
            }
            $html.='</select>';
            echo $html;
        }
        die;
    }
}
