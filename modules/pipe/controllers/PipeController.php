<?php
namespace app\modules\pipe\controllers;
use Yii;
use app\models\Pipe;
use app\models\PipeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
class PipeController extends Controller
{
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [ 
                    [
                        'allow' => true,
                        'actions' => ['checkanomally','create','csv-import','csv-line','auto-list','defect-update'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    public function actionDefectUpdate($id,$defectsItem){    
        $Pipe = \app\models\Pipe::find()->where(['id'=>$id])->one();  
        if(!empty($Pipe->defects)){

            $defects = json_decode($Pipe->defects,true);
          
            if(!empty($defects)){
                $defects = array_flip($defects);
                unset($defects[$defectsItem]);
                $Pipe->defects = json_encode(array_flip($defects));
                $Pipe->save(false);
            }
        }
        return $this->redirect(['/pipe/pipe/create','EditId'=>$id]);
    }
    public function actionCreate($EditId=""){        
        $model     = new Pipe(); 
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Pipe',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']); 
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }

        if(!empty($_GET['download'])){
            ini_set('max_execution_time', 0);
            $searchModel = new \app\models\PipeSearch();
            $searchModel->download = 1;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }

        if($model->load(Yii::$app->request->post())){
            $model = Yii::$app->anomaly->pipe_anomaly($model);
            if($model->validate() && $model->save()) {                 
                echo json_encode(array('status'=>true,'modelData'=>'Your data has been saved.') );die;
            }else{
                echo json_encode(array('status'=>false,'message'=>$model->errors));die;
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    public function actionCsvLine(){ 
        $model 	  	 =  new \app\models\CsvImport;
        $model->file =  UploadedFile::getInstance($model,'file');      
		if(empty(Yii::$app->user->identity->project_id)){
            echo json_encode(array('status'=>false,'message'=>'Please select project to import.'));die;
        }
        if(empty($model->file)){
            echo json_encode(array('status'=>false,'message'=>'Invalid file or select csv file'));die;
        }
        $Error = [];
		if($model->file->extension == "csv"){
            ini_set('max_execution_time', 0);
			$filename 	=  time().'.'.$model->file->extension;
			$upload 	=  $model->file->saveAs(Yii::$app->basePath.'/web/csv/'.$filename);	
			if($upload){
				$csv_file = Yii::$app->basePath.'/web/csv/' . $filename;
				$filecsv  = file($csv_file);
			 	if(empty($filecsv)){
                    echo json_encode(array('status'=>false,'message'=>'This file has not any dat'));die;
                }
                $Imported = false;;
				foreach($filecsv as $k => $data){
					if($k>0){
                        $Line = new \app\models\Line;
                        $hasil = explode(",",$data);          
                        if(!empty($hasil[1])){
                            $Imported = true;
                            $Line->from_kp		                = !empty($hasil[0])?$hasil[0]:"";
                            $Line->to_kp 			            = !empty($hasil[1])?$hasil[1]:"";
                            $Line->from_geo_code 	        	= !empty($hasil[2])?$hasil[2]:"";
                            $Line->to_geo_code		            = !empty($hasil[3])?$hasil[3]:"";
                            $Line->pipe_diameter 	        	= !empty($hasil[4])?$hasil[4]:0;
                            $Line->wall_thickness 	        	= !empty($hasil[5])?$hasil[5]:0;
                            $Line->depth_of_cover 		        = !empty($hasil[6])?$hasil[6]:0;
                            $Line->coating_type 		        = !empty($hasil[7])?$hasil[7]:0;
                            $Line->bend_location		        = !empty($hasil[8])?$hasil[8]:0;
                            $Line->road_crossing		        = !empty($hasil[9])?$hasil[9]:0;
                            $Line->river_crossing 		        = !empty($hasil[10])?$hasil[10]:"";
                            $Line->foreign_service_crossing		= !empty($hasil[11])?$hasil[11]:0;
                            $Line->fence_crossing		        = !empty($hasil[12])?$hasil[12]:0;
                            $Line->hdd_locations				= !empty($hasil[13])?$hasil[13]:"";
                            $Line->backfill_material			= !empty($hasil[14])?$hasil[14]:"";
                            $Line->marker_tape_location			= !empty($hasil[15])?$hasil[1]:"";   
                            $Line->comment			            = !empty($hasil[16])?$hasil[16]:"";     
                                                  
                            $Line->project_id			        = Yii::$app->user->identity->project_id;
                            $Line->created_at                   = time();
                            $Line->updated_at                   = time();
                            $Line->created_by                   = Yii::$app->user->identity->id;
                            $Line->updated_by                   = Yii::$app->user->identity->id;
                            if ($Line->validate() && $Line->save()){
                                
                            }else{
                                echo json_encode(array('status'=>true,'message'=>'Csv file data imported.','error'=>$Line->errors));die;
                            }                            
                        }
					}
                }
                if($Imported){
                    echo json_encode(array('status'=>true,'message'=>'Csv file data imported.','error'=>$Error));die;
                } else{
                    echo json_encode(array('status'=>false,'message'=>'Invalid format data of the file.','error'=>$Error));die;
                }             
              
		    }else{
                echo json_encode(array('status'=>false,'message'=>'Upload instance missing'));die;
            }
		}else{
			echo json_encode(array('status'=>false,'message'=>'Only csv file will allowed.'));die;
        }
        echo json_encode(array('status'=>true,'data'=>array('message'=>'Your data has been saved.')));die;
    }
    public function actionCsvImport(){  
        $model 	  	 =  new \app\models\CsvImport;
        $model->file =  UploadedFile::getInstance($model,'file');      
		if(empty(Yii::$app->user->identity->project_id)){
            echo json_encode(array('status'=>false,'message'=>'Please select project to import.'));die;
        }
        if(empty($model->file)){
            echo json_encode(array('status'=>false,'message'=>'Invalid file or select csv file'));die;
        }
        $Error = [];
		if($model->file->extension == "csv"){
            ini_set('max_execution_time', 0);
			$filename 	=  time().'.'.$model->file->extension;
			$upload 	=  $model->file->saveAs(Yii::$app->basePath.'/web/csv/'.$filename);	
			if($upload){
				$csv_file = Yii::$app->basePath.'/web/csv/' . $filename;
                $filecsv  = file($csv_file);
			    if(empty($filecsv)){
                    echo json_encode(array('status'=>false,'message'=>'This file has not any dat'));die;
                }
                $Imported = false;;
				foreach($filecsv as $k => $data){
					if($k>0){
                        $Pipe = new Pipe;
                        $hasil = explode(",",$data);                         
                        if(!empty($hasil[1])){
                            $Imported = true;
                            $Pipe->pipe_number		    = !empty($hasil[0])?$hasil[0]:"";
                            $Pipe->mto_number 			= !empty($hasil[1])?$hasil[1]:"";
                            $Pipe->mto_certificate 		= !empty($hasil[2])?$hasil[2]:"";
                            $Pipe->mill					= !empty($hasil[3])?$hasil[3]:"";
                            $Pipe->od 					= !empty($hasil[4])?$hasil[4]:0;
                            $Pipe->wall_thikness 		= !empty($hasil[5])?$hasil[5]:0;
                            $Pipe->heat_number 			= !empty($hasil[6])?$hasil[6]:0;
                            $Pipe->plate_number 		= !empty($hasil[7])?$hasil[7]:0;
                            $Pipe->weight 				= !empty($hasil[8])?$hasil[8]:0;
                            $Pipe->length 				= !empty($hasil[9])?$hasil[9]:0;
                            $Pipe->coating_type 		= !empty($hasil[10])?$hasil[10]:"";
                            $Pipe->ship_out_number		= !empty($hasil[11])?$hasil[11]:0;
                            $Pipe->yeild_strength		= !empty($hasil[12])?$hasil[12]:0;
                            $Pipe->hfb					= !empty($hasil[13])?$hasil[13]:"";
                            $Pipe->vessel				= !empty($hasil[14])?$hasil[14]:"";
                            $Pipe->comments				= !empty($hasil[15])?$hasil[15]:"";                           
                            $Pipe->project_id			= Yii::$app->user->identity->project_id;
                            $Pipe->created_at           = time();
                            $Pipe->updated_at           = time();
                            $Pipe->created_by           = Yii::$app->user->identity->id;
                            $Pipe->updated_by           = Yii::$app->user->identity->id;
                            $Pipe->is_anomally			= "No";	
                            $Pipe = Yii::$app->anomaly->pipe_anomaly($Pipe);
                            if ($Pipe->validate() && $Pipe->save()){
                            }else{
                                $Error[$Pipe->pipe_number]   = $Pipe->errors; 
                            }                            
                        }
					}
                }
                if($Imported){
                    echo json_encode(array('status'=>true,'message'=>'Csv file data imported.','error'=>$Error));die;
                } else{
                    echo json_encode(array('status'=>false,'message'=>'Invalid format data of the file.','error'=>$Error));die;
                }             
              
		    }else{
                echo json_encode(array('status'=>false,'message'=>'Upload instance missing'));die;
            }
		}else{
			echo json_encode(array('status'=>false,'message'=>'Only csv file will allowed.'));die;
        }
        echo json_encode(array('status'=>true,'data'=>array('message'=>'Your data has been saved.')));die;
	}    
  
}
