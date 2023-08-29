<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Trenching;

/**
 * TrenchingSearch represents the model behind the search form of `app\models\Trenching`.
 */
class TrenchingSearch extends Trenching
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'from_kp', 'to_kp', 'from_weld', 'to_weld', 'width', 'depth', 'qa_manager', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['date', 'report_number', 'comment', 'signed_off'], 'safe'],
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
            $query = Trenching::find()->anomally()->orderBy('from_kp ASC');;
        }else{
             $query = Trenching::find()->active()->orderBy('from_kp ASC');
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
            'id' => $this->id,
            'from_kp' => $this->from_kp,
            'to_kp' => $this->to_kp,
            'from_weld' => $this->from_weld,
            'to_weld' => $this->to_weld,
            'width' => $this->width,
            'depth' => $this->depth,
            'qa_manager' => $this->qa_manager,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'signed_off', $this->signed_off]);
    
        if(!empty($this->date)) {
            list($start_date, $end_date) = explode('/', $this->date);
            $query->andFilterWhere(['between', 'date', $start_date, $end_date]);			
        }

        $query->andFilterWhere(['AND',['>=', 'from_kp', $this->from_kp],['<=', 'to_kp', $this->to_kp]]);
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
