<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Reception;

/**
 * ReceptionSearch represents the model behind the search form of `app\models\Reception`.
 */
class ReceptionSearch extends Reception
{
    public $download;
    public $length;
    public $weight;
    public $defects;
    public $od;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'qa_manager', 'created_by', 'updated_by', 'created_at', 'updated_at', 'length', 'weight'], 'integer'],
            [['od'], 'number'],
            [['date', 'report_number', 'pipe_number', 'truck', 'location', 'transferred', 'transfer_report', 'comment', 'signed_off', 'is_anomally', 'why_anomally', 'defects'], 'safe'],
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
            $query = Reception::find()->anomally()->orderBy('id DESC');;
        }else{
            $query = Reception::find()->active()->orderBy('id DESC');;
        }
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
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
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'signed_off' => $this->signed_off,
			
        ]);

        $query->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['like', 'truck', $this->truck])
            ->andFilterWhere(['like', 'transferred', $this->transferred])
            ->andFilterWhere(['IN',   'location',$this->location])
            ->andFilterWhere(['like','pipe_number',$this->pipe_number]);
		if(!empty($this->date)) {			
			list($start_date, $end_date) = explode('/', $this->date);
			$query->andFilterWhere(['between', 'date', $start_date, $end_date]);			
        }
        if($this->download){
            $dataProvider = new ActiveDataProvider([
                'query' => $query->asArray(),
                'pagination' => false,
            ]);
            Yii::$app->export->excelExport('reception', $dataProvider->query->all());
        }

        if($this->od){
            $pipeNumArray = [];
            $getPipes = \app\models\Pipe::find()->select('pipe_number')->where(['od' => $this->od])->active()->asArray()->all();
            if(!empty($getPipes)){
                foreach($getPipes as $pipe){
                    $pipeNumArray[] = $pipe['pipe_number'];
                }
            }
            if(!empty($pipeNumArray)){
                $query->andFilterWhere(['IN', 'pipe_number', $pipeNumArray]);
            } else {
                $query->andFilterWhere(['IN', 'pipe_number', ['']]);
            }
        }
        
        if($this->length){
            $pipeNumArray = [];
            $getPipes = \app\models\Pipe::find()->select('pipe_number')->where(['length' => $this->length])->active()->asArray()->all();
            if(!empty($getPipes)){
                foreach($getPipes as $pipe){
                    $pipeNumArray[] = $pipe['pipe_number'];
                }
            }
            if(!empty($pipeNumArray)){
                $query->andFilterWhere(['IN', 'pipe_number', $pipeNumArray]);
            } else {
                $query->andFilterWhere(['IN', 'pipe_number', ['']]);
            }
        }

        if($this->weight){
            $pipeNumArray = [];
            $getPipes = \app\models\Pipe::find()->select('pipe_number')->where(['weight' => $this->weight])->active()->asArray()->all();
            if(!empty($getPipes)){
                foreach($getPipes as $pipe){
                    $pipeNumArray[] = $pipe['pipe_number'];
                }
            }
            if(!empty($pipeNumArray)){
                $query->andFilterWhere(['IN', 'pipe_number', $pipeNumArray]);
            } else {
                $query->andFilterWhere(['IN', 'pipe_number', ['']]);
            }
        }

        if($this->defects){
            $pipeNumArray = [];
            $getPipes = \app\models\Pipe::find()->select('pipe_number')->where(['like', 'defects', $this->defects])->active()->asArray()->all();
            if(!empty($getPipes)){
                foreach($getPipes as $pipe){
                    $pipeNumArray[] = $pipe['pipe_number'];
                }
            }
            if(!empty($pipeNumArray)){
                $query->andFilterWhere(['IN', 'pipe_number', $pipeNumArray]);
            } else {
                $query->andFilterWhere(['IN', 'pipe_number', ['']]);
            }
        }

        if(!empty($_GET['delete-all'])){
          
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
            $row = \yii\helpers\ArrayHelper::map($dataProvider->query->asArray()->all(),'id','id');           
            Yii::$app->general->delete($this,$row);
            Yii::$app->controller->redirect('create');
        }

        $dataProvider->pagination->pageSize=!empty($_GET['per-page'])?$_GET['per-page']:10;
        // echo $query->createCommand()->getRawSql();die;
           
        return $dataProvider;
    }
}
