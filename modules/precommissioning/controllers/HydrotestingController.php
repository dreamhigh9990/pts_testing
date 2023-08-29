<?php

namespace app\modules\precommissioning\controllers;

use Yii;
use app\models\Hydrotesting;
use app\models\HydrotestingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HydrotestingController implements the CRUD actions for Hydrotesting model.
 */
class HydrotestingController extends Controller
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
                        'actions' => ['create'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionCreate($EditId="")
    {
		//################ Add #####################
        $model = new Hydrotesting();
		//################ Edit #####################
        
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Hydrotesting',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']);
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }
        //################ Save  #####################
        
        $model = Yii::$app->general->reportNo($model,'HYD');
        if ($model->load(Yii::$app->request->post())) {
            $model = Yii::$app->anomaly->precom_hydrotesting_anomaly($model,'\app\models\Hydrotesting');
            if($model->validate() && $model->save()){  
                $data = Yii::$app->general->UploadImg($model->id,'Hydrotesting');
                if(!empty($EditId)){                
                    $Data = '';
                 }else{
                    $Data = Hydrotesting::find()->where(['id'=>$model->id])->asArray()->one();  
                 }                             
                 echo json_encode(array('status'=>true,'modelData'=>$Data) );die;
            }else{
                echo json_encode(array('status'=>false,'modelData'=>$model) );die;
            }   
        }
       //################ Render to view #####################
        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
