<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Coatingrepair;

/**
 * CoatingrepairSearch represents the model behind the search form of `app\models\Coatingrepair`.
 */
class CoatingrepairSearch extends Coatingrepair
{
    public $download;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted', 'is_active'], 'integer'],
            [['date', 'report_number', 'weld_number', 'type_repair', 'checkpoint', 'comment', 'signed_off', 'is_anomally', 'why_anomally'], 'safe'],
            [['ambient_temperature', 'substrate_temprature', 'humidity', 'dew_point', 'kp', 'coating_product', 'temperature', 'time'], 'number'],
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
            $query = Coatingrepair::find()->anomally()->orderBy('id DESC');;
        }else{
            $query = Coatingrepair::find()->active()->orderBy('id DESC');;
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
            'ambient_temperature' => $this->ambient_temperature,
            'substrate_temprature' => $this->substrate_temprature,
            'humidity' => $this->humidity,
            'dew_point' => $this->dew_point,
            'coating_product' => $this->coating_product,
            'temperature' => $this->temperature,
            'time' => $this->time,
            'qa_manager' => $this->qa_manager,
            'created_by' => $this->created_by,
            'kp' => $this->kp,
        ]);

        $query->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['like', 'weld_number', $this->weld_number])
            ->andFilterWhere(['like', 'type_repair', $this->type_repair])
            ->andFilterWhere(['like', 'checkpoint', $this->checkpoint])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'signed_off', $this->signed_off])
            ->andFilterWhere(['like', 'is_anomally', $this->is_anomally])
            ->andFilterWhere(['like', 'why_anomally', $this->why_anomally]);
        
        if(!empty($this->date)) {
            list($start_date, $end_date) = explode('/', $this->date);

            $query->andFilterWhere(['between', 'date', $start_date, $end_date]);			
        }

        if($this->download){
            $dataProvider = new ActiveDataProvider([
                'query' => $query->asArray(),
                'pagination' => false,
            ]);
            Yii::$app->export->excelExport('coatingrepair', $dataProvider->query->all());
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
