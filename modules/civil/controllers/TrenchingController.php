<?php

namespace app\modules\civil\controllers;

use Yii;
use app\models\Trenching;
use app\models\TrenchingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrenchingController implements the CRUD actions for Trenching model.
 */
class TrenchingController extends Controller
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
         $model = new Trenching();
        
		//################ Edit #####################
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Trenching',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']);
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }
		//################ Save  #####################
        $model = Yii::$app->general->reportNo($model,'TCH');
        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post();
            // pre start
            if(isset($postData['Trenching']['pre_start']) && $postData['Trenching']['pre_start'] == ''){
                $model->pre_start = '';
            } else {
                $model->pre_start = $postData['Trenching']['pre_start'];
            }

            // during trenching
            if(isset($postData['Trenching']['during_trenching']) && $postData['Trenching']['during_trenching'] == ''){
                $model->during_trenching = '';
            } else {
                $model->during_trenching = $postData['Trenching']['during_trenching'];
            }
            // $model = Yii::$app->anomaly->civil_trenching_anomaly($model,'\app\models\Trenching'); //as per client says anomaly section has been turn off
            if($model->validate() && $model->save()){             
                $data = Yii::$app->general->UploadImg($model->id,'Trenching');
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                   $Data = Trenching::find()->where(['id'=>$model->id])->asArray()->one();  
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
