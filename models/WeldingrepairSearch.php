<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Weldingrepair;

/**
 * WeldingrepairSearch represents the model behind the search form of `app\models\Weldingrepair`.
 */
class WeldingrepairSearch extends Weldingrepair
{
   
    public function rules()
    {
        return [
            [['id', 'project_id', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['date', 'report_number', 'is_anomally', 'signed_off', 'weld_number', 'weld_sub_type', 'welder', 'wps', 'electrodes', 'examination', 'excavation', 'repair_examination', 'comment'], 'safe'],
            [['kp'], 'number'],
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
            $query = Weldingrepair::find()->anomally()->orderBy('id DESC');;
        }else{
            $query = Weldingrepair::find()->active()->orderBy('id DESC');;
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
            'id' => $this->id,
            'project_id' => $this->project_id,
            'qa_manager' => $this->qa_manager,
            'created_by' => $this->created_by,
            'kp' => $this->kp,
        ]);

        $query->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['like', 'is_anomally', $this->is_anomally])
            ->andFilterWhere(['like', 'signed_off', $this->signed_off])
            ->andFilterWhere(['like', 'weld_number', $this->weld_number])
            ->andFilterWhere(['like', 'weld_sub_type', $this->weld_sub_type])
            ->andFilterWhere(['like', 'welder', $this->welder])
            ->andFilterWhere(['like', 'wps', $this->wps])
            ->andFilterWhere(['like', 'electrodes', $this->electrodes])
            ->andFilterWhere(['like', 'examination', $this->examination])
            ->andFilterWhere(['like', 'excavation', $this->excavation])
            ->andFilterWhere(['like', 'repair_examination', $this->repair_examination])
            ->andFilterWhere(['like', 'comment', $this->comment]);
        
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
        // if(isset($this->kp)) {
        //     $query->andWhere('FLOOR(kp)='.floor($this->kp));	
        // }

        return $dataProvider;
    }
}