<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "com_surveying".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property int $kp
 * @property string $geo_location
 * @property int $ir_reading
 * @property int $project_id
 * @property string $comment
 * @property int $qa_manager
 * @property string $signed_off
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $is_deleted
 * @property string $is_anomally
 * @property string $why_anomally
 */
class Surveying extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'com_surveying';
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
            [['date', 'report_number', 'kp', 'ir_reading'], 'required'],
            [['date'], 'safe'],
            [['kp'],'number'],
            [['ir_reading', 'project_id', 'qa_manager', 'created_by', 'updated_by', 'created_at', 'updated_at', 'is_deleted'], 'integer'],
            [['comment', 'signed_off', 'is_anomally'], 'string'],
            [['report_number', 'why_anomally','geo_location'], 'string', 'max' => 255],
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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'geo_location' => Yii::$app->trans->getTrans('Geo Location'),
            'ir_reading' => Yii::$app->trans->getTrans('IR Reading'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
            'is_anomally' => 'Is Anomally',
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
        ];
    }

    public function print_attributes()
    {
        return [
         
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'ir_reading' => Yii::$app->trans->getTrans('IR Reading'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
        ];
    }

    /**
     * @inheritdoc
     * @return SurveyingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SurveyingQuery(get_called_class());
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
