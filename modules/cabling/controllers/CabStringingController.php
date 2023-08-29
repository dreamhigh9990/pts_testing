<?php

namespace app\modules\cabling\controllers;

use Yii;
use app\models\CabStringing;
use app\models\CabStringingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CabStringingController implements the CRUD actions for CabStringing model.
 */
class CabStringingController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    public function actionCreate($EditId="")
    {
    
		//################ Add #####################
         $model = new CabStringing();
		//################ Edit #####################
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\CabStringing',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']);
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
			$model->colour = !empty($model->colour)?json_decode($model->colour,true):[];
        }
        $model = Yii::$app->general->reportNo($model,'CAB');
		//################ Save  #####################
        if ($model->load(Yii::$app->request->post())) {
            $model = Yii::$app->anomaly->cable_stringing_anomaly($model);
            if(!empty($model->save())){
                $data = Yii::$app->general->UploadImg($model->id,'CabStringing');
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                    $Data = CabStringing::find()->where(['id'=>$model->id])->asArray()->one();
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
