<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Cutting;
class CuttingSearch extends Cutting
{
    
    public function rules()
    {
        return [
            [['id', 'pipe_id', 'qa_manager', 'project_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['report_number', 'pipe_number', 'retain_pipe_number', 'signed_off', 'date', 'is_anomally', 'why_anomally'], 'safe'],
            [['length_1', 'length_2'], 'number'],
        ];
    }
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    public function search($params)
    {
        if(Yii::$app->controller->module->id == "admin" && Yii::$app->controller->id == "anomaly"){
            $query = Cutting::find()->anomally()->orderBy('id DESC');;
        }else{
            $query = Cutting::find()->active()->orderBy('id DESC');;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if(Yii::$app->controller->id!="default"){
            $dataProvider->pagination->pageSize=!empty($_GET['per-page'])?$_GET['per-page']:10;
        }
        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'created_by' => $this->created_by,
            'qa_manager' => $this->qa_manager,
        ]);

        $query->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['like', 'pipe_number', $this->pipe_number])
            ->andFilterWhere(['like', 'signed_off', $this->signed_off]);
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
