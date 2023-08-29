<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Welding;

/**
 * WeldingSearch represents the model behind the search form of `app\models\Welding`.
 */
class WeldingSearch extends Welding
{
   
    public $end_kp;
    public $end_weld;
    public function rules()
    {
        return [
            [['id', 'project_id', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'sequence'], 'integer'],
            [['date', 'report_number', 'line_type', 'pipe_number', 'next_pipe', 'geo_location', 'weld_type', 'weld_crossing', 'weld_number', 'weld_sub_type', 'WPS', 'electrodes', 'root_os', 'root_ts', 'hot_os', 'hot_ts', 'fill_os', 'fill_ts', 'cap_os', 'cap_ts', 'visual_acceptance', 'comment', 'signed_off', 'is_anomally'], 'safe'],
            [['has_been_cut_out'], 'string'],
            [['kp','end_kp','end_weld'], 'number'],
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
        $defaultSort = 'SORT_DESC';
        if(Yii::$app->controller->module->id == "admin" && Yii::$app->controller->id == "anomaly"){
            $query = Welding::find()->anomally();
        }else{
            $query = Welding::find()->active()->orderBy('id DESC');;
        }
        // add conditions that should always apply here
        if(Yii::$app->controller->module->id  == "report"){
            $query = Welding::find()->select('id, date, report_number, geo_location, kp, weld_number, line_type, pipe_number, next_pipe, sequence, visual_acceptance, has_been_cut_out, weld_type, weld_crossing, root_os, root_ts, hot_os, hot_ts, fill_os, fill_ts, cap_os, cap_ts, WPS, weld_sub_type, electrodes, comment, created_by, signed_off, qa_manager, created_at, updated_at')->active()->orderBy('sequence ASC');
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            // 'sort' => [
            //     'defaultOrder' => ['id' => $defaultSort],
            //     // 'attributes' => ['sequence'],
            // ]
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
        $query->andFilterWhere([
            'id' => $this->id,
            'project_id' => $this->project_id,
            'qa_manager' => $this->qa_manager,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'kp' => $this->kp,
            'sequence' => $this->sequence,
            'has_been_cut_out' => $this->has_been_cut_out
        ]);
     
        $query->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['like', 'line_type', $this->line_type])
            ->andFilterWhere(['like', 'weld_number', $this->weld_number])
            ->andFilterWhere(['like', 'pipe_number', $this->pipe_number])
            ->andFilterWhere(['like', 'next_pipe', $this->next_pipe])
            ->andFilterWhere(['like', 'geo_location', $this->geo_location])
            ->andFilterWhere(['like', 'weld_type', $this->weld_type])
            ->andFilterWhere(['like', 'weld_crossing', $this->weld_crossing])
            ->andFilterWhere(['like', 'weld_sub_type', $this->weld_sub_type])
            ->andFilterWhere(['like', 'electrodes', $this->electrodes])
            ->andFilterWhere(['like', 'root_os', $this->root_os])
            ->andFilterWhere(['like', 'root_ts', $this->root_ts])
            ->andFilterWhere(['like', 'hot_os', $this->hot_os])
            ->andFilterWhere(['like', 'hot_ts', $this->hot_ts])
            ->andFilterWhere(['like', 'fill_os', $this->fill_os])
            ->andFilterWhere(['like', 'fill_ts', $this->fill_ts])
            ->andFilterWhere(['like', 'cap_os', $this->cap_os])
            ->andFilterWhere(['like', 'cap_ts', $this->cap_ts])
            ->andFilterWhere(['like', 'visual_acceptance', $this->visual_acceptance])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'signed_off', $this->signed_off]);
        if(!empty($this->date)) {
            list($start_date, $end_date) = explode('/', $this->date);

            $query->andFilterWhere(['between', 'date', $start_date, $end_date]);			
        }
        
        // if(isset($this->kp)) {
        //     $query->andWhere('FLOOR(kp)='.floor($this->kp));	
        // }
        if(!empty($_GET['download']) && !empty($_GET['weldBook'])){
            $dataProvider = new ActiveDataProvider([
                'query' => $query->asArray(),
                'pagination' => false,
            ]);
            Yii::$app->general->downloadCsvforweldbook($dataProvider->query->all());
        } else if(!empty($params['download']) && isset($params['app']) && $params['app']){
            return Yii::$app->general->globalDownload($query, $params['app']);
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
