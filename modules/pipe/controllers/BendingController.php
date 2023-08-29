<?php

namespace app\modules\pipe\controllers;

use Yii;
use app\models\Bending;
use app\models\BendingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BendingController implements the CRUD actions for Bending model.
 */
class BendingController extends Controller
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
    public function actionCreate($EditId = "") {
		//################ Add #####################
		$model = new Bending();
		//################ Edit #####################
		if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Bending',$EditId);
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']); 
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }
		//################ Save  #####################
		$model = Yii::$app->general->reportNo($model,'BEN');
        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post();
            if(isset($postData['Bending']['bending_checkpoints']) && $postData['Bending']['bending_checkpoints'] == ''){
                $model->bending_checkpoints = '';
            } else {
                $model->bending_checkpoints = $postData['Bending']['bending_checkpoints'];
            }            
            $model = Yii::$app->anomaly->pipe_bending_anomaly($model);
            if( $model  && $model->validate() && $model->save()) {
                $data = Yii::$app->general->UploadImg($model->id,'Bending');
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                    $Data = Bending::find()->where(['id'=>$model->id])->asArray()->one();
                }

                $PipeDefects = !empty($_POST['Pipe']['defects'])  ? $_POST['Pipe']['defects'] : "";
                Yii::$app->general->savePipeDefect($PipeDefects,$model->pipe_number);

                echo json_encode(array('status'=>true,'modelData'=>$Data) );die;
            } else {
                echo json_encode(array('status'=>false,'message'=>$model->errors));die;
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
