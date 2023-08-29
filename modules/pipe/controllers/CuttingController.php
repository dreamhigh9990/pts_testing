<?php

namespace app\modules\pipe\controllers;

use Yii;
use app\models\Cutting;
use app\models\CuttingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CuttingController implements the CRUD actions for Cutting model.
 */
class CuttingController extends Controller
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
                        'actions' => ['create', 'delete', 'check-duplicate-cut', 'get-new-pipes'],
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
         $model = new Cutting();
		//################ Edit #####################
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Cutting',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']); 
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }
		//################ Save  #####################
        $model = Yii::$app->general->reportNo($model,'CUT');
        if ($model->load(Yii::$app->request->post())) {
            $model = Yii::$app->anomaly->pipe_cutting_anomaly($model);
            if($model->validate() && $model->save()) { 
                Yii::$app->general->UploadImg($model->id,'Cutting');  
                 if(!empty($EditId)){                
                    $Data = '';
                 }else{
                     $Data = Cutting::find()->where(['id'=>$model->id])->asArray()->one(); 
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

    public function actionCheckDuplicateCut(){
        $result = array('status' => false);
        if(!empty($_POST['number'])){
            $pipeNumber = $_POST['number'];
            $checkCutDataExist = Cutting::find()->where(['pipe_number' => $pipeNumber])->asArray()->one();
            if(!empty($checkCutDataExist)){
                $result = array('status' => true);
            }
        }

        echo json_encode($result);
        die;
    }

    public function actionGetNewPipes(){
        $result['status'] = false;
        if(!empty($_POST['pipe_number'])){
            $pipeNumber = $_POST['pipe_number'];
            $pipesAfterCutting = Cutting::pipeNumbersAfterCut($pipeNumber);

            $result['status'] = true;
            $result['pipes'] = $pipesAfterCutting;
        }
        echo json_encode($result);
        die;
    }
}
