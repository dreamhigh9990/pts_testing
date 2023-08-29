<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Line;
use app\models\Landowner;
use app\models\LandownerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LandownerController implements the CRUD actions for Landowner model.
 */
class LandownerController extends Controller
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
                        'actions' => ['create','get-geo-code','get-kp-list','delete'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }
    public function actionGetKpList(){
        $LineKpList = array();
        if(isset($_POST['kp']) && !empty($_POST['state'])){
           $LineKpList = \app\models\Line::find()->where(['LIKE',$_POST['state'],$_POST['kp']])->active()->asArray()->all();
        }
        echo json_encode($LineKpList);
        die;
    }
    public function actionCreate($EditId = "") {
		//################ Add #####################
        $model = new Landowner();
		//################ Edit #####################
		if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Landowner',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }
		//################ Save  #####################
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate() && $model->save()){
				$data = Yii::$app->general->UploadImg($model->id,'Landowner');
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
