<?php

namespace app\modules\pipe\controllers;

use Yii;
use app\models\Cleargrade;
use app\models\CleargradeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PipeCleargradeController implements the CRUD actions for PipeCleargrade model.
 */
class PipeCleargradeController extends Controller
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
                        'actions' => ['create','location-geo-code','delete','landowner'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }   
	
    public function actionCreate($EditId = "") {
		//################ Add #####################
        $model = new Cleargrade();
		//################ Edit #####################
		if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Cleargrade',$EditId);
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
            $postData = Yii::$app->request->post();
            if(isset($postData['Cleargrade']['check_points']) && $postData['Cleargrade']['check_points'] == ''){
                $model->check_points = '';
            } else {
                $model->check_points = $postData['Cleargrade']['check_points'];
            } 
            $model = Yii::$app->anomaly->pipe_cleargrade_anomaly($model);
            if($model->validate() && $model->save()){
				$data = Yii::$app->general->UploadImg($model->id,'Cleargrade');
				echo json_encode($data);die;
            }else{
                echo json_encode(array('status'=>false,'message'=>$model->errors));die;
            }
        }
		//################ Render to view #####################
        return $this->render('create', [
            'model' => $model,
        ]);
    }
	
	public function actionLocationGeoCode() {
		$result = array('status'=>false,'geocode'=>'');
		if(!empty($_POST['id'])){
			$id = $_POST['id'];
            $locData = Yii::$app->general->getLocationGeoCode($id);
            $lat = !empty($locData['location_lat']) ? $locData['location_lat'] : '-25.2744';
            $long = !empty($locData['location_long']) ? $locData['location_long'] : '133.7751';
            
			if(!empty($locData)){                
				$geodata = array('lat'=>$lat, 'long'=> $long);
				$result = array('status'=>true,'geocode'=>$geodata);
			}
		}
		echo json_encode($result);
		die;
    }
    
    public function actionLandowner(){
        $result = array('status'=>false,'list'=>'');
        if(isset($_POST['start']) && isset($_POST['end'])){
            $landOwnerList = \app\models\Landowner::find()->where(
                [
                    'AND',
                    [
                        'OR',
                        ['>=', 'from_kp', (float)$_POST['start']],
                        ['>=', 'to_kp', (float)$_POST['start']]
                    ],
                    [
                        'OR',
                        ['<=', 'from_kp', (float)$_POST['end']],
                        ['<=', 'to_kp', (float)$_POST['end']]
                    ]
                ])->active()->asArray()->all();
            if(!empty($landOwnerList)){
                $landownerlistHtml = $this->renderAjax('landowner',['landownerlist' => $landOwnerList]);
                $result = array('status'=>true,'list'=>$landownerlistHtml);
            }
        }

        echo json_encode($result);
        die;
    }
}