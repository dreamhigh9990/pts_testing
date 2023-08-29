<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Landowner;

/**
 * LandownerSearch represents the model behind the search form of `app\models\Landowner`.
 */
class LandownerSearch extends Landowner
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['landholder', 'site_reference', 'foregin_service','fencing_details', 'gate_management', 'stock_impact', 'vegetation_impact', 'weed_hygiene', 'signed_off', 'from_geo_code', 'to_geo_code', 'comment', 'qa_manager'], 'safe'],
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
        $query = Landowner::find()->active();

        // add conditions that should always apply here

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
            'qa_manager' => $this->qa_manager,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'signed_off', $this->signed_off]);
        $query->andFilterWhere(['like', 'landholder', $this->landholder]);

        $query->andFilterWhere(['AND',['=', 'from_kp', $this->from_kp],['=', 'to_kp', $this->to_kp]]);
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
