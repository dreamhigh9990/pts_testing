<?php
namespace app\modules\report\controllers;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Welding;
use app\models\Pipe;
use mikehaertl\wkhtmlto\Pdf;
use Yii;
class ReportController extends \yii\web\Controller
{
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [                   
                    [
                        'allow' => true,
                        'actions' => ['weldbook-report2','index','clearance-report','review-report','weldbook-report','visual-progress','clearance','production','welder-combine','welder-detail','welder-overall','sequence'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    public function getWelderdata($welder_name,$date,$weldType = ""){
        if($weldType == ""){
            $join = 1;
        }else{
            $join = ['line_type'=>$weldType];
        }
        //####################### Welding ####################################
        $TotalWeldCount =0; $TotalWeldLength = 0;                 
        $Welding =  \app\models\Welding::find()->select(['welding.root_os','welding.root_ts','welding.hot_os','welding.hot_ts','welding.fill_os',
        'welding.fill_ts','welding.cap_os','welding.cap_ts','pipe.od','welding.weld_number','welding.kp'])
                    ->leftJoin('pipe','welding.pipe_number=pipe.pipe_number AND welding.project_id=pipe.project_id AND welding.is_active=pipe.is_active')
                    ->where([   'OR',
                                ['root_os'=>$welder_name],
                                ['root_ts'=>$welder_name],
                                ['hot_os'=>$welder_name],
                                ['hot_ts'=>$welder_name],
                                ['fill_os'=>$welder_name],
                                ['fill_ts'=>$welder_name],
                                ['cap_os'=>$welder_name],
                                ['cap_ts'=>$welder_name],
                            ])
                    ->andWhere(['between', 'welding.date',date('Y-m-d',strtotime("-1 year",strtotime($date))),$date])
                    ->andWhere($join)
                    ->active()->asArray()->all();
        $TotalWeldRepairCount = 0;$RepairLength =0;
        if(!empty($Welding[0])){   
                          
                foreach($Welding as $Weld){
                    $WelderCount = 0;        
                    $WelderWeldPosition = array();    
                     
                    foreach($Weld as $key => $v){
                        if($v == $welder_name){
                            $WelderCount    =  $WelderCount+1;
                            $TotalWeldCount =  $TotalWeldCount+1;                                        
                                array_push($WelderWeldPosition,$key);
                        }
                    }
                    $TotalWeldLength = $TotalWeldLength+$WelderCount*3.14*$Weld['od']; 

                    $Weldingrepair = \app\models\Ndt::find()->where(['weld_number'=>$Weld['weld_number'],'kp'=>$Weld['kp']])
                    ->andWhere(['OR',['outcome'=>'Rejected'],['outcome'=>'Repaired']])->active()->all();
                    
                    if(!empty($Weldingrepair)){
                        foreach($Weldingrepair as $s){
                            $defect = json_decode($s['ndt_defact'],true);
                            
                            if(!empty($defect)){
                                foreach($defect as $k => $x){
                                    $dp =   str_replace(" ","_",strtolower($x['defect_position']));                                  
                                    if (in_array($x['defect_position'], $WelderWeldPosition) || in_array($dp, $WelderWeldPosition)  ){
                                        //   echo 1;
                                        $TotalWeldRepairCount = $TotalWeldRepairCount + 1;
                                    }
                                }
                            }                                   
                        }
                        $RepairLength    = $RepairLength+(3.14*$Weld['od']);
                    }  
                }   
                            
        }
        return array('WelderName'=>$welder_name,
                    'TotalWeldCount'=>$TotalWeldCount,
                    'TotalWeldRepair'=>$TotalWeldRepairCount,                              
                    'WeldingLength'=>$TotalWeldLength,
                    'RepairLength'=>$RepairLength,  
                    'RepairRate'=>  !empty($TotalWeldCount) ? ($TotalWeldRepairCount/$TotalWeldCount)*100 : 0  ,
                    'WeldNumbers'=>ArrayHelper::map($Welding,'weld_number','weld_number')                                           
    );
              
    }
    public function actionWelderDetail(){
        $date        =  empty($_GET['date']) ? date('Y-m-d') : $_GET['date'];
        $welder_name =  empty($_GET['welder_name']) ? "" : $_GET['welder_name'];

        $data =  $this->getWelderdata($welder_name,$date);
        $defectPosition = [
            'Root OS' => 'Root OS',
            'Root TS' => 'Root TS',
            'Hot OS' => 'Hot OS',
            'Hot TS' => 'Hot TS',
            'Fill OS' => 'Fill OS',
            'Fill TS' => 'Fill TS',
            'Cap OS' => 'Cap OS',
            'Cap TS' => 'Cap TS',
        ];
        foreach($defectPosition as $ele){
            $data['NdtData'][] = (float) \app\models\Ndt::find()->where(['AND',['IN','weld_number',$data['WeldNumbers'],['defect_position'=>$ele] ]])->active()->count();
        }


        // $ndtDefectList = ArrayHelper::map(\app\models\TaxonomyValue::find()->where(['taxonomy_id'=>9,'project_id'=>Yii::$app->user->identity->project_id])->asArray()->all(),'id','value');     
        
        $ndtDefectList = array_values(Yii::$app->general->TaxonomyDrop(9, true));

        $weldingDataMain =  \app\models\Welding::find()->where([
            'OR',
            ['welding.root_os'=>$welder_name],
            ['welding.root_ts'=>$welder_name],
            ['welding.hot_os'=>$welder_name],
            ['welding.hot_ts'=>$welder_name],
            ['welding.fill_os'=>$welder_name],
            ['welding.fill_ts'=>$welder_name],
            ['welding.cap_os'=>$welder_name],
            ['welding.cap_ts'=>$welder_name],
        ])->active()->asArray()->all();
        $fMainArray = array();
        $defectPosArray = array();

        if(!empty($weldingDataMain)){
            $CountOfDefectMain = array();
            foreach($weldingDataMain as $wData){

                $Weldingrepair = \app\models\Ndt::find()->where(['weld_number'=>$wData['weld_number'], 'kp'=>$wData['kp']])->andWhere(['OR',['outcome'=>'Rejected'],['outcome'=>'Repaired']])->active()->all();
                    
                if(!empty($Weldingrepair)){
                    foreach($Weldingrepair as $s){
                        $defect = json_decode($s['ndt_defact'],true);                        
                        if(!empty($defect)){
                            foreach($defect as $k => $x){
                                $dp = str_replace(" ","_",strtolower($x['defect_position']));
                                foreach($defectPosition as $position){
                                    if(!empty($x['defect_position'])){
                                        $currentPos = str_replace(' ', '_', strtolower($x['defect_position']));
                                    }else{
                                        $currentPos = "";
                                    }

                                    if(($wData[$currentPos] == $welder_name && $x['defect_position'] == $position && in_array($x['defect_position'], $defectPosition)) || in_array($dp, $defectPosition)){
                                        $CountOfDefectMain[$position][] = $x['defects'];
                                    } else {
                                        $CountOfDefectMain[$position][] = 0;
                                    }
                                }
                            }
                        }                                   
                    }
                }
            }
            
            if(!empty($CountOfDefectMain)){
                foreach($CountOfDefectMain as $key => $countDef){                    
                    $fMainArray[$key] = array_count_values($countDef);
                }
            }

            $finalMainArray = array();
            if(!empty($ndtDefectList)){
                foreach($ndtDefectList as $ndtDef){
                    $finalMainArray = array();
                    foreach($fMainArray as $fMain){
                        if (array_key_exists($ndtDef, $fMain)){
                            $finalMainArray[] = $fMain[$ndtDef];
                        } else {
                            $finalMainArray[] = 0;
                        }
                    }
                    $defectPosArray[] = array(
                        'name' => $ndtDef,
                        'data' => $finalMainArray,
                    );
                }
            }
        } else {
            if(!empty($ndtDefectList)){
                foreach($ndtDefectList as $ndtDef){
                    $emptyTieArray = array();
                    for($i=0;$i<count($defectPosition);$i++){
                        $emptyTieArray[] = 0;
                    }
                    $defectPosArray[] = array(
                        'name' => $ndtDef,
                        'data' => $emptyTieArray,
                    );
                }
            }
        }
        return $this->render('welderDetail',['data'=>$data,'defectPosArray'=>$defectPosArray,
        'defectPosition'=>array('Root OS','Root TS','Hot OS','Hot TS','Fill OS','Fill TS','Cap OS','Cap TS')]);      
    }
    public function actionWelderCombine(){
        $jsn = false;
        if(!empty($_POST['date'])) $jsn = true;
        $date = empty($_POST['date']) ? date('Y-m-d') : $_POST['date'];
        $welderData = array();
        $WelderList  = ArrayHelper::map(\app\models\TaxonomyValue::find()->where(['taxonomy_id'=>7,'project_id'=>Yii::$app->user->identity->project_id])->asArray()->all(),'value','value');
      // $WelderList = array('Sagar Patel'=>'Sagar Patel');
       if(!empty($WelderList)){
            foreach($WelderList as $welder_name){ 
                $d = $this->getWelderdata($welder_name,$date);
                array_push($welderData,$d);
            }      
            if($jsn){
                $html = $this->renderAjax('_weldercombinetable',['data'=>$welderData,'date'=>$date]);
                echo json_encode(['status'=>true,'html'=>$html]);
                die;
            } else {
                return $this->render('weldercombine',['data'=>$welderData,'date'=>$date]);      
            }
        }
    }

    public function actionWelderOverall(){
        $WeldData    = array('defect_position'=>array('Root OS','Root TS','Hot OS','Hot TS','Fill OS','Fill TS','Cap OS','Cap TS'));
        // $welderList  = ArrayHelper::map(\app\models\TaxonomyValue::find()->where(['taxonomy_id'=>7,'project_id'=>Yii::$app->user->identity->project_id])->asArray()->all(),'value','value');
        $welderList  = Yii::$app->general->TaxonomyDrop(7);
        //$ndtDefectList = ArrayHelper::map(\app\models\TaxonomyValue::find()->where(['taxonomy_id'=>9,'project_id'=>Yii::$app->user->identity->project_id])->asArray()->all(),'id','value');       
        $ndtDefectList = array_values(Yii::$app->general->TaxonomyDrop(9, true));

        if(!empty($welderList)){
            $mainLineWelder = array();
            $tieLineWelder =  array();
            $date        =  empty($_GET['date']) ? date('Y-m-d') : $_GET['date'];
            foreach($welderList as $welder){
               
                $d = $this->getWelderdata($welder,$date,'Main Line');  
             
                $WeldData['mainline']['name'][]  = $welder;
                $WeldData['mainline']['count'][] = (float)!empty($d)?$d['TotalWeldRepair']:0;

                $d = $this->getWelderdata($welder,$date,'Tie Line');     
                $weldingRepairTie =(float)!empty($d)?$d['TotalWeldRepair']:0;     
                $weldingTie       = (float)!empty($d)?$d['TotalWeldCount']:0;     
                $WeldData['tieline']['name'][]  = '(Repair = '.$weldingRepairTie.')/(Weld ='.$weldingTie.') | '.$welder;
                $WeldData['tieline']['count'][] = $weldingRepairTie;
                $WeldData['tieline']['rate'][]  = !empty($weldingTie)?($weldingRepairTie / $weldingTie)*100:0;

               
            }
        }
        
        /******************** Main Line *************************/
        $weldingDataListMainLine = \app\models\Welding::find()->where(['line_type' => 'Main Line'])->active()->asArray()->all();
        // echo "<pre>";
        // print_r($weldingDataListMainLine);
        // die;
        $fMainArray = array();
        $fTieArray = array();
        $defectPosArray = array();
        if(!empty($weldingDataListMainLine)){
            $CountOfDefectMain = array();
            foreach($weldingDataListMainLine as $wData){
                // $defactsData = !empty($wData['ndt_defects']) ? json_decode($wData['ndt_defects'], true) : array();
                // if(!empty($defactsData)){
                //     foreach($defactsData as $defect){
                //         foreach($WeldData['defect_position'] as $position){
                //             if(!empty($defect['defect_position']) && $position == $defect['defect_position']){
                //                 $CountOfDefectMain[$position][] = $defect['defects'];
                //             } else {
                //                 $CountOfDefectMain[$position][] = 0;
                //             }
                //         }
                //     }
                // }

                $Weldingrepair = \app\models\Ndt::find()->where(['weld_number'=>$wData['weld_number'],'kp'=>$wData['kp']])->andWhere(['OR',['outcome'=>'Rejected'],['outcome'=>'Repaired']])->active()->all();
                    
                if(!empty($Weldingrepair)){
                    foreach($Weldingrepair as $s){
                        $defect = json_decode($s['ndt_defact'],true);                        
                        if(!empty($defect)){
                            foreach($defect as $k => $x){
                                $dp = str_replace(" ","_",strtolower($x['defect_position']));
                                foreach($WeldData['defect_position'] as $position){
                                    if((($x['defect_position'] == $position) && in_array($x['defect_position'], $WeldData['defect_position'])) || in_array($dp, $WeldData['defect_position'])){
                                        $CountOfDefectMain[$position][] = $x['defects'];
                                    } else {
                                        $CountOfDefectMain[$position][] = 0;
                                    }
                                }
                            }
                        }                                   
                    }
                }
            }
            
            if(!empty($CountOfDefectMain)){
                foreach($CountOfDefectMain as $key => $countDef){                    
                    $fMainArray[$key] = array_count_values($countDef);
                }
            }

            $finalMainArray = array();
            if(!empty($ndtDefectList)){
                foreach($ndtDefectList as $ndtDef){
                    $finalMainArray = array();
                    foreach($fMainArray as $fMain){
                        if (array_key_exists($ndtDef, $fMain)){
                            $finalMainArray[] = $fMain[$ndtDef];
                        } else {
                            $finalMainArray[] = 0;
                        }
                    }
                    $defectPosArray[] = array(
                        'name' => $ndtDef,
                        'data' => $finalMainArray,
                    );
                }
            }
        } else {
            if(!empty($ndtDefectList)){
                foreach($ndtDefectList as $ndtDef){
                    $emptyTieArray = array();
                    for($i=0;$i<count($WeldData['defect_position']);$i++){
                        $emptyTieArray[] = 0;
                    }
                    $defectPosArray[] = array(
                        'name' => $ndtDef,
                        'data' => $emptyTieArray,
                    );
                }
            }
        }

        /******************** Tie Line **************************/
        $weldingDataListTieLine = \app\models\Welding::find()->where(['line_type' => 'Tie Line'])->active()->asArray()->all();
        $fTieArray = array();
        $fTieArray = array();
        $defectPosArrayTie = array();
        if(!empty($weldingDataListTieLine)){
            $CountOfDefectTie = array();
            foreach($weldingDataListTieLine as $wData){
                // $defactsData = !empty($wData['ndt_defects']) ? json_decode($wData['ndt_defects'], true) : array();              
                // if(!empty($defactsData)){
                //     foreach($defactsData as $defect){
                //         foreach($WeldData['defect_position'] as $position){
                //             if(!empty($defect['defect_position']) && $position == $defect['defect_position']){
                //                 $CountOfDefectTie[$position][] = $defect['defects'];
                //             } else {
                //                 $CountOfDefectTie[$position][] = 0;
                //             }
                //         }
                //     }
                // }

                $Weldingrepair = \app\models\Ndt::find()->where(['weld_number'=>$wData['weld_number'],'kp'=>$wData['kp']])->andWhere(['OR',['outcome'=>'Rejected'],['outcome'=>'Repaired']])->active()->all();
                    
                if(!empty($Weldingrepair)){
                    foreach($Weldingrepair as $s){
                        $defect = json_decode($s['ndt_defact'],true);                        
                        if(!empty($defect)){
                            foreach($defect as $k => $x){
                                $dp = str_replace(" ","_",strtolower($x['defect_position']));
                                foreach($WeldData['defect_position'] as $position){
                                    if((($x['defect_position'] == $position) && in_array($x['defect_position'], $WeldData['defect_position'])) || in_array($dp, $WeldData['defect_position'])){
                                        $CountOfDefectTie[$position][] = $x['defects'];
                                    } else {
                                        $CountOfDefectTie[$position][] = 0;
                                    }
                                }
                            }
                        }                                   
                    }
                }
            }
            
            if(!empty($CountOfDefectTie)){
                foreach($CountOfDefectTie as $key => $countDef){                    
                    $fTieArray[$key] = array_count_values($countDef);
                }
            }

            $finalTieArray = array();
            if(!empty($ndtDefectList)){
                foreach($ndtDefectList as $ndtDef){
                    $finalTieArray = array();
                    foreach($fTieArray as $fTie){
                        if (array_key_exists($ndtDef, $fTie)){
                            $finalTieArray[] = $fTie[$ndtDef];
                        } else {
                            $finalTieArray[] = 0;
                        }
                    }
                    $defectPosArrayTie[] = array(
                        'name' => $ndtDef,
                        'data' => $finalTieArray,
                    );
                }
            }
        } else {
            if(!empty($ndtDefectList)){
                foreach($ndtDefectList as $ndtDef){
                    $emptyTieArray = array();
                    for($i=0;$i<count($WeldData['defect_position']);$i++){
                        $emptyTieArray[] = 0;
                    }
                    $defectPosArrayTie[] = array(
                        'name' => $ndtDef,
                        'data' => $emptyTieArray,
                    );
                }
            }
        }
      
        /*******************************************************/  
      
        return $this->render('welder-overall',['data'=>$WeldData,'defectPosArray'=>$defectPosArray,'defectPosArrayTie'=>$defectPosArrayTie]);    
    }

    public function actionClearance(){
        $data = array();
        if(isset($_POST['ClearanceReport'])){
            if(isset($_POST['ClearanceReport']['from_kp']) && isset($_POST['ClearanceReport']['to_kp']) && isset($_POST['ClearanceReport']['from_weld']) && isset($_POST['ClearanceReport']['to_weld'])){

                $StartWelding = \app\models\Welding::find()->where(['kp'=>$_POST['ClearanceReport']['from_kp'],'weld_number'=>$_POST['ClearanceReport']['from_weld']])->active()->asArray()->one();
                $data['startPipe']   = $StartWelding['pipe_number'];                
                $data['startKp']     = $StartWelding['kp'];     
                $data['startWeld']   = $StartWelding['weld_number'];  

                $EndWelding  = \app\models\Welding::find()->where(['kp'=>$_POST['ClearanceReport']['to_kp'],'weld_number'=>$_POST['ClearanceReport']['to_weld']])->active()->asArray()->one();
                $data['endPipe']   = $EndWelding['next_pipe'];                
                $data['endKp']     = $EndWelding['kp'];     
                $data['endWeld']   = $EndWelding['weld_number'];  


                //####################### Weld Data Received #####################
                $data['weldCheck'] = "No";
                $Prev = \app\models\Welding::find()->where(['pipe_number'=>$StartWelding['pipe_number']])->active()->asArray()->one();
               
                if(!empty($Prev)){
                   $Next = \app\models\Welding::find()->where(['next_pipe'=>$EndWelding['pipe_number']])->active()->asArray()->one();
                   if(!empty($Next)){
                        $data['weldCheck'] = "Yes";
                   }
                }
                //########################### Weld Data ######################                
                $Welding = \app\models\Welding::find()->select(['weld_number','kp'])->where([ 'AND',
                                                             ['>=','kp',$_POST['ClearanceReport']['from_kp']],
                                                             ['<=','kp',$_POST['ClearanceReport']['to_kp']],                                                             
                                                           ])->active()->asArray()->all();

                $WeldingRecord = array();                 
                if(!empty($Welding)){
                    $start = 0; $end = 0;
                    
                    foreach($Welding as $ele){
                        if($ele['kp']==$_POST['ClearanceReport']['from_kp'] && $ele['weld_number']==$_POST['ClearanceReport']['from_weld']){
                            $start = 1;      
                        }                       
                        if($start==1 && $end == 0){
                            array_push($WeldingRecord,$ele);
                        }
                        if($ele['kp']==$_POST['ClearanceReport']['to_kp'] && $ele['weld_number']==$_POST['ClearanceReport']['to_weld']){     
                            $end = 1;        
                        }                        
                    }                    
                }
                 //########################### Weld Ndt Received ######################
                $data['ndtCheck'] = "No";
                $NdtData = array();
                if(!empty($WeldingRecord)){
                    foreach($WeldingRecord as $e){
                        $data['ndtCheck'] = "Yes";
                        $Ndt = \app\models\Ndt::find()->where(['kp'=>$e['kp'],'weld_number'=>$e['weld_number']])->active()->asArray()->one(); 
                        if(!isset($Ndt)){
                            $data['ndtCheck'] = "No";
                            break;
                        }else{
                            array_push($NdtData,$Ndt);
                        }
                    }                       
                }
               
                //########################### No outstanding repairs ######################
                 $data['repairCheck'] = "No";
                 if(!empty($WeldingRecord)){
                     $Rejected =array();
                     foreach($WeldingRecord as $e){                         
                         $Ndt = \app\models\Ndt::find()->select(['kp','weld_number'])->where(['kp'=>$e['kp'],'weld_number'=>$e['weld_number'],'outcome'=>'Rejected'])->active()->asArray()->one(); 
                         if(!empty($Ndt)){
                            array_push($Rejected,$Ndt);
                         }
                     }
                     if(!empty($Rejected)){
                        foreach($Rejected as $e){  
                            $data['repairCheck'] = "Yes";                       
                            $Repair = \app\models\Weldingrepair::find()->select(['kp','weld_number'])->where(['kp'=>$e['kp'],'weld_number'=>$e['weld_number']])->active()->asArray()->one(); 
                            if(!isset($Repair)){
                                $data['repairCheck'] = "No";
                                break;
                            }
                        }   
                     }                    
                           
                 }

                //########################### Weld Coating Production ######################
                $data['coatingCheck'] = "No";
                if(!empty($WeldingRecord)){
                    foreach($WeldingRecord as $e){
                        $data['coatingCheck'] = "Yes";
                        $Production = \app\models\Production::find()->where(['kp'=>$e['kp'],'weld_number'=>$e['weld_number']])->active()->asArray()->one(); 
                        if(!isset($Production)){
                            $data['coatingCheck'] = "No";
                            break;
                        }                        
                    }                       
                }
                 //########################### Weld Coating Production Accepted ######################
                 $data['coatingAccepted'] = "No";
                 if($data['ndtCheck'] == "Yes"){
                    if(!empty($NdtData)){
                        foreach($NdtData as $e){
                            $data['coatingAccepted'] = "Yes";
                            $Production = \app\models\Production::find()->where(['kp'=>$e['kp'],'weld_number'=>$e['weld_number']])->active()->asArray()->one(); 
                           
                            if(!empty($Production)){
                                if($Production['outcome']=="Rejected"){
                                    $CoatingRepair = \app\models\Coatingrepair::find()->where(['kp'=>$Production['kp'],'weld_number'=>$Production['weld_number']])->active()->asArray()->one(); 
                                    if(empty($CoatingRepair)){
                                        $data['coatingAccepted'] = "No";
                                    }
                                }
                            }else{
                                $data['coatingAccepted'] = "No";
                                break;
                            }                        
                        }                       
                    }
                }
                 //########################### Weld AnomalyCheck ######################
                 $data['anomalyCheck'] = "No";
                 if(!empty($WeldingRecord)){
                     foreach($WeldingRecord as $e){
                         $data['anomalyCheck'] = "Yes";
                         $Welding = \app\models\Welding::find()->where(['kp'=>$e['kp'],'weld_number'=>$e['weld_number'],'is_anomally'=>'Yes'])->active()->one();
                         if(!empty($Welding )){
                            $data['anomalyCheck'] = "No";
                            break;
                         }
                         $Parameter = \app\models\Parameter::find()->where(['kp'=>$e['kp'],'weld_number'=>$e['weld_number'],'is_anomally'=>'Yes'])->active()->one();
                         if(!empty($Parameter )){
                            $data['anomalyCheck'] = "No";
                            break;
                         }
                         $Ndt = \app\models\Ndt::find()->where(['kp'=>$e['kp'],'weld_number'=>$e['weld_number'],'is_anomally'=>'Yes'])->active()->one();
                         if(!empty($Ndt )){
                            $data['anomalyCheck'] = "No";
                            break;
                         }
                         $Weldingrepair = \app\models\Welding::find()->where(['kp'=>$e['kp'],'weld_number'=>$e['weld_number'],'is_anomally'=>'Yes'])->active()->one();
                         if(!empty($Weldingrepair )){
                            $data['anomalyCheck'] = "No";
                            break;
                         }

                         $Production = \app\models\Production::find()->where(['kp'=>$e['kp'],'weld_number'=>$e['weld_number'],'is_anomally'=>'Yes'])->active()->one();
                         if(!empty($Production )){
                            $data['anomalyCheck'] = "No";
                            break;
                         }

                         $Coatingrepair = \app\models\Coatingrepair::find()->where(['kp'=>$e['kp'],'weld_number'=>$e['weld_number'],'is_anomally'=>'Yes'])->active()->one();
                         if(!empty($Coatingrepair )){
                            $data['anomalyCheck'] = "No";
                            break;
                         }
                     }                   
                 }
                 
                $html =  $this->renderAjax('_clearanceForm',['data'=>$data]);           
                $res['status'] = true; 
                $res['html']   = $html; 
            }else{
                $res['status'] = false; 
                $res['message'] = "Parameter is missing";
            }
            echo json_encode($res);die;
        }
        return $this->render('Clearance',['data'=>$data]);
    }
    public function actionIndex($model=""){ 
        if($model == "Dailyproduction"){
            echo $this->render($model);die;
        }else{            
            $searchModel ='\\app\models\\'.$model;
            $searchModel = new $searchModel;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);        
            echo $this->render('_'.$model, [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }

    }

    public function actionProduction($filter=false){
        $filterType = 'all';
        $dateRange = '';
        $html = '';
        if(!empty($_POST['filterType'])){
            $filterType = $_POST['filterType'];
            if($filterType == 'weekly'){
                $dateRange = !empty($_POST['weekRange']) ? $_POST['weekRange'] : "";
                $html = $this->renderAjax('production/_overallproductiondata', ['filterType' => $filterType,'dateRange' => $dateRange,]);
            } else if($filterType == 'daily') {
                $dateRange = !empty($_POST['dailyRange']) ? $_POST['dailyRange'] : "";
                $html = $this->renderAjax('production/_overallproductiondata', ['filterType' => $filterType,'dateRange' => $dateRange,]);
            } else if($filterType == 'all'){
                $html = $this->renderAjax('production/_overallproductiondata', ['filterType' => $filterType,'dateRange' => $dateRange,]);
            }
        }

        if($filter){
            $result = [
                'status' => true,
                'html' => $html
            ];
            echo json_encode($result);
            die;
        } else {        
            
            return $this->render('Dailyproduction', ['filterType' => $filterType,'dateRange' => $dateRange,]);
        }
    }

    public function actionClearanceReport(){
        if(!empty($_POST['Clearance']['from_kp']) && !empty($_POST['Clearance']['to_kp'])){
            $res['html'] = $this->renderAjax('_clearanceHtml');
            $res['status'] = true; 
            echo json_encode($res);die;
        }else{
            $res['status'] = false;
            $res['message'] ="From kp or To kp is missing.";
        }
        echo json_encode($res);die;
    }
    public function actionReviewReport($model="PipeSearch"){ 
        return $this->render('reviewsummary',['model'=>$model]);
    }
    public function actionVisualProgress(){ 
        return $this->render('Visualprograss');
    }
    public function actionWeldbookReport(){
        $searchModel = new \app\models\WeldingSearch();
                        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                
        echo $this->render('WeldBook', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionWeldbookReport2(){

      
             $download_file =  Yii::$app->basePath."/web/excel/1523944008weldbook.xlsx";
    
            if(file_exists($download_file)){
                header('Content-Disposition: attachment; filename=1523944008weldbook.xlsx');  
                readfile($download_file); 
                exit;
            }else {
                 echo $download_file;die;
                echo 'File does not exists on given path';
            }
        


        $query=Welding::find()->leftJoin('welding_coating_production', 'welding.weld_number=welding_coating_production.weld_number')
        ->leftJoin('welding_coating_repair','welding.weld_number=welding_coating_repair.weld_number AND 
        welding_coating_repair.is_active=1 AND welding_coating_repair.is_deleted=0 AND welding_coating_repair.project_id='.Yii::$app->user->identity->project_id)
        ->leftJoin('welding_ndt','welding.weld_number=welding_ndt.weld_number AND 
        welding_ndt.is_active=1 AND welding_ndt.is_deleted=0 AND welding_ndt.project_id='.Yii::$app->user->identity->project_id)
        ->leftJoin('welding_parameter_check','welding.weld_number=welding_parameter_check.weld_number AND 
        welding_parameter_check.is_active=1 AND welding_parameter_check.is_deleted=0 AND welding_parameter_check.project_id='.Yii::$app->user->identity->project_id)
        ->leftJoin('welding_repair','welding.weld_number=welding_repair.weld_number AND 
        welding_repair.is_active=1 AND welding_repair.is_deleted=0 AND welding_repair.project_id='.Yii::$app->user->identity->project_id)->active();

        //code for generate excel file using  https://github.com/codemix/yii2-excelexport this extension
        $file = \Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [        
                'Active Users' => [
                    'class' => 'codemix\excelexport\ActiveExcelSheet',
                    'query' => $query, 
                ],
            ],
        ]);
        $filename=time().'weldbook.xlsx';
        $path= \Yii::getAlias('@webroot').'/excel/'.$filename;
        $dPath=Url::base().'/excel/'.$filename;
        $file->saveAs($path);
        echo json_encode(array('status'=>true,'file'=>$dPath));die;
    }

    private function x($sequence){
        $sql = "SELECT  kp from welding as w where `w`.`has_been_cut_out` = 'NO' AND `w`.`is_deleted`=0 AND `w`.`is_active`=1  AND w.sequence = 0 AND w.project_id =".Yii::$app->user->identity->project_id;
        $data = \Yii::$app->db->createCommand($sql)->queryAll();
        if($data){
            $lkp = min(\yii\helpers\ArrayHelper::getColumn($data,'kp'));
        }else{
            $lkp = "-";
        }
        

        $sql = "SELECT  w.weld_number from welding as w where `w`.`has_been_cut_out` = 'NO' AND `w`.`is_deleted`=0 AND `w`.`is_active`=1 AND w.kp = '".$lkp."' AND w.sequence = 0  AND w.project_id =".Yii::$app->user->identity->project_id;
        $data = \Yii::$app->db->createCommand($sql)->queryAll();
        if($data){
            $lweld = min(\yii\helpers\ArrayHelper::getColumn($data,'weld_number'));
        }else{
            $lweld = "-";
        }

        $weld = \app\models\Welding::find()->select(['id', 'sequence', 'pipe_number', 'next_pipe', 'has_been_cut_out'])->where(['weld_number' => $lweld,'kp' => $lkp, 'has_been_cut_out' => 'NO'])->active()->one();
        if($weld){ 
            $weld->sequence =  $sequence+1;
            $weld->save(false);
            return $weld;
        }else{
            return false;
        }
    }
    public function actionSequence(){
        $weldList = \app\models\Welding::find()->select(['id', 'sequence', 'has_been_cut_out'])->active()->all();
        foreach($weldList as $key => $weld){
            $weld->sequence = 0;
            $weld->save(false);
        }
        $sequence = 0;
        $callX = true;
        $flag =  true;
        $in = 0;
        do{            
            if($callX){
                $callX = false;
                
                $weld = $this->x($sequence);
                if($weld){
                    $sequence = $weld->sequence;
                    $kp = $weld->kp;
                    $pipe_number = $weld->next_pipe;                    
                }else{
                    $flag = false;
                    $pipe_number = "-";
                    $kp = "-";
                }
            }
            $weldList = \app\models\Welding::find()->select(['id', 'kp', 'sequence', 'pipe_number', 'next_pipe', 'has_been_cut_out'])->where(['pipe_number' => $pipe_number, 'has_been_cut_out' => 'No'])->active()->one();            
            if($weldList){
                // if($weldList->has_been_cut_out == 'No'){
                    $weldList->sequence = $sequence + 1;
                    $weldList->save(false);

                    $sequence = $weldList->sequence;
                    $kp = $weldList->kp;
                    $pipe_number = $weldList->next_pipe;
                // } else {
                //     $weldList->sequence = '1000001';
                //     $weldList->save(false);
                //     $sequence = $sequence - 1;
                //     $callX = true;
                // }
            }else{    
                $callX = true;
            }
            $in++;
            // if($in > 600){
            //     $flag =  false;
            // }
        }while($flag);

        //set all cut out weld sequence to 1000001
        $weldList = \app\models\Welding::find()->select(['id', 'sequence'])->where(['has_been_cut_out' => 'Yes'])->active()->all();
        if(!empty($weldList)){
            foreach($weldList as $w){
                $w->sequence = '1000001';
                $w->save(false);
            }
        }
        
        return true;
    }

    public function actionSequence_temp(){
        //set sequence 0 at first;
        $weldList = \app\models\Welding::find()->active()->all();
        foreach($weldList as $key => $weld){
            $weld->sequence = 0;
            $weld->save(false);
        }
        $weldKpList = \app\models\Welding::find()->select('kp')->where(['kp' => 2])->active()->groupBy('kp')->asArray()->all();
        if(!empty($weldKpList)){
            $sequenceFlag = 1;
            foreach($weldKpList as $kp){
                $weldList = \app\models\Welding::find()->where(['kp' => $kp['kp']])->active()->orderBy('cast(weld_number as unsigned) asc')->all();
                if(!empty($weldList)){
                    foreach($weldList as $key => $weld){
                        if($key == 0){
                            $weld->sequence = 1;
                            $weld->save(false);
                        } else {
                            $sequenceFlag++;
                            $weld->sequence = $sequenceFlag;
                            $weld->save(false);
                            
                            //check for next pipe
                            $connected = \app\models\Welding::find()->where(['pipe_number' => $weld->next_pipe, 'kp' => $weld->kp])->active()->one();
                            // $connected = \app\models\Welding::find()->where(['next_pipe' => $weld->pipe_number, 'kp' => $weld->kp])->active()->asArray()->one();
                            // if($key == 13){
                            //     echo "Sequence = ".$sequenceFlag."<br/>";
                            //     echo "<pre>";
                            //     print_r($connected);
                            //     die;
                            // }

                            // if($weld->next_pipe == '3391350'){
                            //     echo "<pre>";
                            //     print_r($connected);
                            //     die;
                            // }

                            if(!empty($connected)){
                                // $connected->sequence = $weld->sequence+1;
                                // $connected->save(false);
                                if($weld->next_pipe == '3485710'){
                                    echo "<pre>";
                                    print_r($connected);
                                    die;
                                }
                            } else {
                                // $sequenceFlag++;
                                // $weldNum = $this->getLowestWeld($kp['kp']);
                                // $weld->sequence = $sequenceFlag;
                                // $sequenceFlag = $weldNum;
                            }                            
                        }
                        // if($key == 13) die;
                    }
                }
            }
        }
    }

    public function getLowestWeld($kp){
        $weld = \app\models\Welding::find()->select('weld_number')->where(['kp' => $kp, 'sequence' => 0])->active()->orderBy('cast(weld_number as unsigned) asc')->asArray()->one();

        return $weld['weld_number'];
    }
    
    // working example - issue in reordering
    public function actionSequence_working(){
        $weldList = \app\models\Welding::find()->active()->all();
        foreach($weldList as $key => $weld){
            $weld->sequence = 0;
            $weld->save(false);
        }

        ///99% working code
        $weldList = \app\models\Welding::find()->active()->all();
        foreach($weldList as $key => $weld){
            if($key == 0){
                $weld->sequence = 1;
                $weld->save(false);
            } else {
                $lastSeqNum = $this->getLastSeqNum();
                $nextWeldDetails = \app\models\Welding::find()->where(['next_pipe' => $weld->pipe_number])->active()->one();

                // if($weld->pipe_number == '3485700'){
                //     echo "<pre>";
                //     print_r($weld);
                //     die;
                // }
                if(!empty($nextWeldDetails)){
                    $currentSeq = $nextWeldDetails->sequence;
                    if($nextWeldDetails->sequence == 0){
                        $getLastExistKpSeq = $this->getLastExistKpSequence($nextWeldDetails->kp);
                        if($getLastExistKpSeq == 0){
                            $currentSeq = $lastSeqNum;
                            $newSequence = $lastSeqNum+1;
                        } else {
                            $currentSeq = $getLastExistKpSeq;
                            $newSequence = $getLastExistKpSeq+1;
                        }
                    } else {
                        $newSequence = ($nextWeldDetails->sequence)+1;
                    }
                    
                    $weld->sequence = $newSequence;
                     //check squence already exist
                    $changeExistSequence = \app\models\Welding::find()->where(['>', 'sequence', $currentSeq])->active()->all();
                    if(!empty($changeExistSequence)){
                        foreach($changeExistSequence as $existSequence){
                            $existSequence->sequence = $existSequence->sequence+1;
                            $existSequence->save(false);
                        }
                    }
                    $weld->save(false);
                } else {
                    $currentKp = $weld->kp;
                    $kpExist = $this->checkKpExist($currentKp);
                    if($kpExist){
                        $getLastExistKpSeq = $this->getLastExistKpSequence($currentKp);
                        if($getLastExistKpSeq == 0){
                            $currentSeq = $lastSeqNum;
                            $newSequence = $lastSeqNum+1;
                        } else {
                            $currentSeq = $getLastExistKpSeq;
                            $newSequence = $getLastExistKpSeq+1;
                        }                     
                        $weld->sequence = $newSequence;

                        //check squence already exist
                        $changeExistSequence = \app\models\Welding::find()->where(['>', 'sequence', $currentSeq])->active()->all();
                        if(!empty($changeExistSequence)){
                            foreach($changeExistSequence as $existSequence){
                                $existSequence->sequence = $existSequence->sequence+1;
                                $existSequence->save(false);
                            }
                        }
                    } else {
                        $weld->sequence = $lastSeqNum+1;
                    }
                    $weld->save(false);
                }

                $firstWeldDetails = \app\models\Welding::find()->where(['pipe_number' => $weld->next_pipe])->active()->one();
                // if($weld->next_pipe == '3485700'){
                //     echo "<pre>";
                //     print_r($firstWeldDetails);
                //     die;
                // }
                if(!empty($firstWeldDetails)){
                    if($firstWeldDetails->sequence > 0 && $weld->sequence != (($firstWeldDetails->sequence)-1)){
                        $changeExistSequence = \app\models\Welding::find()->where(['>', 'sequence', $firstWeldDetails->sequence])->active()->all();
                        if(!empty($changeExistSequence)){
                            foreach($changeExistSequence as $existSequence){
                                $existSequence->sequence = $existSequence->sequence+1;
                                $existSequence->save(false);
                            }
                        }
                        $firstWeldDetails->sequence = $weld->sequence+1;
                        $firstWeldDetails->save(false);
                    }
                }
                // if($weld->next_pipe == '3485710'){
                    
                //     echo "Current = ".$weld->sequence;
                //     "=======================================";
                //     echo "<pre>";
                //     print_r($firstWeldDetails);
                //     die;
                // }
            }
        }
        return $this->redirect(['weldbook-report']);
    }

    public function actionSequence_old_1(){
        // echo Yii::$app->user->identity->project_id;die;
        $weldList = \app\models\Welding::find()->active()->all();
        foreach($weldList as $key => $weld){
            if($key == 0){
                $weldData = $this->weldData($weld->id);
                $weldData->sequence = 1;
                $weldData->save(false);
            } else {
                $lastSeqNum = $this->getLastSeqNum();
                $nextWeldDetails = \app\models\Welding::find()->where(['pipe_number' => $weld->next_pipe])->active()->one();
                if(!empty($nextWeldDetails)){
                    // $getFirstPipe = \app\models\Welding::find()->where(['next_pipe' => $weld->pipe_number])->active()->one();
                    // if(!empty($getFirstPipe)){
                        
                    // }
                    $weld->sequence = $lastSeqNum+1;
                } else {
                    $currentKp = $weld->kp;
                    $kpExist = $this->checkKpExist($currentKp);
                    if($kpExist){
                        $getLastExistKpSeq = $this->getLastExistKpSequence($currentKp);
                        $newSequence = $getLastExistKpSeq+1;
                        $weld->sequence = $newSequence;

                        //check squence already exist
                        $getExistSequence = \app\models\Welding::find()->where(['sequence' => $newSequence])->active()->one();

                        if(!empty($getExistSequence)){
                            $changeExistSequence = \app\models\Welding::find()->where(['>=', 'sequence', $newSequence])->active()->all();
                            if(!empty($changeExistSequence)){
                                foreach($changeExistSequence as $existSequence){
                                    $existSequence->sequence = $existSequence->sequence+1;
                                    $existSequence->save(false);
                                }
                            }
                        }
                    } else {
                        $weld->sequence = $lastSeqNum+1;
                    }
                }
                $weld->save(false);
            }
        }
        echo "Sequence Change Successfully.";
        die;
    }

    public function getLastExistKpSequence($kp){
        $lastSeq = \app\models\Welding::find()->where(['kp' => $kp])->active()->orderBy('sequence DESC')->asArray()->one();
        if(!empty($lastSeq)){
            return $lastSeq['sequence'];
        } else {
            return 0;
        }
    }

    public function getLastSeqNum(){
        $lastSeq = \app\models\Welding::find()->where(['!=', 'sequence', 0])->active()->orderBy('sequence DESC')->asArray()->one();
        if(!empty($lastSeq)){
            return $lastSeq['sequence'];
        } else {
            return 0;
        }
    }

    public function weldData($id){
        $wData = \app\models\Welding::find()->where(['id' => $id])->active()->one();
        if(!empty($wData)){
            return $wData;
        } else {
            return [];
        }
    }

    public function actionSequence_old(){
        $weldList = \app\models\Welding::find()->active()->asArray()->all();
        foreach($weldList as $key => $weld){
            $firstConnect = $this->checkFirstPipeConnect($weld['pipe_number']);
            $nextConnect = $this->checkNextPipeConnect($weld['next_pipe']);
            $kpExist = $this->checkKpExist($weld['kp']);
            if($key == 0){
                echo $weld['kp'].' == '.$weld['weld_number']." In Sequence ===== ".$weld['pipe_number']." ===== ".$weld['next_pipe'];
                echo "<br/>";
            } else {
                $lastWeld = $weldList[$key-1];
                if($firstConnect && $nextConnect){
                    echo $weld['kp'].' == '.$weld['weld_number']." In Sequence".$weld['pipe_number']." ===== ".$weld['next_pipe'];
                    echo "<br/>";
                } else {
                    if($kpExist){
                        echo $weld['kp'].' == '.$weld['weld_number']." New KP in Sequence & Weld not In Sequence".$weld['pipe_number']." ===== ".$weld['next_pipe'];
                        echo "<br/>";
                    } if($lastWeld['kp'] != $weld['kp']) {
                        echo $weld['kp'].' == '.$weld['weld_number']." KP & Weld both Not In Sequence".$weld['pipe_number']." ===== ".$weld['next_pipe'];
                        echo "<br/>";
                    } else {
                        echo $weld['kp'].' == '.$weld['weld_number']." KP in sequence & Weld not In Sequence".$weld['pipe_number']." ===== ".$weld['next_pipe'];
                        echo "<br/>";
                    }                    
                }
            }
        }
        die;
    }

    public function checkKpExist($kp){
        $kpAvail = \app\models\Welding::find()->where(['kp' => $kp])->active()->asArray()->one();
        if(!empty($kpAvail)){
            return true;
        } else {
            return false;
        }
    }

    public function checkFirstPipeConnect($number){
        $firstWeldDetails = \app\models\Welding::find()->where(['next_pipe' => $number])->active()->asArray()->one();
        if(!empty($firstWeldDetails)){
            return true;
        } else {
            return false;
        }
    }

    public function checkNextPipeConnect($number){
        $nextWeldDetails = \app\models\Welding::find()->where(['pipe_number' => $number])->active()->asArray()->one();
        if(!empty($nextWeldDetails)){
            return true;
        } else {
            return false;
        }
    }
}
