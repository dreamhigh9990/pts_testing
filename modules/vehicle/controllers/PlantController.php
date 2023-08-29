<?php

namespace app\modules\vehicle\controllers;
use Yii;

class PlantController extends \yii\web\Controller
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
                        'actions' => ['dashboard'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionDashboard()
    {
        //for Schedule
        $scheduledVehicles = [];
        $getSchedule = \app\models\VehicleSchedule::find()->select('id')->active()->asArray()->all();
        if(!empty($getSchedule)){
            foreach($getSchedule as $scheduled){
                $scheduledVehicles[] = $scheduled['id'];
            }
        }

        // $scheduledVehicles
        $inspectedVehicles = [];
        $getInspectedSchedule = \app\models\VehicleInspection::find()->select('vehicle_id')->active()->asArray()->all();
        if(!empty($getInspectedSchedule)){
            foreach($getInspectedSchedule as $inspected){
                $inspectedVehicles[] = $inspected['vehicle_id'];
            }
        }

        $unInspectedSchedule = array_values(array_diff($scheduledVehicles, $inspectedVehicles));

        $searchModelSchedule = new \app\models\VehicleScheduleSearch();
        $searchModelSchedule->id = $unInspectedSchedule;
        $dataProviderSchedule = $searchModelSchedule->search(Yii::$app->request->queryParams);

        //for Inspection
        $searchModel = new \app\models\VehicleInspectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('dashboard', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchModelSchedule' => $searchModelSchedule,
            'dataProviderSchedule' => $dataProviderSchedule,
        ]);
    }

}
