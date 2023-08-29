<?php

namespace app\modules\welding\controllers;

use Yii;
use app\models\Parameter;
use app\models\ParameterSearch;
use app\models\Welding;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ParameterController implements the CRUD actions for Parameter model.
 */
class ParameterController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [                    
                    // allow authenticated users
                    [
                        'allow' => true,
                        'actions' => ['create', 'get-weld-wps'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }

    public function actionCreate($EditId = "") {
        //################ Add #####################
        $model = new Parameter();
        //################ Edit #####################
        if(!empty($EditId)){
            $model = Yii::$app->general->getModelData('\app\models\Parameter',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']); 
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }
        //################ Save  #####################
        $model = Yii::$app->general->reportNo($model,'PAR');
        if ($model->load(Yii::$app->request->post())) {
            // $model = Yii::$app->anomaly->welding_param_anomaly($model,'\app\models\Parameter'); //as per client says anomaly section has been turn off
            if($model->validate() && $model->save()){
                $data = Yii::$app->general->UploadImg($model->id,'Parameter');
                if(!empty($EditId)){                
                    $Data = '';
                 }else{
                    $Data = Parameter::find()->where(['id'=>$model->id])->asArray()->one();
                 }
                echo json_encode(array('status'=>true,'modelData'=>$Data) );die;
            }else{
                echo json_encode(array('status'=>false,'message'=>$model->errors));die;
            }   
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionGetWeldWps(){
        $weldHtml = '<option value="">Please Select</option>';
        $result = array('wps'=>'', 'welders'=>$weldHtml);
        if(isset($_POST['kp']) && !empty($_POST['number'])){
            $weldDetails = Welding::find()->where(['kp'=>$_POST['kp'], 'weld_number'=>$_POST['number']])->active()->asArray()->one();
            if(!empty($weldDetails)){
                $details = Yii::$app->general->getTaxomonyData($weldDetails['WPS']);
                if(!empty($details)){
                    $welderArray = Yii::$app->weld->getWelders($weldDetails['WPS']);
                    if(!empty($welderArray)){
                        foreach($welderArray as $key => $welder){
                            $weldHtml .= '<option value="'.$key.'">'.$welder.'</option>';
                        }
                    }
                    $result = array('wps'=>$details['value'], 'welders'=>$weldHtml);
                }
            }
        }
        echo json_encode($result);
        die;
    }
}
