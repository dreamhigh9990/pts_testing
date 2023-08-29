<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "com_hydrotesting".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property int $from_kp
 * @property int $to_kp
 * @property int $from_weld
 * @property int $to_weld
 * @property string $test_result
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
class Hydrotesting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'com_hydrotesting';
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
            [['date', 'report_number', 'from_kp', 'to_kp', 'from_weld', 'to_weld','test_result'], 'required'],
            [['date'], 'safe'],
            [['from_kp', 'to_kp', 'from_weld', 'to_weld'],'number'],
            [['project_id', 'qa_manager', 'created_by', 'updated_by', 'created_at', 'updated_at', 'is_deleted'], 'integer'],
            [['test_result', 'comment', 'signed_off', 'is_anomally'], 'string'],
            [['report_number', 'why_anomally'], 'string', 'max' => 255],
        ];
    }

    public function print_attributes()
    {
        return [
         
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'from_kp' => Yii::$app->trans->getTrans('From KP'),
            'to_kp' => Yii::$app->trans->getTrans('To KP'),
            'from_weld' => Yii::$app->trans->getTrans('From Weld'),
            'to_weld' => Yii::$app->trans->getTrans('To Weld'),
            'test_result' => Yii::$app->trans->getTrans('Test Result'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'from_kp' => Yii::$app->trans->getTrans('From KP'),
            'to_kp' => Yii::$app->trans->getTrans('To KP'),
            'from_weld' => Yii::$app->trans->getTrans('From Weld'),
            'to_weld' => Yii::$app->trans->getTrans('To Weld'),
            'test_result' => Yii::$app->trans->getTrans('Test Result'),
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

    /**
     * @inheritdoc
     * @return HydrotestingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new HydrotestingQuery(get_called_class());
    }
    public function checkAnomaly(){
        if($this->isNewRecord){
            if($this->from_weld != ""){
                $fromweldlist =  Welding::find()->where(['weld_number'=>$this->from_weld])->active()->one();
                if(empty($fromweldlist)){
                    $this->is_anomally  = "Yes";	
                    $this->why_anomally = "This from weld number was not exist in welding";	
                }
            }
            if($this->to_weld != ""){
                $toweldlist =  Welding::find()->where(['weld_number'=>$this->to_weld])->active()->one();
                if(empty($toweldlist)){
                    $this->is_anomally  = "Yes";	
                    $this->why_anomally = "This to weld number was not exist in welding";	
                }
            }
        }
		return;
	}
    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {
            $this->checkAnomaly();
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
