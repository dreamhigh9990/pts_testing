<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cabling_splicing".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property double $splice_number
 * @property double $kp
 * @property string $next_drum
*   @property string $colour
 * @property string $drum_number
 * @property string $power_meter_1
 * @property string $power_meter_2
 * @property string $light_source
 * @property string $geo_location
 * @property string $comment
 * @property int $project_id
 * @property int $qa_manager
 * @property string $signed_off
 * @property string $is_anomally
 * @property string $why_anomally
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 */
class CabSplicing extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cabling_splicing';
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
    public function rules()
    {
        return [
            [['splice_number', 'kp', 'next_drum', 'drum_number', 'geo_location'], 'required'],
            [['date','colour'], 'safe'],
            [['splice_number', 'kp'], 'number'],
            [['comment', 'signed_off', 'is_anomally'], 'string'],
            [['project_id', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['report_number', 'next_drum', 'drum_number', 'power_meter_1', 'power_meter_2', 'light_source', 'geo_location', 'why_anomally'], 'string', 'max' => 255],
        ];
    }
    public function print_attributes()
    {
        return [ 
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'splice_number' => Yii::$app->trans->getTrans('Splice No.'),
            'kp' => Yii::$app->trans->getTrans('Kp'),
            'next_drum' => Yii::$app->trans->getTrans('Next Drum'),
            'drum_number' => Yii::$app->trans->getTrans('Drum Number'),
            'power_meter_1' => Yii::$app->trans->getTrans('Power Meter 1'),
            'power_meter_2' => Yii::$app->trans->getTrans('Power Meter 2'),
            'light_source' => Yii::$app->trans->getTrans('Light Source'),
            'geo_location' => Yii::$app->trans->getTrans('Geo Location'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->trans->getTrans('ID'),
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'splice_number' => Yii::$app->trans->getTrans('Splice No.'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'next_drum' => Yii::$app->trans->getTrans('Next Drum'),
            'drum_number' => Yii::$app->trans->getTrans('Drum Number'),
            'power_meter_1' => Yii::$app->trans->getTrans('Power Meter 1'),
            'power_meter_2' => Yii::$app->trans->getTrans('Power Meter 2'),
            'light_source' => Yii::$app->trans->getTrans('Light Source'),
            'geo_location' => Yii::$app->trans->getTrans('Geo Location'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'qa_manager' => Yii::$app->trans->getTrans('Qa Manager'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'is_anomally' => Yii::$app->trans->getTrans('Is Anomally'),
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'created_at' => Yii::$app->trans->getTrans('Created At'),
            'updated_at' => Yii::$app->trans->getTrans('Updated At'),
            'created_by' => Yii::$app->trans->getTrans('Created By'),
            'updated_by' => Yii::$app->trans->getTrans('Updated By'),
            'is_deleted' => Yii::$app->trans->getTrans('Is Deleted'),
        ];
    }

    /**
     * @inheritdoc
     * @return CabSplicingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CabSplicingQuery(get_called_class());
    }
    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {	
            if(is_array($this->colour)){
				$this->colour =  json_encode($this->colour);
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
