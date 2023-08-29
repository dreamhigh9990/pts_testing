<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CabStringing;

/**
 * CabStringingSearch represents the model behind the search form of `app\models\CabStringing`.
 */
class CabStringingSearch extends CabStringing
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted','qa_manager'], 'integer'],
            [['report_number', 'location', 'drum_number', 'geo_location', 'comment', 'is_anomally', 'why_anomally','date','signed_off'], 'safe'],
            [['from_kp', 'to_kp', 'length'], 'number'],
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
            $query = CabStringing::find()->anomally()->orderBy('id DESC');;
        }else{
            $query = CabStringing::find()->active()->orderBy('id DESC');;
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
             $query->where('0=1');
             return $dataProvider;
        }
        $query->andFilterWhere([
            'from_kp' => $this->from_kp,
            'to_kp' => $this->to_kp,
            'updated_by' => $this->updated_by,
            'qa_manager' => $this->qa_manager,
            'signed_off' => $this->signed_off,
        ]);

        $query->andFilterWhere(['like', 'report_number', $this->report_number])
            ->andFilterWhere(['like', 'drum_number', $this->drum_number])
            ->andFilterWhere(['IN',   'location',$this->location]);

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
