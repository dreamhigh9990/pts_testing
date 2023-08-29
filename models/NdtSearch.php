<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Ndt;

/**
 * NdtSearch represents the model behind the search form of `app\models\Ndt`.
 */
class NdtSearch extends Ndt
{
    public $weldType;
    public $weldSubType;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['date', 'report_number', 'weld_number', 'outcome', 'comment', 'is_anomally', 'why_anomally', 'signed_off', 'weldType', 'weldSubType'], 'safe'],
            [['kp'], 'number'],
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
            $query = Ndt::find()->anomally()->orderBy('id DESC');;
        }else{
            $query = Ndt::find()->active()->orderBy('id DESC');;
        }
        // add conditions that should always apply here

        if(!empty($params['NdtSearch']) && (!empty($params['NdtSearch']['weldSubType']) || !empty($params['NdtSearch']['weldType']))){
            $query = Ndt::find()->select(['welding_ndt.*', 'welding.weld_sub_type', 'welding.is_active', 'welding.is_deleted'])->leftJoin('welding', 'welding_ndt.weld_number = welding.weld_number AND welding_ndt.project_id = welding.project_id AND welding.is_active = 1 AND welding.is_deleted = 0 AND welding_ndt.kp = welding.kp AND welding.project_id = '.Yii::$app->user->identity->project_id)->active()->orderBy('id DESC');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if(Yii::$app->controller->id!="default"){
            $dataProvider->pagination->pageSize=!empty($_GET['per-page'])?$_GET['per-page']:10;
        }     
        $this->load($params);

        if($this->weldType){
            $query->andFilterWhere(['=', 'welding.weld_type', $this->weldType]);
        }

        if($this->weldSubType){
            $query->andFilterWhere(['=', 'welding.weld_sub_type', $this->weldSubType]);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'welding_ndt.id' => $this->id,
            'welding_ndt.project_id' => $this->project_id,           
            'welding_ndt.qa_manager' => $this->qa_manager,
            'welding_ndt.created_by' => $this->created_by,
            'welding_ndt.kp' => $this->kp,
        ]);

        $query->andFilterWhere(['like', 'welding_ndt.report_number', $this->report_number])
            ->andFilterWhere(['like', 'welding_ndt.weld_number', $this->weld_number])
            ->andFilterWhere(['like', 'welding_ndt.outcome', $this->outcome])
            ->andFilterWhere(['like', 'welding_ndt.comment', $this->comment])
            ->andFilterWhere(['like', 'welding_ndt.is_anomally', $this->is_anomally])
            ->andFilterWhere(['like', 'welding_ndt.why_anomally', $this->why_anomally])
            ->andFilterWhere(['like', 'welding_ndt.signed_off', $this->signed_off]);

        
        if(!empty($this->date)) {
            list($start_date, $end_date) = explode('/', $this->date);

            $query->andFilterWhere(['between', 'welding_ndt.date', $start_date, $end_date]);			
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
