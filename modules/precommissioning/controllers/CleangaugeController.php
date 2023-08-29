<?php

namespace app\modules\precommissioning\controllers;

use Yii;
use app\models\Cleangauge;
use app\models\CleangaugeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CleangaugeController implements the CRUD actions for Cleangauge model.
 */
class CleangaugeController extends Controller
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
         $model = new Cleangauge();
		//################ Edit #####################
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Cleangauge',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']);
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }
        //################ Save  #####################
        
        $model = Yii::$app->general->reportNo($model,'CLG');
        if ($model->load(Yii::$app->request->post())) {
            $model = Yii::$app->anomaly->precom_cleangauge_anomaly($model,'\app\models\Cleangauge');
            if($model->validate() && $model->save()){  
                $data = Yii::$app->general->UploadImg($model->id,'Cleangauge');
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                    $Data = Cleangauge::find()->where(['id'=>$model->id])->asArray()->one();    
                }           
                echo json_encode(array('status'=>true,'modelData'=>$Data) );die;
            }else{
                echo json_encode(array('status'=>false,'message'=>$model->errors) );die;
            }   
        }
       //################ Render to view #####################
        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
