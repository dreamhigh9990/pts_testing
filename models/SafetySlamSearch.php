<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SafetySlam;

/**
 * SafetySlamSearch represents the model behind the search form of `app\models\SafetySlam`.
 */
class SafetySlamSearch extends SafetySlam
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by', 'updated_at'], 'integer'],
            [['name', 'crew', 'location', 'task', 'date_time','report_number','potential_hazards', 'created_at'], 'safe'],
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
        $query = SafetySlam::find();

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
            'id' => $this->id,        
            'date_time' => $this->date_time,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'project_id' => Yii::$app->user->identity['project_id'],
            'is_deleted' => 0
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'crew', $this->crew])
            ->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'task', $this->task])
            ->andFilterWhere(['like', 'report_number', $this->report_number]);

        return $dataProvider;
    }
}
