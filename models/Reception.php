<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pipe_reception".
 *
 * @property int $id
 * @property string $date
 * @property int $project_id
 * @property string $report_number
 * @property string $pipe_number
 * @property string $defacts
 * @property string $truck
 * @property string $location
 * @property string $transferred
 * @property string $transfer_report
 * @property string $comment
 * @property int $qa_manager
 * @property string $signed_off
 * @property string $is_anomally
 * @property string $why_anomally
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 */
class Reception extends \yii\db\ActiveRecord
{
	
    public static function tableName()
    {
        return 'pipe_reception';
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
			[['date','report_number', 'pipe_number','location'], 'required'],
            [['date'], 'safe'],
            [['project_id', 'qa_manager', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],  
			[['transferred', 'transfer_report', 'comment', 'signed_off', 'is_anomally'], 'string'],         
            [['report_number', 'pipe_number', 'truck', 'location', 'why_anomally'], 'string', 'max' => 255],
        ];
    }

	
    /**
     * @inheritdoc
     */
    public function print_attributes(){
        return [
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'pipe_number' => Yii::$app->trans->getTrans('Pipe Number'),
            'pipe_weight' => Yii::$app->trans->getTrans('Pipe weight'),
            'pipe_length' => Yii::$app->trans->getTrans('Pipe Length'),
            'defects' => Yii::$app->trans->getTrans('Defects'),
            'truck' => Yii::$app->trans->getTrans('Truck'),
            'location' => Yii::$app->trans->getTrans('Location'),
            'transferred' => Yii::$app->trans->getTrans('Transferred'),
            'transfer_report' => Yii::$app->trans->getTrans('Transfer Report'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date' => Yii::$app->trans->getTrans('Date'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'pipe_number' => Yii::$app->trans->getTrans('Pipe Number'),
            'truck' => Yii::$app->trans->getTrans('Truck'),
            'location' => Yii::$app->trans->getTrans('Location'),
            'transferred' => Yii::$app->trans->getTrans('Transferred'),
            'transfer_report' => Yii::$app->trans->getTrans('Transfer Report'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'is_anomally' => Yii::t('app', 'Is Anomally'),
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
    public static function find()
    {
        return new ReceptionQuery(get_called_class());
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
