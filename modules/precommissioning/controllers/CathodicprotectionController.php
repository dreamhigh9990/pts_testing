<?php

namespace app\modules\precommissioning\controllers;

use Yii;
use app\models\Cathodicprotection;
use app\models\CathodicprotectionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CathodicprotectionController implements the CRUD actions for Cathodicprotection model.
 */
class CathodicprotectionController extends Controller
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
                ],
            ],
        ];
    }
    public function actionCreate($EditId = "") {
         //################ Add #####################
          $model = new Cathodicprotection();
         //################ Edit #####################
        if(!empty($EditId)){
            $model = Yii::$app->general->getModelData('\app\models\Cathodicprotection',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']);
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }
        //################ Save  #####################
        $model = Yii::$app->general->reportNo($model,'CAP');
        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post();
            if(isset($postData['Cathodicprotection']['check_points']) && $postData['Cathodicprotection']['check_points'] == ''){
                $model->check_points = '';
            } else {
                $model->check_points = $postData['Cathodicprotection']['check_points'];
            }
            $model = Yii::$app->anomaly->precom_cathodic_anomaly($model,'\app\models\Cathodicprotection');  
            if($model->validate() && $model->save()){
                $data = Yii::$app->general->UploadImg($model->id,'Cathodicprotection');
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                    $Data = Cathodicprotection::find()->where(['id'=>$model->id])->asArray()->one();
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
