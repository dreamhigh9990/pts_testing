<?php

namespace app\modules\precommissioning\controllers;

use Yii;
use app\models\Surveying;
use app\models\SurveyingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SurveyingController implements the CRUD actions for Surveying model.
 */
class SurveyingController extends Controller
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

    public function actionCreate($EditId="")
    {
		//################ Add #####################
         $model = new Surveying();
		//################ Edit #####################
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Surveying',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']);
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }
        //################ Save  #####################
        
        $model = Yii::$app->general->reportNo($model,'DCV');
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate() && $model->save()){
                $data = Yii::$app->general->UploadImg($model->id,'Surveying');
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                   $Data = Surveying::find()->where(['id'=>$model->id])->asArray()->one();                 
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
