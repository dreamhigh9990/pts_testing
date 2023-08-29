<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Stringing;

/**
 * StringingSearch represents the model behind the search form of `app\models\Stringing`.
 */
class StringingSearch extends Stringing
{
    public $download;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pipe_id', 'project_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['date', 'kp','report_number', 'location', 'pipe_number', 'geo_location', 'comment', 'relocated', 'transfer_report', 'signed_off', 'qa_manager', 'is_anomally', 'why_anomally'], 'safe'],
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
            $query = Stringing::find()->anomally()->orderBy('id DESC');;
        }else{
             $query = Stringing::find()->active()->orderBy('id DESC');
        }
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
            'qa_manager' => $this->qa_manager,
            'created_by' => $this->created_by,
            'signed_off' => $this->signed_off,
            'kp'=> $this->kp,
        ]);

        // if(isset($this->kp)) {
        //     $query->andWhere('FLOOR(kp)='.floor($this->kp));	
        // }

        $query->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['IN',   'location',$this->location])
            ->andFilterWhere(['like',   'pipe_number',$this->pipe_number]);
            
        if(!empty($this->date)) {
            list($start_date, $end_date) = explode('/', $this->date);
            $query->andFilterWhere(['between', 'date', $start_date, $end_date]);			
        }
        if($this->download){
            $dataProvider = new ActiveDataProvider([
                'query' => $query->asArray(),
                'pagination' => false,
            ]);
            Yii::$app->export->excelExport('stringing', $dataProvider->query->all());
        }
        
        if(!empty($_GET['delete-all'])){
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
            $row = \yii\helpers\ArrayHelper::map($dataProvider->query->asArray()->all(),'id','id');           
            Yii::$app->general->delete($this,$row);
            Yii::$app->controller->redirect('create');
        } 
        
        if(Yii::$app->controller->id!="default"){
            $dataProvider->pagination->pageSize=!empty($_GET['per-page'])?$_GET['per-page']:10;
        }

        return $dataProvider;
    }
}
