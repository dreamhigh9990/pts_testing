<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Production;

/**
 * ProductionSearch represents the model behind the search form of `app\models\Production`.
 */
class ProductionSearch extends Production
{
    public $download;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['date', 'report_number', 'weld_number', 'abrasive_material', 'material_batch_number', 'batch_number_a', 'batch_number_b', 'steel_adhesion', 'fbe_adhesion', 'checkpoint', 'comment', 'signed_off', 'is_anomally','outcome','why_anomally'], 'safe'],
            [['kp', 'dew_point', 'temperature', 'surface_profile', 'dft', 'dft_2', 'dft_3', 'dft_4', 'dft_5', 'dft_6'], 'number'],
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
            $query = Production::find()->anomally()->orderBy('kp ASC');;
        }else{
             $query = Production::find()->active()->orderBy('kp ASC');
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
            'dew_point' => $this->dew_point,
            'temperature' => $this->temperature,
            'surface_profile' => $this->surface_profile,
            'dft' => $this->dft,
            'outcome' => $this->outcome,
            'qa_manager' => $this->qa_manager,
            'created_by' => $this->created_by,
            'kp' => $this->kp,
        ]);

        $query->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['like', 'weld_number', $this->weld_number])
            ->andFilterWhere(['like', 'abrasive_material', $this->abrasive_material])
            ->andFilterWhere(['like', 'material_batch_number', $this->material_batch_number])
            ->andFilterWhere(['like', 'batch_number_a', $this->batch_number_a])
            ->andFilterWhere(['like', 'batch_number_b', $this->batch_number_b])
            ->andFilterWhere(['like', 'steel_adhesion', $this->steel_adhesion])
            ->andFilterWhere(['like', 'fbe_adhesion', $this->fbe_adhesion])
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
            Yii::$app->export->excelExport('coatingproduction', $dataProvider->query->all());
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