<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Parameter;

/**
 * ParameterSearch represents the model behind the search form of `app\models\Parameter`.
 */
class ParameterSearch extends Parameter
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['date', 'report_number', 'weld_number', 'welder', 'preheat', 'gas_flow', 'pass_number', 'amps', 'volt', 'rol', 'travel', 'hit', 'comment', 'signed_off', 'is_anomally', 'why_anomally'], 'safe'],
            [['kp', 'rot', 'k_factor', 'wire_speed'], 'number'],
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
            $query = Parameter::find()->anomally()->orderBy('id DESC');;
        }else{
             $query = Parameter::find()->active()->orderBy('id DESC');
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
            'id' => $this->id,
            'project_id' => $this->project_id,
            'qa_manager' => $this->qa_manager,
            'created_by' => $this->created_by,
            'rot' => $this->rot,
            'k_factor' => $this->k_factor,
            'wire_speed' => $this->wire_speed,
            'kp' => $this->kp,
        ]);

        $query->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['like', 'weld_number', $this->weld_number])
            ->andFilterWhere(['like', 'welder', $this->welder])
            ->andFilterWhere(['like', 'preheat', $this->preheat])
            ->andFilterWhere(['like', 'gas_flow', $this->gas_flow])
            ->andFilterWhere(['like', 'pass_number', $this->pass_number])
            ->andFilterWhere(['like', 'amps', $this->amps])
            ->andFilterWhere(['like', 'volt', $this->volt])
            ->andFilterWhere(['like', 'rol', $this->rol])
            ->andFilterWhere(['like', 'travel', $this->travel])
            ->andFilterWhere(['like', 'hit', $this->hit])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'signed_off', $this->signed_off])
            ->andFilterWhere(['like', 'is_anomally', $this->is_anomally])
            ->andFilterWhere(['like', 'why_anomally', $this->why_anomally]);
        
        if(!empty($this->date)) {
            list($start_date, $end_date) = explode('/', $this->date);

            $query->andFilterWhere(['between', 'date', $start_date, $end_date]);			
        }

        // if(isset($this->kp)) {
        //     $query->andWhere('FLOOR(kp)='.floor($this->kp));	
        // }
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