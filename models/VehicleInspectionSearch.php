<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\VehicleInspection;

/**
 * VehicleInspectionSearch represents the model behind the search form of `app\models\VehicleInspection`.
 */
class VehicleInspectionSearch extends VehicleInspection
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'odometer_reading', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted', 'is_active'], 'integer'],
            [['date', 'report_number', 'location', 'service_due', 'geolocation', 'signed_off', 'vehicle_filter', 'vehicle_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = VehicleInspection::find()->leftJoin('vehicle_schedule','vehicle_schedule.id = vehicle_inspection.vehicle_id')->where(['in_use' => 'Yes'])->active()->orderBy('id DESC');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'vehicle_inspection.project_id' => $this->project_id,
            // 'vehicle_id' => $this->vehicle_id,
            'odometer_reading' => $this->odometer_reading,
            'vehicle_inspection.qa_manager' => $this->qa_manager,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'vehicle_inspection.created_by' => $this->created_by,
            'vehicle_inspection.updated_by' => $this->updated_by,
            'vehicle_inspection.is_deleted' => $this->is_deleted,
            'vehicle_inspection.is_active' => $this->is_active,
        ]);

        $query->andFilterWhere(['like', 'vehicle_inspection.report_number', $this->report_number])
            ->andFilterWhere(['like', 'vehicle_inspection.location', $this->location])
            // ->andFilterWhere(['like', 'service_due', $this->service_due])
            ->andFilterWhere(['like', 'geolocation', $this->geolocation])
            ->andFilterWhere(['like', 'vehicle_inspection.signed_off', $this->signed_off]);
        
        if(Yii::$app->controller->id != 'plant'){
            $query->andFilterWhere(['like', 'service_due', $this->service_due]);
        }

        // for vehicle number filter
        if(!empty($this->vehicle_id)){
            $getVehicleList = \app\models\VehicleSchedule::find()->select('id')->where(['LIKE', 'vehicle_number', $this->vehicle_id])->active()->asArray()->all();
            $vehicleIds = [];
            if(!empty($getVehicleList)){
                foreach($getVehicleList as $vehicle){
                    $vehicleIds[] = $vehicle['id'];
                }
                $query->andFilterWhere(['IN', 'vehicle_id', $vehicleIds]);
            } else {
                $query->andFilterWhere(['=', 'vehicle_id', 0]);
            }
        }
        
        if(!empty($this->date)) {
            $expDate = explode('/', $this->date);
            if(!empty($expDate) && count($expDate) > 1){
                list($start_date, $end_date) = $expDate;
                $query->andFilterWhere(['between', 'date', $start_date, $end_date]);
            }
            
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
        }

        if(!empty($_GET['delete-all'])){
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
            $row = \yii\helpers\ArrayHelper::map($dataProvider->query->asArray()->all(), 'id', 'id');
            Yii::$app->general->delete($this, $row);
            Yii::$app->controller->redirect('create');
        }

        //for filters on plant dashboard page
        if(Yii::$app->controller->id == 'plant'){
            $query->andFilterWhere(['=', 'vehicle_inspection.signed_off', 'No']);
            if(!empty($this->vehicle_filter)){
                $currentFilter = $this->vehicle_filter;
                //if issue present
                if($currentFilter == 'issues_present'){
                    $getMapDetails = \app\models\MapPartVehicleInspection::find()->select('inspection_id')->where(['status' => 'Needs Attention'])->groupBy('inspection_id')->asArray()->all();
                    $ids = [];
                    if(!empty($getMapDetails)){
                        foreach($getMapDetails as $map){
                            $ids[] = $map['inspection_id'];
                        }
                    }
                    $query->andFilterWhere(['IN', 'vehicle_inspection.id', $ids]);
                }

                //if service due
                if($currentFilter == 'service_due'){
                    $query->andFilterWhere(['=', 'vehicle_inspection.service_due', 'Yes']);
                }

                //if overdue date
                if($currentFilter == 'overdue_inspection'){
                    $query->andFilterWhere([
                        'AND',
                        ['!=', 'vehicle_inspection.date', date('Y-m-d')],
                        ['!=', 'vehicle_inspection.date', date('Y-m-d', strtotime("-1 days"))]
                    ]);
                }

                //if overdue date
                if($currentFilter == 'today_inspection'){
                    $query->andFilterWhere(['=', 'vehicle_inspection.date', date('Y-m-d', strtotime("-1 days"))]);
                }
            }

            // $inspectedList = \app\models\VehicleInspection::find()->select('vehicle_id')->active()->asArray()->all();
            // $inspectedIds = [];
            // if(!empty($inspectedList)){
            //     foreach($inspectedList as $key => $val){
            //         $inspectedIds[] = $val['vehicle_id'];
            //     }
            // }

            // $unInspectedList = \app\models\VehicleSchedule::find()->where([
            //     'AND',
            //     [
            //         'NOT IN',
            //         'id',
            //         $inspectedIds
            //     ],
            // ])->active()->asArray()->all();

            // // add conditions that should always apply here

            // $dataProviderSchedule = new ActiveDataProvider([
            //     'query' => $querySchedule,
            // ]);

            // $data = array_merge($dataProviderSchedule->getModels(), $dataProvider->getModels());

            // $dataProviderFinal = new ArrayDataProvider([
            //     'allModels' => $data
            // ]);
        }

        //export to excel
        if(!empty($_GET['download'])){
            $dataProvider = new ActiveDataProvider([
                'query' => $query->asArray(),
                'pagination' => false,
            ]);
            Yii::$app->general->downloadCsvPlantDashboard($dataProvider->query->all());
        }

        // echo $query->createCommand()->getRawSql();die;

        return $dataProvider;
    }
}
