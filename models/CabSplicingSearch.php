<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CabSplicing;

/**
 * CabSplicingSearch represents the model behind the search form of `app\models\CabSplicing`.
 */
class CabSplicingSearch extends CabSplicing
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['date', 'report_number', 'next_drum', 'drum_number', 'power_meter_1', 'power_meter_2', 'light_source', 'geo_location', 'comment', 'signed_off', 'is_anomally', 'why_anomally'], 'safe'],
            [['splice_number', 'kp'], 'number'],
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
       
        if(Yii::$app->controller->module->id == "admin" && Yii::$app->controller->id == "anomaly"){
             $query = CabSplicing::find()->anomally()->orderBy('id DESC');;
        }else{
             $query = CabSplicing::find()->active()->orderBy('id DESC');;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if(Yii::$app->controller->id!="default"){
            $dataProvider->pagination->pageSize=!empty($_GET['per-page'])?$_GET['per-page']:10;
        }

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'splice_number' => $this->splice_number,
            'kp' => $this->kp,
            'qa_manager' => $this->qa_manager,
            'updated_by' => $this->updated_by,
            'signed_off' => $this->signed_off,
        ]);

        $query->andFilterWhere(['like', 'report_number', $this->report_number])
                ->andFilterWhere(['like', 'next_drum', $this->next_drum])
                ->andFilterWhere(['like', 'drum_number', $this->drum_number])
                ->andFilterWhere(['like', 'power_meter_1', $this->power_meter_1])
                ->andFilterWhere(['like', 'power_meter_2', $this->power_meter_2])
                ->andFilterWhere(['like', 'light_source', $this->light_source]);
        if(!empty($this->date)) {			
                list($start_date, $end_date) = explode('/', $this->date);
                $query->andFilterWhere(['between', 'date', $start_date, $end_date]);			
        }         
        if(!empty($_GET['delete-all'])){
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
            $row = \yii\helpers\ArrayHelper::map($dataProvider->query->asArray()->all(),'id','id');           
            Yii::$app->general->delete($this,$row);
            Yii::$app->controller->redirect('create');
        }
        return $dataProvider;
    }
}
