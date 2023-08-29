<?php

namespace app\modules\welding\controllers;

use Yii;
use app\models\Ndt;
use app\models\NdtSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * NdtController implements the CRUD actions for Ndt model.
 */
class NdtController extends Controller
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
                        'actions' => ['create', 'csv-import'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }

    public function actionCreate($EditId = "") {
       
        //################ Add #####################
        $model = new Ndt();
        //################ Edit #####################
        if(!empty($EditId)){
            $model = Yii::$app->general->getModelData('\app\models\Ndt',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']); 
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }
        //################ Save  #####################
        $model = Yii::$app->general->reportNo($model,'NDT');
        
      

        if ($model->load(Yii::$app->request->post())) {
            $model = Yii::$app->anomaly->welding_ndt_anomaly($model,'\app\models\Ndt');

            // if($model->outcome == "Cut Out"){
            //     Yii::$app->weld->removeWeld($model->weld_number);
            // } // as per client says don't remove the weld
            
            $Da = !empty($_POST['Welding'])?$_POST['Welding']:array();       
            $weldNumber = $model->weld_number;     
            Yii::$app->general->saveNdtdefects($Da,$weldNumber);
            $Welding = \app\models\Welding::find()->where(['kp' => $model->kp, 'weld_number'=>$model->weld_number])->active()->one();
            $model->ndt_defact = !empty($Welding->ndt_defects)?$Welding->ndt_defects:"";
            
            if($model->validate() && $model->save()){
                // save data to welding screen
                if($model->outcome == 'Cut Out'){
                    $Welding->has_been_cut_out = 'Yes';
                    $Welding->save(false);
                }
                $data = Yii::$app->general->UploadImg($model->id,'Ndt');
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                    $Data = Ndt::find()->where(['id'=>$model->id])->asArray()->one();
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

    public function actionCsvImport(){  
        $model = new \app\models\CsvImport;
        $model->file = UploadedFile::getInstance($model,'file');
		if(empty(Yii::$app->user->identity->project_id)){
            echo json_encode(array('status'=>false,'message'=>'Please select project to import.'));die;
        }
        if(empty($model->file)){
            echo json_encode(array('status'=>false,'message'=>'Invalid file or select csv file'));die;
        }
        $error = [];
		if($model->file->extension == "csv"){
            $filename = 'ndt_import_'.time().'.'.$model->file->extension;
            $upload = $model->file->saveAs(Yii::$app->basePath.'/web/csv/'.$filename);
            if($upload){
                $csv_file = Yii::$app->basePath.'/web/csv/'.$filename;
                $filecsv = file($csv_file);
                if(empty($filecsv)){
                    echo json_encode(array('status'=>false,'message'=>'This file has not any dat'));die;
                }
                $imported = false;
                foreach($filecsv as $k => $data){
                    if($k > 0){
                        $ndt = new Ndt();
                        $colExplode = explode(",", $data);

                        if(isset($colExplode[1]) && $colExplode[1] != ''){
                            $imported = true;
                            if(strpos($colExplode[0], '/') !== false){
                                $colExplode[0] = str_replace('/', '-', $colExplode[0]);
                            }
                            $date = !empty($colExplode[0]) ? date('Y-m-d', strtotime($colExplode[0])) : date('Y-m-d');
                            $kp = isset($colExplode[1]) && $colExplode[1] != '' ? $colExplode[1] : '';
                            $weldNumber = !empty($colExplode[2]) ? $colExplode[2] : '';
                            $ndtDefect = !empty($colExplode[3]) ? $colExplode[3] : '';
                            $ndtDefectPosition = !empty($colExplode[4]) ? $colExplode[4] : '';
                            $defect = [];
                            if(!empty($ndtDefect) && !empty($ndtDefectPosition)){
                                $defect[] = array(
                                    'defects' => $ndtDefect,
                                    'defect_position' => $ndtDefectPosition
                                );
                            }
                            $outcome = !empty($colExplode[5]) ? $colExplode[5] : '';
                            $comment = !empty($colExplode[6]) ? $colExplode[6] : '';

                            $ndt = Yii::$app->general->reportNo($ndt, 'NDT');

                            $ndt->date = $date;
                            $ndt->kp = $kp;
                            $ndt->weld_number = $weldNumber;
                            if(!empty($defect)){
                                $ndt->ndt_defact = json_encode($defect);
                            }
                            $ndt->outcome = $outcome;
                            $ndt->comment = $comment;
                            
                            // get main weld id
                            $weldData = \app\models\Welding::find()->where(['AND',['=', 'weld_number', $weldNumber], ['=','kp', $kp], ['=', 'has_been_cut_out', 'No']])->active()->one();
                            if(!empty($weldData)){
                                $ndt->main_weld_id = $weldData->id;
                            } else {
                                $ndt->main_weld_id = 0;
                            }

                            if($ndt->outcome == 'Cut Out'){
                                $weldData->has_been_cut_out = 'Yes';
                                $weldData->save(false);
                            }

                            //common fields
                            $ndt->project_id = Yii::$app->user->identity->project_id;
                            $ndt->created_at = time();
                            $ndt->updated_at = time();
                            $ndt->created_by = Yii::$app->user->identity->id;
                            $ndt->updated_by = Yii::$app->user->identity->id;
                            $ndt->is_anomally = "No";	
                            $ndt = Yii::$app->anomaly->welding_ndt_anomaly($ndt, '\app\models\Ndt');
                            if($ndt->validate() && $ndt->save()){
                            }else{
                                $error[$ndt->weld_number] = $ndt->errors; 
                            }                            
                        }
					}
                }
                if($imported){
                    echo json_encode(array('status' => true, 'message' => 'Csv file data imported.', 'error' => $error));die;
                } else{
                    echo json_encode(array('status' => false, 'message' => 'Invalid format data of the file.', 'error' => $error));die;
                }
            }else{
                echo json_encode(array('status' => false, 'message' => 'Upload instance missing'));die;
            }
		}else{
			echo json_encode(array('status' => false, 'message' => 'Only csv file will allowed.'));die;
        }
        echo json_encode(array('status' => true, 'data' => array('message' => 'Your data has been saved.')));die;
	}
}