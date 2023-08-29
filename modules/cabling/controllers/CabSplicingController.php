<?php

namespace app\modules\cabling\controllers;

use Yii;
use app\models\CabSplicing;
use app\models\CabSplicingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CabSplicingController implements the CRUD actions for CabSplicing model.
 */
class CabSplicingController extends Controller
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
        $model = new CabSplicing();
		//################ Edit #####################
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\CabSplicing',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']);
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
			$model->colour = !empty($model->colour)?json_decode($model->colour,true):[];
        }
        $model = Yii::$app->general->reportNo($model,'SLC');
		//################ Save  #####################
        if ($model->load(Yii::$app->request->post())) {
            $model = Yii::$app->anomaly->cable_splicing_anomaly($model);
            if(!empty($model->save())){
                $data = Yii::$app->general->UploadImg($model->id,'CabSplicing');
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                    $Data = CabSplicing::find()->where(['id'=>$model->id])->asArray()->one();
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
