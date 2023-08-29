<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "welding_coating_repair".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property int $project_id
 * @property double $ambient_temperature
 * @property double $substrate_temprature
 * @property double $humidity
 * @property double $dew_point
 * @property double $kp
 * @property string $weld_number
 * @property double $coating_product
 * @property string $type_repair
 * @property string $checkpoint
 * @property double $temperature
 * @property double $time
 * @property string $comment
 * @property string $signed_off
 * @property int $qa_manager
 * @property string $is_anomally
 * @property string $why_anomally
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 * @property int $is_active
 */
class Coatingrepair extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'welding_coating_repair';
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
            // [['date', 'report_number', 'ambient_temperature', 'substrate_temprature', 'humidity', 'dew_point', 'kp', 'weld_number', 'coating_product', 'type_repair', 'checkpoint'], 'required'],
            [['date', 'report_number', 'ambient_temperature', 'substrate_temprature', 'humidity', 'dew_point', 'kp', 'type_repair', 'main_weld_id'], 'required'],
            [['date'], 'safe'],
            [['project_id', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted', 'is_active'], 'integer'],
            [['ambient_temperature', 'substrate_temprature', 'humidity', 'dew_point', 'kp', 'coating_product', 'temperature', 'time'], 'number'],
            [['type_repair', 'comment', 'signed_off', 'is_anomally', 'batch_number_a', 'batch_number_b'], 'string'],
            [['report_number', 'weld_number', 'why_anomally','pipe_number'], 'string', 'max' => 255],
            // ['checkpoint', "checkCheckPoints"],
        ];
    }

    public function checkCheckPoints($attribute, $params) {
        if(Yii::$app->controller->id !="sync"){
            $listItems = Yii::$app->general->TaxonomyDrop(22);

            if(!is_array($this->checkpoint)){
                $this->checkpoint = json_decode($this->checkpoint,true);
            }

            if(count($this->checkpoint) != count($listItems)){
                $this->addError('Check Points', 'Please ensure all checkpoints are completed');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function print_attributes(){
        return [
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'weld_number' => Yii::$app->trans->getTrans('Weld Number'),
            'weld_type' => Yii::$app->trans->getTrans('Weld Type'),
            'weld_sub_type' => Yii::$app->trans->getTrans('Weld Sub Type'),
            'ambient_temperature' => Yii::$app->trans->getTrans('Ambient Temperature'),
            'substrate_temprature' => Yii::$app->trans->getTrans('Substrate Temprature'),
            'humidity' => Yii::$app->trans->getTrans('Humidity'),
            'dew_point' => Yii::$app->trans->getTrans('Dew Point'),
            // 'coating_product' => Yii::$app->trans->getTrans('Coating Product'),
            'batch_number_a' => Yii::$app->trans->getTrans('Batch Number').' A',
            'batch_number_b' => Yii::$app->trans->getTrans('Batch Number').' B',
            'type_repair' => Yii::$app->trans->getTrans('Type of Repair'),
            'temperature' => Yii::$app->trans->getTrans('Temperature'),
            'time' => Yii::$app->trans->getTrans('Time'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'pipe_number' => Yii::$app->trans->getTrans('Pipe Number'),
        ];
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
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'ambient_temperature' => Yii::$app->trans->getTrans('Ambient Temperature'),
            'substrate_temprature' => Yii::$app->trans->getTrans('Substrate Temprature'),
            'humidity' => Yii::$app->trans->getTrans('Humidity'),
            'dew_point' => Yii::$app->trans->getTrans('Dew Point'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'weld_number' => Yii::$app->trans->getTrans('Weld Number'),
            // 'coating_product' => Yii::$app->trans->getTrans('Coating Product'),
            'batch_number_a' => Yii::$app->trans->getTrans('Batch Number').' A',
            'batch_number_b' => Yii::$app->trans->getTrans('Batch Number').' B',
            'type_repair' => Yii::$app->trans->getTrans('Type of Repair'),
            'checkpoint' => Yii::$app->trans->getTrans('Check Points'),
            'temperature' => Yii::$app->trans->getTrans('Temperature'),
            'time' => Yii::$app->trans->getTrans('Time'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'is_anomally' => 'Is Anomally',
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'pipe_number' => Yii::$app->trans->getTrans('Pipe Number'),
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
        ];
    }

    /**
     * @inheritdoc
     * @return CoatingrepairQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CoatingrepairQuery(get_called_class());
    }
    
    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {
            if(Yii::$app->controller->id !="sync"){
                if(!empty($this->checkpoint)){
                    $this->checkpoint = json_encode($this->checkpoint);
                } else {
                    $this->checkpoint = '';
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
