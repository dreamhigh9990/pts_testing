<?php

namespace app\modules\civil\controllers;

use Yii;
use app\models\Reinstatement;
use app\models\ReinstatementSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReinstatementController implements the CRUD actions for Reinstatement model.
 */
class ReinstatementController extends Controller
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
                        'actions' => ['create','delete'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    public function actionCreate($EditId="")
    {
		//################ Add #####################
         $model = new Reinstatement();
		//################ Edit #####################
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Reinstatement',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']);
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }
		//################ Save  #####################
        $model = Yii::$app->general->reportNo($model,'RIN');
        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post();
            if(isset($postData['Reinstatement']['check_points']) && $postData['Reinstatement']['check_points'] == ''){
                $model->check_points = '';
            } else {
                $model->check_points = $postData['Reinstatement']['check_points'];
            }
            // $model = Yii::$app->anomaly->civil_reinstatement_anomaly($model,'\app\models\Reinstatement'); //as per client says anomaly section has been turn off      
            if($model->validate() && $model->save()){               
                $data = Yii::$app->general->UploadImg($model->id,'Reinstatement');
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                    $Data = Reinstatement::find()->where(['id'=>$model->id])->asArray()->one(); 
                }              
                echo json_encode(array('status'=>true,'modelData'=>$Data) );die;
            }else{
               // print_r($model->errors);die;
               echo json_encode(array('status'=>false,'message'=>$model->errors));die;
            }   
        }
       //################ Render to view #####################
        return $this->render('create', [
            'model' => $model,
        ]);
    }   
}
