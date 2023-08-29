<?php

namespace app\modules\welding\controllers;

use Yii;
use app\models\Welding;
use app\models\WeldingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WeldingController implements the CRUD actions for Welding model.
 */
class WeldingController extends Controller
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
                        'actions' => ['create','get-electrods','get-welders','pipe-auto-stringing-base','weld-number-alert','weld-crossing','defect-update'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }
    public function actionDefectUpdate($id,$defectsItem,$defectsPos){    
        $Welding = \app\models\Welding::find()->where(['id'=>$id])->one();  
        if(!empty($Welding->ndt_defects)){
            $defects = json_decode($Welding->ndt_defects,true);  
            if(!empty($defects)){
                foreach($defects as $k=>$ele){
                    if($ele['defects'] == $defectsItem && $ele['defect_position'] == $defectsPos){
                        unset( $defects[$k]);
                    }
                }
                $Welding->ndt_defects = json_encode($defects);
                $Welding->save(false);
            }
        }
        return $this->redirect(['/welding/welding/create','EditId'=>$id]);
    }
    /**
     * Creates a new Welding model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($EditId = "") {
        //################ Add #####################
        $model = new Welding();
        //################ Edit #####################
        if(!empty($EditId)){
            $model = Yii::$app->general->getModelData('\app\models\Welding',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']); 
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
			$model->electrodes = !empty($model->electrodes)?json_decode($model->electrodes,true):[];
        }
        //################ Save  #####################
        $model = Yii::$app->general->reportNo($model,'WEL');
        if ($model->load(Yii::$app->request->post())) {
            $model = Yii::$app->anomaly->welding_anomaly($model);
            if($model->validate() && $model->save()){
                $data = Yii::$app->general->UploadImg($model->id,'Welding');
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                    $Data = Welding::find()->where(['id'=>$model->id])->asArray()->one();
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
    public function actionGetElectrods(){
        $result = array('status'=>false,'data'=>array());
        if(!empty($_POST['wps'])){
            $data = Yii::$app->weld->getElectrods($_POST['wps']);
            if(!empty($data)){
                $result = array('status' => true, 'data' => $data);
            }
        }
        echo json_encode($result);
        die;
    }

    public function actionGetWelders(){
        $result = array('status'=>false,'data'=>array());
        if(!empty($_POST['wps'])){
            $data = Yii::$app->weld->getWelders($_POST['wps']);
            if(!empty($data)){
                $result = array('status' => true, 'data' => $data);
            }
        }
        echo json_encode($result);
        die;
    }

    public function actionWeldNumberAlert(){
        $result = array('status'=>false);
        if(isset($_POST['kp']) && isset($_POST['number'])){
            $resp = Yii::$app->weld->checkKpWeldNumber($_POST['kp'], $_POST['number']);
            if($resp == 0){
                $result = array('status'=>true);      
            }
        }
        echo json_encode($result);
        die;
    }

    public function actionWeldCrossing(){
        $result = array('status'=>false, 'count'=> 0);
        // if(isset($_POST['kp'])){
        //     $resp = Yii::$app->weld->checkWeldCrossing($_POST['kp']);
        //     if($resp != 0){
        //         $result = array('status'=>true, 'count'=>$resp);
        //     }
        // }
        if(isset($_POST['weld'])){
            $resp = Yii::$app->weld->checkWeldCrossing($_POST['kp'], $_POST['weld']);
            if($resp != 0){
                $result = array('status'=>true, 'count'=>$resp);
            }
        }
        echo json_encode($result);
        die;
    }
}
