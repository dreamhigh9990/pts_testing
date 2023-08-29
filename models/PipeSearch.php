<?php

namespace app\models;

use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pipe;

/**
 * PipeSearch represents the model behind the search form of `app\models\Pipe`.
 */
class PipeSearch extends Pipe
{
    public $download;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at'], 'integer'],
            [['pipe_id','pups','pipe_number', 'coating_type', 'vessel', 'hfb', 'mto_number', 'mto_certificate', 'mill', 'comments', 'project_id', 'created_by', 'updated_by'], 'safe'],
            [['wall_thikness', 'weight', 'heat_number', 'yeild_strength', 'length', 'od', 'plate_number', 'ship_out_number'], 'number'],
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
             $query = Pipe::find()->anomally()->orderBy('id DESC');;
        }else{
             $query = Pipe::find()->active();
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => ['pipe_number'],
                'defaultOrder' => ['pipe_number' => SORT_ASC]
            ]
        ]);
        $dataProvider->pagination->pageSize=!empty($_GET['per-page'])?$_GET['per-page']:10;
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'wall_thikness' => $this->wall_thikness,
            'weight' => $this->weight,
            'heat_number' => $this->heat_number,
            'yeild_strength' => $this->yeild_strength,
            'length' => $this->length,
            'od' => $this->od,
            'plate_number' => $this->plate_number,
            'ship_out_number' => $this->ship_out_number,
            'pups' => $this->pups,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'coating_type', $this->coating_type])
            ->andFilterWhere(['like', 'vessel', $this->vessel])
            ->andFilterWhere(['like', 'pipe_number', $this->pipe_number])
            ->andFilterWhere(['like', 'hfb', $this->hfb])
            ->andFilterWhere(['like', 'mto_number', $this->mto_number])
            ->andFilterWhere(['like', 'mto_certificate', $this->mto_certificate])
            ->andFilterWhere(['like', 'mill', $this->mill])
            ->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'project_id', $this->project_id])
            ->andFilterWhere(['like', 'created_by', $this->created_by])
            ->andFilterWhere(['like', 'updated_by', $this->updated_by]);


        if($this->download){
            $dataProvider = new ActiveDataProvider([
                'query' => $query->asArray(),
                'pagination' => false,
            ]);
            Yii::$app->export->excelExport('pipe', $dataProvider->query->all());
        }

        if(!empty($_GET['download'])){
           Yii::$app->general->globalDownload($query);
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
