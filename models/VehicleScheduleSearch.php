<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\VehicleSchedule;

/**
 * VehicleScheduleSearch represents the model behind the search form of `app\models\VehicleSchedule`.
 */
class VehicleScheduleSearch extends VehicleSchedule
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted', 'is_active'], 'integer'],
            [['date', 'report_number', 'location', 'sca_unit_number', 'vehicle_type', 'vehicle_number', 'inspection_frequency', 'part_list', 'signed_off', 'qa_manager'], 'safe'],
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
        $query = VehicleSchedule::find()->active()->orderBy('id DESC');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = !empty($_GET['per-page']) ? $_GET['per-page'] : 10;
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'project_id' => $this->project_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'is_deleted' => $this->is_deleted,
            'is_active' => $this->is_active,
            'qa_manager' => $this->qa_manager,
        ]);

        $query->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'sca_unit_number', $this->sca_unit_number])
            ->andFilterWhere(['like', 'vehicle_type', $this->vehicle_type])
            ->andFilterWhere(['like', 'vehicle_number', $this->vehicle_number])
            ->andFilterWhere(['like', 'inspection_frequency', $this->inspection_frequency])
            ->andFilterWhere(['like', 'part_list', $this->part_list])
            ->andFilterWhere(['like', 'signed_off', $this->signed_off]);
        
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

        return $dataProvider;
    }
}
