<?php

namespace app\modules\civil\controllers;

use Yii;
use app\models\Lowering;
use app\models\LoweringSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LoweringController implements the CRUD actions for Lowering model.
 */
class LoweringController extends Controller
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
   

    /**
     * Creates a new Lowering model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
   
    public function actionCreate($EditId="")
    {
		//################ Add #####################
         $model = new Lowering();
		//################ Edit #####################
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Lowering',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']);
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }
		//################ Save  #####################
        $model = Yii::$app->general->reportNo($model,'LOW');
        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post();
            if(isset($postData['Lowering']['check_points']) && $postData['Lowering']['check_points'] == ''){
                $model->check_points = '';
            } else {
                $model->check_points = $postData['Lowering']['check_points'];
            } 
            // $model = Yii::$app->anomaly->civil_lowering_anomaly($model,'\app\models\Lowering'); //as per client says anomaly section has been turn off
            if($model->validate() && $model->save()){
                $data = Yii::$app->general->UploadImg($model->id,'Lowering');
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                    $Data = Lowering::find()->where(['id'=>$model->id])->asArray()->one();    
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
