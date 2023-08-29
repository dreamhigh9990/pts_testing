<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "civil_lowering".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property int $from_kp
 * @property int $to_kp
 * @property int $from_weld
 * @property int $to_weld
 * @property int $length
 * @property string $holiday_detection
 * @property string $holiday_serial_number
 * @property string $check_points
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
class Lowering extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'civil_lowering';
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
            // [['date', 'report_number', 'from_kp', 'to_kp', 'from_weld', 'to_weld', 'length', 'holiday_detection', 'check_points'], 'required'],
            [['date', 'report_number', 'from_kp', 'to_kp', 'length', 'holiday_detection'], 'required'], //remove required validation from from_weld and to_weld as per client says
            [['date'], 'safe'],
            [['from_kp', 'to_kp', 'from_weld', 'to_weld', 'length'],'number'],
            [['project_id', 'qa_manager', 'created_by', 'updated_by', 'created_at', 'updated_at', 'is_deleted'], 'integer'],
            [['holiday_detection', 'comment', 'signed_off', 'is_anomally'], 'string'],
            [['report_number', 'holiday_serial_number', 'why_anomally'], 'string', 'max' => 255],
            // ['check_points', "checkCheckPoints"],
        ];
    }

    public function checkCheckPoints($attribute, $params) {
        $listItems = Yii::$app->general->TaxonomyDrop(21);

        if(!is_array($this->check_points)){
            $this->check_points = json_decode($this->check_points,true);
        }

        if(count($this->check_points) != count($listItems)){
            $this->addError('Check Points', 'Please ensure all checkpoints are completed');
        }
    }

    /**
     * @inheritdoc
     */
    public function print_attributes(){
        return [
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'from_kp' => Yii::$app->trans->getTrans('From Chainage'),
            'to_kp' => Yii::$app->trans->getTrans('To Chainage'),
            // 'from_weld' => 'From Weld',
            // 'to_weld' => 'To Weld',
            'length' => Yii::$app->trans->getTrans('Length'),
            'holiday_detection' => Yii::$app->trans->getTrans('Holiday Detection'),
            'holiday_serial_number' => Yii::$app->trans->getTrans('Holiday Serial Number'),
            'check_points' => Yii::$app->trans->getTrans('Check Points'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'created_by' => Yii::$app->trans->getTrans('User'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->trans->getTrans('ID'),
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'from_kp' => Yii::$app->trans->getTrans('From Chainage'),
            'to_kp' => Yii::$app->trans->getTrans('To Chainage'),
            // 'from_weld' => 'From Weld',
            // 'to_weld' => 'To Weld',
            'length' => Yii::$app->trans->getTrans('Length'),
            'holiday_detection' => Yii::$app->trans->getTrans('Holiday Detection'),
            'holiday_serial_number' => Yii::$app->trans->getTrans('Holiday Serial Number'),
            'check_points' => Yii::$app->trans->getTrans('Check Points'),
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
     * @return LoweringQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LoweringQuery(get_called_class());
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
