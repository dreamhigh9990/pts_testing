<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Hazard;

/**
 * HazardSearch represents the model behind the search form of `app\models\Hazard`.
 */
class HazardSearch extends Hazard
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['crew', 'location', 'date_time', 'details', 'action', 'supervisor_in_charged', 'report_number','is_followup'], 'safe'],
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
        $query = Hazard::find();

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'project_id' => Yii::$app->user->identity['project_id'],
            'is_deleted' => 0
        ]);

        $query->andFilterWhere(['like', 'crew', $this->crew])
            ->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'details', $this->details])
            ->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['like', 'is_followup', $this->is_followup]);

        return $dataProvider;
    }
}
