<?php
namespace app\components;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\base\Component;

class Export extends Component
{
    public function generateExcelExportButton($btnSmall = false){
        $cstClass = '';
        if($btnSmall){
            $cstClass = 'btn-sm';
        }

		return '<a href ="'.\yii\helpers\Url::current(['download' => 1]).'" data-pjax="0" target="_blank" >
            <button class ="btn '.$cstClass.' btn-outline-teal btn-white btn-min-width mr-1 mb-1 pull-right"><i class="fa fa-file-excel-o"></i> '.Yii::$app->trans->getTrans('Export to XLS').'</button>
        </a>'; 
    }
    
    public function excelExport($type, $data){
        $a = array();
        $mainHeaderKeyArray = [];
        $finalData = [];
        //predefined list
        $empList = Yii::$app->general->employeeList("");
        $projectList = Yii::$app->general->TaxonomyDrop(4, true);

        $filename = time().'_'.$type;
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename={$filename}.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        $outputBuffer = fopen("php://output", 'w');
            
        if(!empty($data)){
            foreach($data as $kEle => $ele){
                $v = array();
                $values = array();
                foreach($ele as $key => $val){
                    // general element to unset from array
                    if($key == 'id') continue;
                    if($key == 'is_anomally') continue;
                    if($key == 'why_anomally') continue;
                    if($key == 'is_deleted') continue;
                    if($key == 'is_active') continue;
                    if($key == 'updated_by') continue;
                    

                    if($type == 'pipe'){
                        if($key == 'defects') continue;

                        $v[$key] = $val;
                        if($key == 'pups'){
                            $pipeStatus = $this->pipeStatusFields($ele['pipe_number']);
                            if(!empty($pipeStatus)){
                                foreach($pipeStatus as $statusKey => $statusVal){
                                    $v[$statusKey] = $statusVal;
                                }
                            }
                        }
                    } else if($type == 'reception'){
                        if($key == 'transferred') continue;
                        if($key == 'transfer_report') continue;

                        $v[$key] = $val;
                        if($key == 'pipe_number'){
                            $receptionAdditionalData = $this->additioFieldsForReception($val);
                            if(!empty($receptionAdditionalData)){
                                foreach($receptionAdditionalData as $addiKey => $addiVal){
                                    $v[$addiKey] = $addiVal;
                                }
                            }
                        }
                    } else if($type == 'stringing'){
                        if($key == 'pipe_id') continue;
                        if($key == 'transfer_report') continue;
                        $v[$key] = $val;
                        if($key == 'pipe_number'){
                            $receptionAdditionalData = $this->additioFieldsForStringing($val);
                            if(!empty($receptionAdditionalData)){
                                foreach($receptionAdditionalData as $addiKey => $addiVal){
                                    $v[$addiKey] = $addiVal;
                                }
                            }
                        }
                    } else if($type == 'coatingrepair'){
                        $v[$key] = $val;
                        if($key == 'weld_number'){
                            $coatingRepairAdditionalData = $this->additionalFieldsForCoating($val, $ele['kp']);
                            if(!empty($coatingRepairAdditionalData)){
                                foreach($coatingRepairAdditionalData as $addiKey => $addiVal){
                                    $v[$addiKey] = $addiVal;
                                }
                            }
                        }
                    } else if($type == 'coatingproduction'){
                        // if($key == 'checkpoint') continue;
                        $v[$key] = $val;
                        if($key == 'weld_number'){
                            $coatingProductionAdditionalData = $this->additionalFieldsForCoating($val, $ele['kp']);
                            if(!empty($coatingProductionAdditionalData)){
                                foreach($coatingProductionAdditionalData as $addiKey => $addiVal){
                                    $v[$addiKey] = $addiVal;
                                }
                            }
                        }
                    } else {
                        $v[$key] = $val;
                    }

                    if($key == 'qa_manager'){
						$v[$key] = !empty($empList[$val]) ? $empList[$val] : "";
                    }
                    
                    if($key == 'created_by'){
						$v[$key] = !empty($empList[$val]) ? $empList[$val] : "";
					}
                    
                    if($key == 'created_at'){
						$v[$key] = date('Y-m-d', $val);
					}
                    
                    if($key == 'updated_at'){
						$v[$key] = date('Y-m-d', $val);
					}
					
					if($key == 'project_id'){
						$v[$key] =  !empty($projectList[$val]) ? $projectList[$val] : $val;
                    }
                }
                if(empty($mainHeaderKeyArray)){
                    $mainHeaderKeyArray = array_keys($v);
                    foreach($mainHeaderKeyArray as $kk => $vv){
                        if($vv == 'project_id') $vv = 'project_name';
                        if($vv == 'created_by') $vv = 'user';
                        if($vv == 'od') $vv = 'OD(mm)';
                        $mainHeaderKeyArray[$kk] = ucwords(str_replace('_',' ',$vv));
                    }
                    fputcsv($outputBuffer, $mainHeaderKeyArray);
                }
                
                $values = array_values($v);
                fputcsv($outputBuffer, $values);

                // array_push($a, $values);
            }

            fclose($outputBuffer);
            die;

            // if(!empty($mainHeaderKeyArray)){
			// 	foreach($mainHeaderKeyArray as $kk => $vv){
            //         if($vv == 'project_id') $vv = 'project_name';
            //         if($vv == 'created_by') $vv = 'user';
            //         if($vv == 'od') $vv = 'OD(mm)';
			// 		$mainHeaderKeyArray[$kk] = ucwords(str_replace('_',' ',$vv));
			// 	}
            // }
            
            // $finalData = array_merge(array($mainHeaderKeyArray), $a);

			// $filename = time().'_'.$type;
			// header("Content-type: application/vnd.ms-excel");
			// header("Content-Disposition: attachment; filename={$filename}.csv");
			// header("Pragma: no-cache");
			// header("Expires: 0");
			// $outputBuffer = fopen("php://output", 'w');
			// foreach($finalData as $val) {
			// 	fputcsv($outputBuffer, $val);
			// }
			// fclose($outputBuffer);die;
        }
    }

