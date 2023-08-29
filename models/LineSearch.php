<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Line;

/**
 * LineSearch represents the model behind the search form of `app\models\Line`.
 */
class LineSearch extends Line
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at'], 'integer'],
            [['pipe_diameter', 'project_id','wall_thickness', 'depth_of_cover', 'coating_type', 'bend_location', 'road_crossing', 'river_crossing', 'foreign_service_crossing', 'fence_crossing', 'hdd_locations', 'backfill_material', 'marker_tape_location', 'comment', 'updated_by','from_kp','to_kp'], 'safe'],
            [['from_kp', 'to_kp',], 'number'],
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
        $query = Line::find()->active()->orderBy('from_kp DESC');;
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
            'created_by' => $this->created_by,
        ]);
        $query->andFilterWhere(['like', 'pipe_diameter', $this->pipe_diameter]);
        $query->andFilterWhere(['like', 'wall_thickness', $this->wall_thickness]);
        $query->andFilterWhere(['like', 'depth_of_cover', $this->depth_of_cover]);
        $query->andFilterWhere(['like', 'coating_type', $this->coating_type]);

        $query->andFilterWhere(['AND',['>=', 'from_kp', $this->from_kp],['<=', 'to_kp', $this->to_kp]]);
        if(!empty($_GET['download'])){
            Yii::$app->general->globalDownload($query);die;
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
