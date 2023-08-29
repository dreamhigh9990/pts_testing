<?php

namespace app\modules\welding\controllers;

use Yii;
use app\models\Production;
use app\models\ProductionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductionController implements the CRUD actions for Production model.
 */
class ProductionController extends Controller
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
                    // everything else is denied
                ],
            ],
        ];
    }

    public function actionCreate($EditId = "") {
        //################ Add #####################
        $model = new Production();
        //################ Edit #####################
        if(!empty($EditId)){
            $model = Yii::$app->general->getModelData('\app\models\Production',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']); 
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
            $model->checkpoint = !empty($model->checkpoint)?json_decode($model->checkpoint,true):[];
        }

        if(!empty($_GET['download'])){
            $searchModel = new \app\models\ProductionSearch();
            $searchModel->download = 1;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }

        //################ Save  #####################
        $model = Yii::$app->general->reportNo($model,'CPR');
        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post();
            if(isset($postData['Production']['checkpoint']) && $postData['Production']['checkpoint'] == ''){
                $model->checkpoint = '';
            } else {
                $model->checkpoint = $postData['Production']['checkpoint'];
            }
            $model = Yii::$app->anomaly->welding_production_anomaly($model,'\app\models\Production');
            if($model->validate() && $model->save()){
                $data = Yii::$app->general->UploadImg($model->id,'Production');
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                    $Data = Production::find()->where(['id'=>$model->id])->asArray()->one();
                }
                echo json_encode(array('status'=>true,'modelData'=>$Data) );die;
            }else{
                echo json_encode(array('status'=>false,'message'=>$model->errors));die;      
            }   
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
