<?php

namespace app\modules\pipe\controllers;

use Yii;
use app\models\Stringing;
use app\models\StringingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StringingController implements the CRUD actions for Stringing model.
 */
class StringingController extends Controller
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
                        'actions' => ['create','kp-range'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }
 
    public function actionCreate($EditId = "") {
		//################ Add #####################
		$model = new Stringing();
		//################ Edit #####################
		if(!empty($EditId)){
            $model = Yii::$app->general->getModelData('\app\models\Stringing',$EditId);
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']); 
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }

        if(!empty($_GET['download'])){
            $searchModel = new \app\models\StringingSearch();
            $searchModel->download = 1;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }

		//################ Save  #####################
		$model = Yii::$app->general->reportNo($model,'STR');
        if ($model->load(Yii::$app->request->post())) {	
            $model = Yii::$app->anomaly->pipe_stringing_anomaly($model);	//as per client says anomaly section has been turn off
            if($model->validate() && $model->save()) {
                $data = Yii::$app->general->UploadImg($model->id,'Stringing');
                
                if(!empty($EditId)){                
                    $Data = '';
                 }else{
                    $Data = Stringing::find()->where(['id'=>$model->id])->asArray()->one();
                 }

                 $PipeDefects = !empty($_POST['Pipe']['defects'])  ? $_POST['Pipe']['defects'] : "";
                 Yii::$app->general->savePipeDefect($PipeDefects,$model->pipe_number);

                 echo json_encode(array('status'=>true,'modelData'=>$Data) );die;
            }else{
                echo json_encode(array('status'=>false,'message'=>$model->errors));die;
            }   
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
	public function actionKpRange() {        
        // as per client says, commenting below code
        // $result = array('status'=>false);
		// if(isset($_POST['kp'])){
        //     $kp = $_POST['kp'];
        //     $kpList = \app\models\Cleargrade::find()->where(['AND',['<=', 'start_kp', $kp],['>=', 'end_kp', $kp]])->active()->asArray()->all();
        //     if(!empty($kpList)){
        //         $result = array('status'=>true);		
        //     }            
        // }
        $result = array('status'=>true);
		echo json_encode($result);
		die;
	}
}
