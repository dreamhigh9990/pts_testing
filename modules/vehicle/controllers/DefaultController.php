<?php

namespace app\modules\vehicle\controllers;

use yii\web\Controller;

/**
 * Default controller for the `vehicle` module
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [ 
                    [
                        'allow' => true,
                        'actions' => ['index', 'vehicle-auto-list', 'vehicle-part-list', 'check-availability', 'update', 'delete', 'unique-part-barcode', 'unique-vehicle-number'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * get autocomplete list of vehicle according to their number
     * @return mixed
     */
    public function actionVehicleAutoList($vehicle_number){
        $list = \app\models\VehicleSchedule::find()->where(['AND',['LIKE', 'vehicle_number', $vehicle_number],['=', 'in_use', 'Yes']])->active()->limit(20)->asArray()->all();	
        echo json_encode($list);
        die;
    }

    /**
     * get selected parts of vehicle
     * @return mixed
     */
    public function actionVehiclePartList(){
        $list = [];
        if(!empty($_POST['vehicle'])){
            $vehicle = $_POST['vehicle'];
            $getPartList = \app\models\VehicleSchedule::find()->where(['id' => $vehicle])->active()->asArray()->one();
            if(!empty($getPartList)){
                $partListJsn = $getPartList['part_list'];
                $partListArray = json_decode($partListJsn, true);

                if(!empty($partListArray)){
                    foreach($partListArray as $key => $parts){
                        $getPartName = \app\models\TaxonomyValue::find()->where(['id' => $parts['part']])->active()->asArray()->one();
                        if(!empty($getPartName)){
                            $list[$key]['id'] = $parts['part'];
                            $list[$key]['name'] = $getPartName['value'];
                            $getPartQuestion = \app\models\MapPartQuestion::find()->select('id, question')->where(['part_id' => $parts['part']])->asArray()->all();
                            $allQue = [];
                            if(!empty($getPartQuestion)){
                                foreach($getPartQuestion as $k => $val){
                                    $allQue[$k]['que_id'] = $val['id'];
                                    $allQue[$k]['question'] = $val['question'];
                                }
                            }
                            $list[$key]['questions'] = $allQue;
                        }
                    }
                }
            }
        }

        $res['list'] = $list;

        echo json_encode($res);
        die;
    }

    /**
     * check vehicle number exist or not
     * @return boolean
     */
    public function actionCheckAvailability(){
        if(!empty($_POST['number'])){
            $number = $_POST['number'];
            $getDetails = \app\models\VehicleSchedule::find()->where(['vehicle_number' => $number])->active()->asArray()->one();
            if(empty($getDetails)){
                $res['status'] = false;
                $res['message'] = 'Vehicle number you entered is not found in system.';
            } else {
                $res['status'] = true;
            }
        } else {
            $res['status'] = true;
        }

        echo json_encode($res);
        die;
    }
}