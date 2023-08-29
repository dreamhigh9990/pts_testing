<?php
namespace app\components;

use Yii;
//helpers
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

//base
use yii\base\Component;
use yii\base\InvalidConfigException;

//models
use app\models\Stringing;
use app\models\Welding;
use app\models\Parameter;
use app\models\Ndt;
use app\models\Production;
use app\models\Weldingrepair;
use app\models\Coatingrepair;
use app\models\Taxonomy;
use app\models\TaxonomyValue;
use app\models\TaxonomyValueValue;

class Weld extends Component {
    public function getElectrods($id){
        $Taxarray = [];
        if(!empty($id)){
            $TaxonomyValueList = TaxonomyValueValue::find()->where(['parent_id'=>$id])->asArray()->all();
            if(!empty($TaxonomyValueList)){
                foreach($TaxonomyValueList as $ele){
                    $TaxonomyValueDetail = TaxonomyValue::find()->where(['id'=>$ele['child_id'],'project_id'=>Yii::$app->user->identity->project_id])->active()->asArray()->one();
					$Taxarray[$TaxonomyValueDetail['value']] = $TaxonomyValueDetail['value'];
                }
            }
        }
        return $Taxarray;  
    }

    public function getWelders($id){
        $Taxarray = [];
        if(!empty($id)){
            $TaxonomyValueList = TaxonomyValueValue::find()->where(['child_id'=>$id])->asArray()->all();
            if(!empty($TaxonomyValueList)){
                foreach($TaxonomyValueList as $ele){
                    $TaxonomyValueDetail = TaxonomyValue::find()->where(['id'=>$ele['parent_id'],'project_id'=>Yii::$app->user->identity->project_id])->active()->asArray()->one();
					$Taxarray[$TaxonomyValueDetail['value']] = $TaxonomyValueDetail['value'];
                }
            }
        }
        return $Taxarray;        
    }
    public function checkKpWeldNumber($kp, $num){
        $result = 0;
		if(isset($kp) && !empty($num)){
            $weldCount = Welding::find()->where(['kp' => $kp, 'weld_number' => $num, 'has_been_cut_out' => 'No'])->active()->count();
			$result = $weldCount;
		}
		return $result;
    }

    // public function checkWeldCrossing($kp){
    //     $result = 0;
	// 	if(isset($kp)){
    //         $list = Welding::find()->where('kp = :kp and weld_type != :weld_type', ['kp'=> $kp, 'weld_type' => 'W'])->active()->asArray()->all();
    //         if(!empty($list)){
	// 			$result = count($list);
	// 		}
	// 	}
	// 	return $result;
    // }

    public function checkWeldCrossing($kp, $weld){
        $result = 0;
		if(isset($weld)){
            $getlastWeld = Welding::find()->where(['kp' => $kp])->orderBy('id DESC')->active()->asArray()->one();
            if(!empty($getlastWeld)){
                $weldType = $getlastWeld['weld_type'];
                if($weldType == "W" && $weldType != $weld){
                    $getWeldCross = Welding::find()->where(['kp' => $kp])->orderBy('weld_crossing DESC')->active()->asArray()->one();
                    if(!empty($getWeldCross)){
                        $result = $getWeldCross['weld_crossing']+1;
                    } else {
                        $result = 1;
                    }
                } else {
                    $result = !empty($getlastWeld['weld_crossing']) ? $getlastWeld['weld_crossing'] : 1;
                }
            } else {
                $result = 1;
            }
		}
		return $result;
    }

    public function getWeldNumberSugg($num, $kp){
        $result = array();
        if(!empty($num) && isset($kp)){
            $weldList = Welding::find()->where(['AND',['LIKE', 'weld_number', $num], ['=','kp', $kp], ['=', 'has_been_cut_out', 'No']])->active()->asArray()->all();
            if(!empty($weldList )){
                $result = $weldList;
            }
        }
        return $result;
    }

