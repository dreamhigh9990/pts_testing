<?php

namespace app\modules\admin\controllers;
use Yii;
use app\models\Line;
use app\models\LineSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
class LineController extends Controller
{
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [         
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
	public function actionCreate($EditId=""){
        $model = new Line();
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Line',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }        
        if ($model->load(Yii::$app->request->post())) {
			if($model->validate() && $model->save()){
				$data = Yii::$app->general->UploadImg($model->id,'Line');
				echo json_encode($data);die;
            }else{
                echo json_encode(array('status'=>false,'message'=>$model->errors));die;
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
