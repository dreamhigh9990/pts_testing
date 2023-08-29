<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cabling_stringing".
 *
 * @property int $id
 * @property string $report_number
 * @property string $location
 * @property double $from_kp
 * @property double $to_kp
 * @property string $drum_number
 * @property double $length
 * @property string $geo_location
 * @property string $comment
 * @property string $colour 
 * @property string $date
 * @property int $project_id
 * @property string $is_anomally
 * @property string $why_anomally
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 * 
 */
class CabStringing extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cabling_stringing';
    }

    /**
     * @inheritdoc
     */
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
            [['location', 'from_kp', 'to_kp', 'drum_number'], 'required'],
            [['from_kp', 'to_kp', 'length'], 'number'],
            [['colour','date'],'safe'],
            [['comment', 'is_anomally','date','signed_off'], 'string'],
            [['project_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted','qa_manager'], 'integer'],
            [['report_number', 'location', 'drum_number', 'geo_location', 'why_anomally'], 'string', 'max' => 255],
        ];
    }
    public function print_attributes()
    {
        return [ 
             'date' => Yii::$app->trans->getTrans('Date'),     
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'location' => Yii::$app->trans->getTrans('Location'),
            'from_kp' => Yii::$app->trans->getTrans('From KP'),
            'to_kp' => Yii::$app->trans->getTrans('To KP'),
            'drum_number' => Yii::$app->trans->getTrans('Drum Number'),
            'length' => Yii::$app->trans->getTrans('Length'),
            'geo_location' => Yii::$app->trans->getTrans('Geo Location'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->trans->getTrans('ID'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'location' => Yii::$app->trans->getTrans('Location'),
            'from_kp' => Yii::$app->trans->getTrans('From KP'),
            'to_kp' => Yii::$app->trans->getTrans('To KP'),
            'drum_number' => Yii::$app->trans->getTrans('Drum Number'),
            'length' => Yii::$app->trans->getTrans('Length'),
            'geo_location' => Yii::$app->trans->getTrans('Geo Location'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'is_anomally' => Yii::$app->trans->getTrans('Is Anomally'),
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'created_at' => Yii::$app->trans->getTrans('Created At'),
            'updated_at' => Yii::$app->trans->getTrans('Updated At'),
            'created_by' => Yii::$app->trans->getTrans('Created By'),
            'updated_by' => Yii::$app->trans->getTrans('User'),
            'is_deleted' => Yii::$app->trans->getTrans('Is Deleted'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'date' => Yii::$app->trans->getTrans('Date'),
        ];
    }

    /**
     * @inheritdoc
     * @return CabStringingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CabStringingQuery(get_called_class());
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
