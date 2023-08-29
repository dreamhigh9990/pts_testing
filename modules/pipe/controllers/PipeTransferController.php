<?php

namespace app\modules\pipe\controllers;

use Yii;
use app\models\PipeTransfer;
use app\models\PipeTransferSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PipeTransferController implements the CRUD actions for PipeTransfer model.
 */
class PipeTransferController extends Controller
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
                    // everything else is denied
                ],
            ],
        ];
    }

    public function actionCreate($EditId="")
    { 
		//################ Add #####################
         $model = new PipeTransfer();
		//################ Edit #####################
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\PipeTransfer',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']); 
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }
		//################ Save  #####################
        $model = Yii::$app->general->reportNo($model,'TFR');
        if ($model->load(Yii::$app->request->post())) {
            $model = Yii::$app->anomaly->pipe_transfer_anomaly($model);       
            if($model->validate() && $model->save()) { 
                Yii::$app->general->UploadImg($model->id,'PipeTransfer');  
                 if(!empty($EditId)){                
                    $Data = '';
                 }else{
                    $Data = PipeTransfer::find()->where(['id'=>$model->id])->asArray()->one(); 
                 }

                 $PipeDefects = !empty($_POST['Pipe']['defects'])  ? $_POST['Pipe']['defects'] : "";
                 Yii::$app->general->savePipeDefect($PipeDefects,$model->pipe_number);

                echo json_encode(array('status'=>true,'modelData'=>$Data));die;
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
