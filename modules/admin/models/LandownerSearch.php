<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Landowner;

/**
 * LandownerSearch represents the model behind the search form of `app\modules\admin\models\Landowner`.
 */
class LandownerSearch extends Landowner
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by', 'created_at', 'updated_at','project_id'], 'integer'],
            [['landholder', 'site_reference', 'fencing_details', 'gate_management', 'stock_impact', 'vegetation_impact', 'weed_hygiene', 'sign_offs', 'from_geo_code', 'form_geo_code', 'comment'], 'safe'],
            [['from_kp', 'to_kp'], 'number'],
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
        $query = Landowner::find();

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
            'from_kp' => $this->from_kp,
            'to_kp' => $this->to_kp,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
			'project_id'=> $this->project_id
        ]);

        $query->andFilterWhere(['like', 'landholder', $this->landholder])
            ->andFilterWhere(['like', 'site_reference', $this->site_reference])
            ->andFilterWhere(['like', 'fencing_details', $this->fencing_details])
            ->andFilterWhere(['like', 'gate_management', $this->gate_management])
            ->andFilterWhere(['like', 'stock_impact', $this->stock_impact])
            ->andFilterWhere(['like', 'vegetation_impact', $this->vegetation_impact])
            ->andFilterWhere(['like', 'weed_hygiene', $this->weed_hygiene])
            ->andFilterWhere(['like', 'sign_offs', $this->sign_offs])
            ->andFilterWhere(['like', 'from_geo_code', $this->from_geo_code])
            ->andFilterWhere(['like', 'form_geo_code', $this->form_geo_code])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
