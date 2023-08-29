<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pipe_stringing".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property string $location
 * @property int $kp
 * @property string $pipe_number
 * @property int $pipe_id
 * @property string $geo_location
 * @property string $defacts
 * @property string $comment
 * @property string $relocated
 * @property string $transfer_report
 * @property string $signed_off
 * @property string $qa_manager
 * @property string $is_anomally
 * @property string $why_anomally
 * @property int $project_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class Stringing extends \yii\db\ActiveRecord
{
  
    public static function tableName()
    {
        return 'pipe_stringing';
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
            [['date', 'location', 'kp', 'pipe_number','geo_location'], 'required'],
            ['kp','number'],
            [['date'], 'safe'],
            [['project_id', 'created_at', 'updated_at', 'created_by', 'updated_by','qa_manager'], 'integer'],
            [['comment', 'relocated', 'transfer_report', 'signed_off', 'is_anomally'], 'string'],
            [['report_number', 'location', 'pipe_number', 'geo_location','why_anomally'], 'string', 'max' => 255],
            ['geo_location', 'validateCoordinate'],
        ];
    }

    public function validateCoordinate($attribute, $params){
        $latlong = $this->$attribute;
        $result = Yii::$app->general->validGeo($latlong);
        if(!$result){
            $this->addError('Geo Location', 'Please enter a valid Geo Location.');
        }
    }

    /**
     * @inheritdoc
     */
    public function print_attributes(){
        return [
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'location' => Yii::$app->trans->getTrans('Location'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'pipe_number' => Yii::$app->trans->getTrans('Pipe Number'),
            'pipe_length' => Yii::$app->trans->getTrans('Pipe Length'),
            'geo_location' => Yii::$app->trans->getTrans('Geo Location'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'relocated' => Yii::$app->trans->getTrans('Relocated'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'location' => Yii::$app->trans->getTrans('Location'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'pipe_number' => Yii::$app->trans->getTrans('Pipe Number'),
            'geo_location' => Yii::$app->trans->getTrans('Geo Location'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'relocated' => Yii::$app->trans->getTrans('Relocated'),
            'transfer_report' => Yii::$app->trans->getTrans('Transfer Report'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'is_anomally' => 'Is Anomally',
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @inheritdoc
     * @return StringingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StringingQuery(get_called_class());
    }   
    public function beforeSave($insert){
      
		if (parent::beforeSave($insert)) {
					
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
