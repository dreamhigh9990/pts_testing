<?php

namespace app\modules\pipe\controllers;

use Yii;
use app\models\Reception;
use app\models\ReceptionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
class ReceptionController extends Controller
{
   
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

        $model = new Reception();
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Reception',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']); 
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        } 

        if(!empty($_GET['download'])){
            $searchModel = new \app\models\ReceptionSearch();
            $searchModel->download = 1;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }
        
        $model = Yii::$app->general->reportNo($model,'REC');
        if ($model->load(Yii::$app->request->post())) {
            $model = Yii::$app->anomaly->pipe_reception_anomaly($model);
            if($model->validate() && $model->save()) { 

                $PipeDefects = !empty($_POST['Pipe']['defects'])  ? $_POST['Pipe']['defects'] : "";
                Yii::$app->general->savePipeDefect($PipeDefects,$model->pipe_number);

                Yii::$app->general->UploadImg($model->id,'Reception');  
                if(!empty($EditId)){                
                    $Data = '';
                 }else{
                    $Data = Reception::find()->where(['id'=>$model->id])->asArray()->one(); 
                 }
                echo json_encode(array('status'=>true,'modelData'=>$Data));die;
            }else{
                echo json_encode(array('status'=>false,'message'=>$model->errors));die;
            }
        }


        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
