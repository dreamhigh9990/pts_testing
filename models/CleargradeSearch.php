<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Cleargrade;

/**
 * PipeCleargradeSearch represents the model behind the search form of `app\models\PipeCleargrade`.
 */
class CleargradeSearch extends Cleargrade
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'created_at', 'updated_at'], 'integer'],
            [['report_number', 'location', 'start_geo_location', 'end_geo_location', 'signed_off', 'qa_manager', 'check_points', 'comment', 'is_anomally', 'why_anomally', 'date','created_by'], 'safe'],
            [['start_kp', 'end_kp'], 'number'],
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
            $query = Cleargrade::find()->anomally()->orderBy('start_kp DESC');
        }else{
            $query = Cleargrade::find()->active()->orderBy('start_kp DESC');
        }
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
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['IN', 'location', $this->location])
            ->andFilterWhere(['like', 'start_geo_location', $this->start_geo_location])
            ->andFilterWhere(['like', 'end_geo_location', $this->end_geo_location])
            ->andFilterWhere(['like', 'signed_off', $this->signed_off])
            ->andFilterWhere(['like', 'qa_manager', $this->qa_manager]);
		if(!empty($this->date)) {			
			list($start_date, $end_date) = explode('/', $this->date);
			$query->andFilterWhere(['between', 'date', $start_date, $end_date]);			
        }
        $query->andFilterWhere([
            'AND',
            [
                'OR',
                ['>', 'start_kp', $this->start_kp],
                ['>', 'end_kp', $this->start_kp]
            ],
            [
                'OR',
                ['<', 'start_kp', $this->end_kp],
                ['<', 'end_kp', $this->end_kp]
            ]
        ]);

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