    public function additioFieldsForReception($pipeNumber){
        $pipeDetails = \app\models\Pipe::find()->select(['od', 'length', 'defects', 'weight'])->where(['pipe_number' => $pipeNumber])->active()->asArray()->one();

        $pipeLength = '';
        $pipeDefects = '';
        $pipeWeight = '';
        if(!empty($pipeDetails)){
            $pipeLength = $pipeDetails['length'];
            $defectsDecode = !empty($pipeDetails['defects']) && $pipeDetails['defects'] != '' ? json_decode($pipeDetails['defects'], true) : '';
            if(!empty($defectsDecode)){
                $pipeDefects = implode(', ', $defectsDecode);
            }
            $pipeWeight = $pipeDetails['weight'];
        }

        $respArray = array(
            'pipe_length' => $pipeLength,
            'pipe_defects' => $pipeDefects,
            'pipe_weight' => $pipeWeight
        );

        return $respArray;
    }

    public function additioFieldsForStringing($pipeNumber){
        $pipeDetails = \app\models\Pipe::find()->select(['length', 'heat_number', 'yeild_strength', 'defects'])->where(['pipe_number' => $pipeNumber])->active()->asArray()->one();

        $pipeLength = '';
        $pipeHeatNum = '';
        $pipeYeildStength = '';
        $pipeDefects = '';
        if(!empty($pipeDetails)){
            $pipeLength = $pipeDetails['length'];
            $defectsDecode = !empty($pipeDetails['defects']) && $pipeDetails['defects'] != '' ? json_decode($pipeDetails['defects'], true) : '';
            if(!empty($defectsDecode)){
                $pipeDefects = implode(', ', $defectsDecode);
            }
            $pipeHeatNum = $pipeDetails['heat_number'];
            $pipeYeildStength = $pipeDetails['yeild_strength'];
        }

        $respArray = array(
            'pipe_length' => $pipeLength,
            'pipe_heat_number' => $pipeHeatNum,
            'pipe_yeild_strength' => $pipeYeildStength,
            'pipe_defects' => $pipeDefects,
        );

        return $respArray;
    }

    public function additionalFieldsForCoating($weldNumber, $kp){
        $weldDetails = \app\models\Production::find()->select(['welding.weld_type', 'welding.weld_sub_type'])->leftJoin('welding','welding_coating_production.weld_number=welding.weld_number AND welding.project_id=welding_coating_production.project_id AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)->where(['AND',['=', 'welding_coating_production.weld_number', $weldNumber],['=','welding_coating_production.kp', $kp]])->active()->asArray()->one();

        $weldType = '';
        $weldSubType = '';
        if(!empty($weldDetails)){
            $weldType = $weldDetails['weld_type'];
            $weldSubType = $weldDetails['weld_sub_type'];
        }

        $respArray = array(
            'weld_type' => $weldType,
            'weld_sub_type' => $weldSubType,
        );

        return $respArray;
    }

    public function pipeStatusFields($pipeNum){
        $classes = [
            ['model' => '\app\models\Reception', 'url' => '/pipe/reception/create', 'des' => 'Receipted?'],
            ['model' => '\app\models\Stringing', 'url' => '/pipe/stringing/create', 'des' => 'Strung?'],
            ['model' => '\app\models\Cutting', 'url' => '/pipe/cutting/create', 'des' => 'Cut?'],
            ['model' => '\app\models\Bending', 'url' => '/pipe/bending/create', 'des' => 'Bent?']
        ];

        $statusArray = [];
        foreach($classes as  $className){
            if ($className['model']=="\app\models\Cutting") {
                $pipeNumber = explode("/", $pipeNum);
                $pipeNumber = $pipeNumber[0];
            }else{
                $pipeNumber = $pipeNum;
            }

            $loaded = $className['model']::find()->select(['id'])->where(['pipe_number' => $pipeNumber])->active()->one();
            if($loaded){
                $statusArray[$className['des']] = 'Yes';
            } else {
                $statusArray[$className['des']] = 'No';
            }
        }

        // for weld
        $currentPipeWeld = \app\models\Welding::find()->select(['id'])->where(['pipe_number' => $pipeNum])->active()->one();
        $prevPipeWeld = \app\models\Welding::find()->select(['id'])->where(['next_pipe' => $pipeNum])->active()->one();

        $weldStatus = '';
        if(!empty($prevPipeWeld) && !empty($currentPipeWeld)){
            $weldStatus = 'Both';
        } else if(empty($prevPipeWeld) && empty($currentPipeWeld)){
            $weldStatus = 'No';
        } else if(!empty($prevPipeWeld) && empty($currentPipeWeld)){
            $weldStatus = 'Single Side';
        } else if(empty($prevPipeWeld) && !empty($currentPipeWeld)){
            $weldStatus = 'Single Side';
        }

        $statusArray['Weld?'] = $weldStatus;

        return $statusArray;
    }
}
?>