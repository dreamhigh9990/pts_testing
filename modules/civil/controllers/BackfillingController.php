<?php

namespace app\modules\civil\controllers;

use Yii;
use app\models\Backfilling;
use app\models\BackfillingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BackfillingController implements the CRUD actions for Backfilling model.
 */
class BackfillingController extends Controller
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
         $model = new Backfilling();
		//################ Edit #####################
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Backfilling',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']);
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }
		//################ Save  #####################
        $model = Yii::$app->general->reportNo($model,'BAC');
        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post();
            // check points
            if(isset($postData['Backfilling']['check_points']) && $postData['Backfilling']['check_points'] == ''){
                $model->check_points = '';
            } else {
                $model->check_points = $postData['Backfilling']['check_points'];
            }

            // backfilling types
            if(isset($postData['Backfilling']['backfilling_type']) && $postData['Backfilling']['backfilling_type'] == ''){
                $model->backfilling_type = '';
            } else {
                $model->backfilling_type = $postData['Backfilling']['backfilling_type'];
            }
            // $model = Yii::$app->anomaly->civil_backfilling_anomaly($model,'\app\models\Backfilling'); //as per client says anomaly section has been turn off
            if($model->validate() && $model->save()){
                $data = Yii::$app->general->UploadImg($model->id,'Backfilling');
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                    $Data = Backfilling::find()->where(['id'=>$model->id])->asArray()->one();       
                }        
                echo json_encode(array('status'=>true,'modelData'=>$Data) );die;
            }else{
                echo json_encode(array('status'=>false,'message'=>$model->errors));die;
            }   
        }
       //################ Render to view #####################
        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
