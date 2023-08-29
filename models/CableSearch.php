<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Cable;

/**
 * CableSearch represents the model behind the search form of `app\models\Cable`.
 */
class CableSearch extends Cable
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'created_by', 'updated_by', 'created_at', 'updated_at', 'is_deleted'], 'integer'],
            [['drum_number', 'drum_cable', 'brand', 'standard', 'colour', 'comment', 'is_anomally', 'why_anomally'], 'safe'],
            [['length', 'cores'], 'number'],
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
            $query = Cable::find()->anomally()->orderBy('id DESC');;;
       }else{
           $query = Cable::find()->active()->orderBy('id DESC');;;
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
            'length' => $this->length,
            'cores' => $this->cores,
            'project_id' => $this->project_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_deleted' => $this->is_deleted,
        ]);

        $query->andFilterWhere(['like', 'drum_number', $this->drum_number])
            ->andFilterWhere(['like', 'drum_cable', $this->drum_cable])
            ->andFilterWhere(['like', 'brand', $this->brand])
            ->andFilterWhere(['like', 'standard', $this->standard])
            ->andFilterWhere(['like', 'colour', $this->colour]);
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
