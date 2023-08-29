<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Bending;

/**
 * BendingSearch represents the model behind the search form of `app\models\Bending`.
 */
class BendingSearch extends Bending
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pipe_id', 'comment', 'project_id', 'is_deleted', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['report_number', 'pipe_number', 'designation', 'position', 'qa_manager', 'signed_off', 'date', 'is_anomally', 'why_anomally'], 'safe'],
            [['angle', 'kp'], 'number'],
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
            $query = Bending::find()->anomally()->orderBy('id DESC');;
        }else{
             $query = Bending::find()->active()->orderBy('id DESC');
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
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'angle' => $this->angle,
            'kp' => $this->kp,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['like', 'pipe_number', $this->pipe_number])
            ->andFilterWhere(['like', 'designation', $this->designation])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'qa_manager', $this->qa_manager])
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