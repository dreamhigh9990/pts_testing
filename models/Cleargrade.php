<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pip_cleargrade".
 *
 * @property int $id
 * @property string $report_number
 * @property string $location
 * @property int $start_kp
 * @property string $start_geo_location
 * @property int $end_kp
 * @property string $end_geo_location
 * @property string $signed_off
 * @property string $qa_manager
 * @property string $check_points
 * @property string $comment
 * @property string $is_anomally
 * @property string $why_anomally
 * @property string $date
 * @property int $project_id
 * @property int $created_at
 * @property int $updated_at
 */
class Cleargrade extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pipe_cleargrade';
    }


	public function behaviors()
    {
        return [
            // [
            //     'class' => \yii\behaviors\TimestampBehavior::className(),
            //     'attributes' => [
            //         \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
            //         \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
            //     ],
            //     // if you're using datetime instead of UNIX timestamp:
            //     // 'value' => new Expression('NOW()'),
            // ],
            [
                'class'=>\yii\behaviors\BlameableBehavior::className(),
            ]
        ];
    }
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['report_number', 'location', 'start_kp', 'start_geo_location', 'end_kp', 'end_geo_location', 'check_points', 'date'], 'required'],
            [['report_number', 'location', 'start_kp', 'start_geo_location', 'end_kp', 'end_geo_location', 'date'], 'required'],
            [['start_kp', 'end_kp'], 'number'],
            ['start_kp', "checkKpRange"],
            ['end_kp', "checkKpRange"],
            [['signed_off', 'comment', 'is_anomally'], 'string'],
            [['project_id', 'created_at', 'updated_at'], 'integer'],
            [['date'], 'safe'],
            [['report_number', 'location', 'start_geo_location', 'end_geo_location', 'qa_manager', 'why_anomally'], 'string', 'max' => 255],
            // ['check_points', "checkCheckPoints"],
            ['start_geo_location', 'validateCoordinate'],
            ['end_geo_location', 'validateCoordinate']
        ];
    }

    public function validateCoordinate($attribute, $params){
        $latlong = $this->$attribute;
        $result = Yii::$app->general->validGeo($latlong);
        if(!$result){
            $this->addError('Geo Location', 'Please enter a valid Geo Location.');
        }
    }

    public function checkCheckPoints($attribute, $params)
    {
        $listItems = Yii::$app->general->TaxonomyDrop(26);

        $this->check_points = $this->check_points;
        if(!is_array($this->check_points)){
            $this->check_points = json_decode($this->check_points,true);
        }

        if(count($this->check_points) != count($listItems)){
            $this->addError('Check Points', 'Please ensure all checkpoints are completed');
        }
    }

    public function checkKpRange($attribute, $params)
    {
        if( $this->isNewRecord){
            if($this->start_kp >= $this->end_kp){
                $this->addError($attribute, 'start_kp is must be less than of end_kp');
            }

            // $Cleargrade = Cleargrade::find()->where(['AND',['<=', 'start_kp', $this->$attribute],['>', 'end_kp', $this->$attribute]])->active()->one();
            // if(!empty($Cleargrade)){
            //     $this->addError($attribute, 'The KP range entered already exists. Please select a different range to Clear & Grade');
            // }
        }
    }

    public function print_attributes(){
        return [
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'location' => Yii::$app->trans->getTrans('Location'),
            'start_kp' => Yii::$app->trans->getTrans('From Chainage'),            
            'end_kp' => Yii::$app->trans->getTrans('To Chainage'),
            'check_points' => Yii::$app->trans->getTrans('Check Points'),
            'created_by' => Yii::$app->trans->getTrans('User'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'location' => Yii::$app->trans->getTrans('Location'),
            'start_kp' => Yii::$app->trans->getTrans('From Chainage'),
            'start_geo_location' => Yii::$app->trans->getTrans('Start Geo Location'),
            'end_kp' => Yii::$app->trans->getTrans('To Chainage'),
            'end_geo_location' => Yii::$app->trans->getTrans('End Geo Location'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'check_points' => Yii::$app->trans->getTrans('Check Points'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'is_anomally' => 'Is Anomally',
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'date' => Yii::$app->trans->getTrans('Date'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'created_at' => 'Created At',
            'created_by' => Yii::$app->trans->getTrans('User'),
            'updated_at' => 'Updated At',
        ];
    }
    public static function find()
    {
        return new CleargradeQuery(get_called_class());
    }
	public function beforeSave($insert){
		if (parent::beforeSave($insert)) {
            if(Yii::$app->controller->id != "sync"){
                if(is_array($this->check_points)){
                    $this->check_points = json_encode($this->check_points);
                } else {
                    $this->check_points = '';
                }
            }
            $this->project_id = empty($this->project_id) ? Yii::$app->user->identity->project_id : $this->project_id;
            $mo = Yii::$app->general->setTimestamp($this);
            $this->created_at =  $mo->created_at;
            $this->updated_at  = $mo->updated_at; 
        	return true;
		} else {
			return false;
		}
	}
}
