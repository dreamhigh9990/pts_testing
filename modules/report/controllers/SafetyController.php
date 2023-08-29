<?php
namespace app\modules\report\controllers;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\SafetySlam;
use app\models\Hazard;
use Yii;
class SafetyController extends \yii\web\Controller
{
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [                   
                    [
                        'allow' => true,
                        'actions' => ['hazard','slam','export-hazard','export-slam'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }    
    public function actionSlam($EditId=""){
        $model = new SafetySlam();
        $model = Yii::$app->general->reportNo($model,'SLM');
        if(!empty($EditId)){
            $model 			= \app\models\SafetySlam::find()->where(['id'=>$EditId])->one();
            if((isset($model['status']) && $model['status'] == false) || empty($model)){
                return $this->redirect(['slam']);
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        } 
        $res11 = true;$message="";
        if ($model->load(Yii::$app->request->post())) {
            $model->project_id =  Yii::$app->user->identity['project_id'];
            foreach($model->potential_hazards as $el){
                if($el['sub']['ans']=="No" && !empty($_GET['isCheck'])){
                   echo json_encode(array('status'=>true,'status_two'=>'warning'));die;
                }
            }
            $model->potential_hazards = json_encode($model->potential_hazards);
            if($EditId){
                $model->date_time  = date('Y-m-d h:i:s');
            }
            if($model->validate() && $model->save()) { 
                if(!empty($EditId)){                
                    $Data = '';
                 }else{
                    $Data = SafetySlam::find()->where(['id'=>$model->id])->asArray()->one(); 
                 }


                 setcookie ( "slam_name", $model->name, time()+(10 * 365 * 24 * 60 * 60) );
                 setcookie ( "slam_location", $model->location, time()+(10 * 365 * 24 * 60 * 60) );
                 setcookie ( "slam_crew", $model->crew, time()+(10 * 365 * 24 * 60 * 60) );


                echo json_encode(array('status'=>true,'modelData'=>$Data));die;
            }else{
                echo json_encode(array('status'=>false,'message'=>$model->errors));die;
            }
        }

        echo $this->render('slam_form', [
            'model'=>$model
        ]);
    }
    public function actionExportHazard(){
        // if($model == "app/models/Hazard"){
          
         $data = \app\models\Hazard::find()->select(['hazard_report.id','hazard_report.name','hazard_report.crew','hazard_report.location','hazard_report.date_time','hazard_report.details',
         'hazard_report.action','hazard_report.supervisor_in_charged','hazard_report.is_followup','user.username as Name'])
         ->leftJoin('user','hazard_report.updated_by = user.id')->active()->asArray()->all();;
 
         $outputBuffer = fopen("php://output", 'w');    
         $filename = 'Hazard-Report-'.time();;
 
         header("Content-type: text/csv");
         header("Content-Disposition: attachment; filename={$filename}.csv");
         header("Pragma: no-cache");
         header("Expires: 0");
     
         $outputBuffer = fopen("php://output", 'w');
         fputcsv($outputBuffer, array('Id','FullName','Crew','Location','Date','Details','Action','Supervisor in charged ?','Is followup ?','User'));
         foreach($data as $val) {
             fputcsv($outputBuffer, $val);
         }
         fclose($outputBuffer);
         die;
     }
    public function actionExportSlam(){
        $dataS = \app\models\SafetySlam::find()->select(['safety_slam.id','safety_slam.name','safety_slam.crew','safety_slam.location','safety_slam.date_time',
        'safety_slam.task','safety_slam.potential_hazards','user.username as Name'])
        ->leftJoin('user','safety_slam.updated_by = user.id AND safety_slam.project_id = '.Yii::$app->user->identity['project_id'])->active()->asArray()->all();;
        $data = array();
        $thead = array('Id','FullName','Crew','Location','Date','Task','User');
        $PotentilHazard = array_reverse(Yii::$app->general->TaxonomyDrop(29));
        foreach($PotentilHazard as $q){
            $t[$q]  = $q;
            $t['Managed ? -'.$q] ='Managed ? -'.$q;
            $t['Action ? -'.$q]  ='Action ? -'.$q;
        }
        $thead          = array_values(array_merge($thead,$t));
        if(!empty($dataS)){
            foreach($dataS as $i => $ele){
                $e =  $ele;
                if(!empty($ele['potential_hazards'])){
                    $d = json_decode($ele['potential_hazards'],true);
                    $h ="";
                    unset($ele['potential_hazards']);
                    if(!empty($d)){
                        foreach($d as $k=>$v){                  
                                $ele[$v['question']]        = !empty($v['ans']) ? $v['ans']:"";
                                $ele[$v['question'].'- Managed']= !empty($v['sub']['ans']) ? $v['sub']['ans']:"";
                                $ele[$v['question'].'- Action'] = !empty($v['sub']['q']) ? $v['sub']['q']:"";                    
                        }
                    }
                }
                array_push($data,$ele);
            }
        }
        $outputBuffer = fopen("php://output", 'w');    
        $filename = 'Slam-Report-'.time();;

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$filename}.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        $outputBuffer = fopen("php://output", 'w');
        fputcsv($outputBuffer, $thead);
        foreach($data as $val) {
            fputcsv($outputBuffer, $val);
        }
        fclose($outputBuffer);
        die;
    }
    public function actionHazard($EditId = ""){       
        $model = new Hazard();
        $model = Yii::$app->general->reportNo($model,'HZD');
        if(!empty($EditId)){
            $model 			= \app\models\Hazard::find()->where(['id'=>$EditId])->one();
            if((isset($model['status']) && $model['status'] == false) || empty($model)){
                return $this->redirect(['hazard']);
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        } 
        if ($model->load(Yii::$app->request->post())) {
            $model->project_id =  Yii::$app->user->identity['project_id'];
            if($EditId == ""){
                $model->date_time  = date('Y-m-d h:i:s');
            }
            if($model->validate() && $model->save()) { 
                setcookie ( "hazard_name", $model->name, time()+(10 * 365 * 24 * 60 * 60) );
                setcookie ( "hazard_location", $model->location, time()+(10 * 365 * 24 * 60 * 60) );
                setcookie ( "hazard_crew", $model->crew, time()+(10 * 365 * 24 * 60 * 60) );
                if(!empty($EditId)){                
                    $Data = '';
                 }else{
                    $Data = Hazard::find()->where(['id'=>$model->id])->asArray()->one(); 
                 }
                echo json_encode(array('status'=>true,'modelData'=>$Data));die;
            }else{
                echo json_encode(array('status'=>false,'message'=>$model->errors));die;
            }
        }

        echo $this->render('hazard_form', [
            'model'=>$model
        ]);
    }
}
