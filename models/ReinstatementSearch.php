<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Reinstatement;

/**
 * ReinstatementSearch represents the model behind the search form of `app\models\Reinstatement`.
 */
class ReinstatementSearch extends Reinstatement
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'from_kp', 'to_kp', 'from_weld', 'to_weld', 'length', 'project_id', 'qa_manager', 'created_by', 'updated_by', 'created_at', 'updated_at', 'is_deleted'], 'integer'],
            [['date', 'report_number', 'check_points', 'comment', 'signed_off', 'is_anomally', 'why_anomally'], 'safe'],
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
            $query = Reinstatement::find()->anomally()->orderBy([
                'from_kp' => SORT_ASC,
                'from_weld' => SORT_ASC
            ]);
        }else{
            $query = Reinstatement::find()->active()->orderBy([
                'from_kp' => SORT_ASC,
                'from_weld' => SORT_ASC
            ]);
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
            'id' => $this->id,
            'from_kp' => $this->from_kp,
            'to_kp' => $this->to_kp,
            'from_weld' => $this->from_weld,
            'to_weld' => $this->to_weld,
            'length' => $this->length,
            'project_id' => $this->project_id,
            'qa_manager' => $this->qa_manager,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_deleted' => $this->is_deleted,
        ]);

        $query->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['like', 'check_points', $this->check_points])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'signed_off', $this->signed_off])
            ->andFilterWhere(['like', 'is_anomally', $this->is_anomally])
            ->andFilterWhere(['like', 'why_anomally', $this->why_anomally]);
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
