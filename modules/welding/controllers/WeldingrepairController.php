<?php

namespace app\modules\welding\controllers;

use Yii;
use app\models\Weldingrepair;
use app\models\WeldingrepairSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WeldingrepairController implements the CRUD actions for Weldingrepair model.
 */
class WeldingrepairController extends Controller
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
                        'actions' => ['create', 'get-ndt-data'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }
    public function actionCreate($EditId = "") {
        //################ Add #####################
        $model = new Weldingrepair();
        //################ Edit #####################
        if(!empty($EditId)){
            $model = Yii::$app->general->getModelData('\app\models\Weldingrepair',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']); 
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
			$model->electrodes = !empty($model->electrodes)?json_decode($model->electrodes,true):[];
        }
        //################ Save  #####################
        $model = Yii::$app->general->reportNo($model,'REP');
        if ($model->load(Yii::$app->request->post())) {

            $Welding = \app\models\Welding::find()->where(['kp' => $model->kp, 'weld_number' => $model->weld_number, 'has_been_cut_out' => 'No'])->active()->one();

            $model = Yii::$app->anomaly->welding_repair_anomaly($model,'\app\models\Weldingrepair');
            $model->ndt_defact = !empty($Welding->ndt_defects)?$Welding->ndt_defects:"";
            //print_r($model->ndt_defect);die;
            if($model->validate() && $model->save()){             
                // save data to welding screen
                if($model->excavation == 'Cut-Out'){
                    $Welding->has_been_cut_out = 'Yes';
                    $Welding->save(false);
                }
                $data = Yii::$app->general->UploadImg($model->id,'Weldingrepair');
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                    $Data = Weldingrepair::find()->where(['id'=>$model->id])->asArray()->one();
                }
                
               
                
                if(!empty($Welding->ndt_defects)){
                    $Defects = json_decode($Welding->ndt_defects,true);
                    if(!empty($Defects)){
                        $de = array();
                        foreach($Defects as $ele){
                            $d['defects']         = $ele['defects'];
                            $d['defect_position'] = $ele['defect_position'];
                            $d['repaired']        = $model->id;   
                            array_push($de,$d);                       
                        }
                       $Welding->ndt_defects = json_encode($de);
                       $Welding->save(false);
                    }                 
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

    public function actionGetNdtData(){        
        $result = array('data'=>'');
        if(isset($_POST['kp']) && !empty($_POST['number'])){
            $weldDetails = Yii::$app->weld->ndtData($_POST['number'],$_POST['kp']);
            if(!empty($weldDetails)){
                $result = array('data'=>$weldDetails);
            }
        }
        echo json_encode($result);
        die;
    }
}
