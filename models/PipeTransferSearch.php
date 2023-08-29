<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PipeTransfer;

/**
 * PipeTransferSearch represents the model behind the search form of `app\models\PipeTransfer`.
 */
class PipeTransferSearch extends PipeTransfer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pipe_id', 'project_id', 'created_at', 'updated_at'], 'integer'],
            [['report_number', 'pipe_number', 'new_location', 'current_location', 'truck', 'comment', 'signed_off', 'qa_manager', 'date', 'is_anomally', 'why_anomally','created_by'], 'safe'],
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
            $query = PipeTransfer::find()->anomally()->orderBy('id DESC');;
        }else{
            $query = PipeTransfer::find()->active()->orderBy('id DESC');;
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
            'truck' => $this->truck,
            'created_by' => $this->created_by,
        ]);
		//echo $this->date;die;
		// do we have values? if so, add a filter to our query
        $query->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['like', 'pipe_number', $this->pipe_number])
            ->andFilterWhere(['IN', 'new_location', $this->new_location])
            ->andFilterWhere(['like', 'signed_off', $this->signed_off])
            ->andFilterWhere(['like', 'qa_manager', $this->qa_manager]);
		if(!empty($this->date)) {			
			list($start_date, $end_date) = explode('/', $this->date);
			$query->andFilterWhere(['between', 'date', $start_date, $end_date]);			
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
