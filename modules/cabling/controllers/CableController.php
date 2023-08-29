<?php

namespace app\modules\cabling\controllers;

use Yii;
use app\models\Cable;
use app\models\ReceptionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
class CableController extends Controller
{
   
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [                    
                    // allow authenticated users
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    public function actionCreate($EditId=""){        
        $model     = new Cable(); 
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Cable',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']);
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }
        if($model->load(Yii::$app->request->post())){
            $model = Yii::$app->anomaly->cable_anomaly($model,'\app\models\Cable');
            if($model->validate() && $model->save()) {
                echo json_encode(array('status'=>true,'modelData'=>'Your data has been saved.') );die;
            }else{
                echo json_encode(array('status'=>false,'message'=>$model->errors));die;
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