    public function weldingData($num, $kp){
        $result = array();
        if(!empty($num) && isset($kp)){
            $weldDetails = Welding::find()->where(['kp'=>$kp, 'weld_number'=>$num, 'has_been_cut_out' => 'No'])->active()->asArray()->one();
            if(!empty($weldDetails)){
                $result = $weldDetails;
            }
        }
        return $result;
    }
    public function ndtData($num, $kp){
        $result = array();
        if(!empty($num) && isset($kp)){
            $ndtDetails = Ndt::find()->where(['kp'=>$kp, 'weld_number'=>$num])->active()->orderBy('id DESC')->asArray()->one();
            if(!empty($ndtDetails)){
                $result = $ndtDetails;
            }
        }
        return $result;
    }

    public function getLastRecords($type){
        $data = array();
        if($type == 'weld'){
            $data = Welding::find()->where(['updated_by'=>Yii::$app->user->identity->id])->orderBy('id DESC')->asArray()->active()->one();         
        } else if($type == 'parameter'){
            $data = Parameter::find()->where(['updated_by'=>Yii::$app->user->identity->id])->orderBy('id DESC')->asArray()->active()->one();
        } else if($type == 'ndt'){
            $data = Ndt::find()->where(['updated_by'=>Yii::$app->user->identity->id])->orderBy('id DESC')->asArray()->active()->one();
        } else if($type == 'production'){
            $data = Production::find()->where(['updated_by'=>Yii::$app->user->identity->id])->orderBy('id DESC')->asArray()->active()->one();
        } else if($type == 'weldingrepair'){
            $data = Weldingrepair::find()->where(['updated_by'=>Yii::$app->user->identity->id])->orderBy('id DESC')->asArray()->active()->one();
        } else if($type == 'coatingrepair'){
            $data = Coatingrepair::find()->where(['updated_by'=>Yii::$app->user->identity->id])->orderBy('id DESC')->asArray()->active()->one();
        }else if($type == 'stringing'){
            $data = \app\models\Stringing::find()->where(['updated_by'=>Yii::$app->user->identity->id])->orderBy('id DESC')->asArray()->active()->one();
        }else if($type == 'reception'){
                $data = \app\models\Reception::find()->where(['updated_by'=>Yii::$app->user->identity->id])->orderBy('id DESC')->asArray()->active()->one();
        }else if($type == 'cleargrade'){
            $data = \app\models\Cleargrade::find()->where(['updated_by'=>Yii::$app->user->identity->id])->orderBy('id DESC')->asArray()->active()->one();
        }              
        return $data;
    }

    public function removeWeld($weldNumber){
        $listModels = array(
            'welding' => 'app\models\Welding',
            'parameter' => 'app\models\Parameter',
            'weldingrepair' => 'app\models\Weldingrepair',
            'production' => 'app\models\Production',
            'coatingrepair' => 'app\models\Coatingrepair',
            'cleangauge' => 'app\models\Cleangauge',
            'hydrotesting' => 'app\models\Hydrotesting',
            'trenching' => 'app\models\Trenching',
            'lowering' => 'app\models\Lowering',
            'backfilling' => 'app\models\Backfilling',
            'reinstatement' => 'app\models\Reinstatement'
        );

        foreach ($listModels as $key => $value) {
            if($key == 'welding' || $key == 'parameter' || $key == 'weldingrepair' || $key == 'production' ||$key == 'coatingrepair'){
                $weldDetails = $value::find()->where(['weld_number'=>$weldNumber])->active()->one();
            } else {
                $weldDetails = $value::find()->where(['OR',['from_weld'=>$weldNumber],['to_weld'=>$weldNumber]])->active()->one();
            }
            if(!empty($weldDetails)){
                $weldDetails->is_deleted = 1;
                $weldDetails->save(false);
            }
        }
    }
    public function defectCount($Defect){
           return \app\models\Ndt::find()->where(['LIKE','ndt_defects',$Defect])->active()->count();
    }
    public function getNextKp($kp){
        $kpList     = ArrayHelper::map(\app\models\Stringing::find()->select(['kp'])->active()->asArray()->all(),'kp','kp');
        $currentKey = key($kpList);
        while ($currentKey !== null && $currentKey != $kp) {
            next($kpList);
            $currentKey = key($kpList);
        }
        return next($kpList);

    }

    public function getWeldByKpAndWeldNum($kp, $weldNumber){
        $getWeldData = \app\models\Welding::find()->where(['kp' => $kp, 'weld_number' => $weldNumber, 'has_been_cut_out' => 'No'])->active()->asArray()->one();

        return $getWeldData;
    }
}
?>