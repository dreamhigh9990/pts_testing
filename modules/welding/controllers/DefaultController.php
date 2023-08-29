<?php
namespace app\modules\welding\controllers;

use Yii;
use yii\web\Controller;

//models
use app\models\Welding;

/**
 * Default controller for the `welding` module
 */
class DefaultController extends Controller
{
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [                    
                    // allow authenticated users
                    [
                        'allow' => true,
                        'actions' => ['index','get-geo-code','auto-weld-number', 'get-weld-data','ndt-reject-weld',
                        'auto-weld-number-from-param','auto-weld-number-from-ndt','auto-weld-number-from-production','civil-weld-number',
                        'precom-weld-number','get-welder-by-wps','get-ndt-field'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }
    public function actionGetNdtField($weld_number=""){
        ob_start();
        $section = !empty($_GET['section'])?true:"";
        if($weld_number==""){
             Yii::$app->general->ndtfield(array(),$section); 
        }else{
            $Welding = \app\models\Welding::find()->where(['weld_number'=> $weld_number])->active()->one();       
            if(!empty($Welding['ndt_defects'])){
                Yii::$app->general->ndtfield(json_decode($Welding['ndt_defects'],true),$section); 
            }
            
        }
        $res['html'] = ob_get_clean();
        echo json_encode($res);die;
    }
    public function actionGetGeoCode($kp=""){
        $model = \app\models\Stringing::find()->where(['kp'=>$kp])->active()->asArray()->one();
        $res['code']=!empty($model['geo_location'])?$model['geo_location']:"-25.2744,133.7751";
        echo json_encode($res);die;
    }
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionAutoWeldNumber($weld_number, $kp){
        $result = array();
        if(isset($weld_number) && isset($kp)){
            $result = Yii::$app->weld->getWeldNumberSugg($weld_number, $kp);
        }
        echo json_encode($result);
        die;
    }

    public function actionAutoWeldNumberFromParam($weld_number, $kp){
        $result = array();
        if(isset($weld_number) && isset($kp)){
            $result = Yii::$app->weld->getWeldNumberSugg($weld_number, $kp);
        }
        echo json_encode($result);
        die;
        // $result = array();
        // if(isset($weld_number) && isset($kp)){
        //     $result = array();
        //     $weldList = \app\models\Parameter::find()->select(['welding_parameter_check.*','welding.weld_type','welding.weld_sub_type'])
        //     ->leftJoin('welding','welding_parameter_check.weld_number=welding.weld_number AND welding.project_id=welding_parameter_check.project_id
        //     AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)
        //     ->where(['AND',['LIKE', 'welding_parameter_check.weld_number', $weld_number],['=','welding_parameter_check.kp', $kp]])->active()->asArray()->all();
         
        //     if(!empty($weldList )){
        //         $result = $weldList;
        //     }
        // }
        // echo json_encode($result);
        // die;
    }
    public function actionAutoWeldNumberFromNdt($weld_number,$kp){
        $result = array();
        if(isset($weld_number) && isset($kp)){
            $result = array();

            $weldList = \app\models\Ndt::find()->select(['welding_ndt.*','welding.weld_type','welding.weld_sub_type'])
            ->leftJoin('welding', 'welding_ndt.weld_number = welding.weld_number AND welding_ndt.kp = welding.kp AND welding.project_id = welding_ndt.project_id AND welding.is_active = 1 AND welding.is_deleted = 0 AND welding.project_id = '.Yii::$app->user->identity->project_id)
            ->where([
                'AND',
                ['LIKE', 'welding_ndt.weld_number', $weld_number],
                ['=', 'welding_ndt.kp', $kp],
                [
                    'OR',
                    ['=', 'outcome', 'Accepted'],
                    // ['=', 'outcome', 'Repaired']
                ],
                ['=', 'welding.has_been_cut_out', 'No']
            ])->active()->asArray()->all();

            if(!empty($weldList )){
                $result = $weldList;
            }
           
        }
        echo json_encode($result);
        die;
    }
    public function actionAutoWeldNumberFromProduction($weld_number, $kp){
        $result = array();
        if(isset($weld_number) && isset($kp)){
            $result = array();
            // $weldList = \app\models\Production::find()->select(['welding_coating_production.*','welding.weld_type','welding.weld_sub_type'])
            // ->leftJoin('welding','welding_coating_production.weld_number=welding.weld_number AND welding.project_id=welding_coating_production.project_id AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)
            // ->where(['AND',['LIKE', 'welding_coating_production.weld_number', $weld_number],['=','welding_coating_production.kp', $kp],['=','outcome', 'Rejected']])->active()->asArray()->all();
            
            $weldList = \app\models\Production::find()->select(['welding_coating_production.*','welding.weld_type','welding.weld_sub_type'])
            ->leftJoin('welding','welding_coating_production.weld_number=welding.weld_number AND welding_coating_production.kp=welding.kp AND welding.project_id=welding_coating_production.project_id AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)
            ->where(['AND',['LIKE', 'welding_coating_production.weld_number', $weld_number],['=','welding_coating_production.kp', $kp],['=', 'welding.has_been_cut_out', 'No']])->active()->asArray()->all();
             if(!empty($weldList )){
                $result = $weldList;
            }
           
        }
        echo json_encode($result);
        die;
    }
    public function actionNdtRejectWeld($weld_number, $kp){
        $result = array();
        if(isset($weld_number) && isset($kp)){
            $result = array();
            $weldList = \app\models\Ndt::find()->select(['max(id) as id','weld_number', 'main_weld_id'])->where([
                'AND',
                ['LIKE', 'weld_number', $weld_number],
                ['=','welding_ndt.kp', $kp],
                [
                    'OR',
                    ['=','outcome', 'Rejected'],
                    ['=','outcome', 'Repaired'],
                    ['=','outcome', 'Accepted']
                ]
            ])->active()->groupBy('weld_number')->orderBy('id DESC')->asArray()->all();

            $weldList = Yii::$app->db->createCommand("SELECT max(id) AS `id`, `weld_number`, `main_weld_id` FROM `welding_ndt` WHERE (`weld_number` LIKE '%".$weld_number."%') AND (`welding_ndt`.`kp` = '".$kp."') AND ((`outcome` = 'Rejected') OR (`outcome` = 'Repaired') OR (`outcome` = 'Accepted')) AND ((`welding_ndt`.`is_deleted`=0) AND (`welding_ndt`.`project_id`=".Yii::$app->user->identity->project_id.") AND (`welding_ndt`.`is_active`=1)) GROUP BY `weld_number` DESC")->queryAll();
            
            if(!empty($weldList)){
                $result = $weldList;
            }
        }
        echo json_encode($result);
        die;
    }

    public function actionGetWeldData(){
        $result = array('data'=>'');
        if(isset($_POST['kp']) && isset($_POST['number'])){
            $weldDetails = Yii::$app->weld->weldingData($_POST['number'],$_POST['kp']);
            if(!empty($weldDetails)){
                $result = array('data'=>$weldDetails);
            }
        }
        echo json_encode($result);
        die;
    }

    public function actionGetWelderByWps(){
        $html = '<option>Please Select</option>';
        if(!empty($_POST['wps'])){
            $welderArray = Yii::$app->weld->getWelders($_POST['wps']);
            echo "<pre>";
            print_r($welderArray);
            die;
        }
    }

    public function actionCivilWeldNumber($weld_number, $kp, $action, $type){
        $result = array();
        if(isset($weld_number) && isset($kp)){
            $result = array();
            if($action == 'Trenching'){                
                $weldList = \app\models\Production::find()->where(['AND',['LIKE', 'weld_number', $weld_number],['=','kp', $kp],['=','outcome', 'Accepted']])->active()->asArray()->all();
                if(!empty($weldList )){
                    $result = $weldList;
                }else{
                    $result = \app\models\Coatingrepair::find()->where(['AND',['LIKE', 'weld_number', $weld_number],['=','kp', $kp]])->active()->asArray()->all();
                }
            } else if($action == 'Lowering'){
                if($type == "from"){
                    $weldList = \app\models\Trenching::find()->where(['AND',['LIKE', 'from_weld', $weld_number],['=','from_kp', $kp]])->active()->asArray()->all();
                    if(!empty($weldList )){
                        $result = $weldList;
                    }
                } else if($type == "to"){
                    $weldList = \app\models\Trenching::find()->where(['AND',['LIKE', 'to_weld', $weld_number],['=','to_kp', $kp]])->active()->asArray()->all();
                    if(!empty($weldList )){
                        $result = $weldList;
                    }
                }
            } else if($action == 'Backfilling'){
                if($type == "from"){
                    $weldList = \app\models\Lowering::find()->where(['AND',['LIKE', 'from_weld', $weld_number],['=','from_kp', $kp]])->active()->asArray()->all();
                    if(!empty($weldList )){
                        $result = $weldList;
                    }
                } else if($type == "to"){
                    $weldList = \app\models\Lowering::find()->where(['AND',['LIKE', 'to_weld', $weld_number],['=','to_kp', $kp]])->active()->asArray()->all();
                    if(!empty($weldList )){
                        $result = $weldList;
                    }
                }
            } else if($action == 'Reinstatement'){
                if($type == "from"){
                    $weldList = \app\models\Backfilling::find()->where(['AND',['LIKE', 'from_weld', $weld_number],['=','from_kp', $kp]])->active()->asArray()->all();
                    if(!empty($weldList )){
                        $result = $weldList;
                    }
                } else if($type == "to"){
                    $weldList = \app\models\Backfilling::find()->where(['AND',['LIKE', 'to_weld', $weld_number],['=','to_kp', $kp]])->active()->asArray()->all();
                    if(!empty($weldList )){
                        $result = $weldList;
                    }
                }
            }        
        }
        echo json_encode($result);
        die;
    }

    public function actionPrecomWeldNumber($weld_number, $kp, $action, $type){
        $result = array();
        if(isset($weld_number) && isset($kp)){
            $result = array();
            if($action == 'Cleangauge'){
                if($type == "from"){
                    $weldList = \app\models\Reinstatement::find()->where(['AND',['LIKE', 'from_weld', $weld_number],['=','from_kp', $kp]])->active()->asArray()->all();
                    if(!empty($weldList)){
                        $result = $weldList;
                    }
                } else if($type == "to"){
                    $weldList = \app\models\Reinstatement::find()->where(['AND',['LIKE', 'to_weld', $weld_number],['=','to_kp', $kp]])->active()->asArray()->all();
                    if(!empty($weldList)){
                        $result = $weldList;
                    }
                }
            } else if($action == 'Hydrotesting'){
                if($type == "from"){
                    $weldList = \app\models\Cleangauge::find()->where(['AND',['LIKE', 'from_weld', $weld_number],['=','from_kp', $kp]])->active()->asArray()->all();
                    if(!empty($weldList)){
                        $result = $weldList;
                    }
                } else if($type == "to"){
                    $weldList = \app\models\Cleangauge::find()->where(['AND',['LIKE', 'to_weld', $weld_number],['=','to_kp', $kp]])->active()->asArray()->all();
                    if(!empty($weldList)){
                        $result = $weldList;
                    }
                }
            }  else if($action == 'Cathodic'){
                if($type == "from"){
                    $weldList = \app\models\Reinstatement::find()->where(['AND',['LIKE', 'from_weld', $weld_number],['=','from_kp', $kp]])->active()->asArray()->all();
                    if(!empty($weldList)){
                        $result = $weldList;
                    }
                } else if($type == "to"){
                    $weldList = \app\models\Reinstatement::find()->where(['AND',['LIKE', 'to_weld', $weld_number],['=','to_kp', $kp]])->active()->asArray()->all();
                    if(!empty($weldList)){
                        $result = $weldList;
                    }
                }
            }      
        }
        echo json_encode($result);
        die;
    }
}
