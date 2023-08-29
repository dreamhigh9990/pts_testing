<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "welding_parameter_check".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property int $project_id
 * @property double $kp
 * @property string $weld_number
 * @property string $welder
 * @property string $preheat
 * @property string $gas_flow
 * @property string $pass_number
 * @property string $amps
 * @property string $volt
 * @property string $rol
 * @property string $travel
 * @property string $hit * 
 * @property double $rot
 * @property double $k_factor
 * @property double $wire_speed
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
 */
class Parameter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'welding_parameter_check';
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
            [['date', 'report_number', 'kp', 'weld_number', 'welder'], 'required'],
            [['date'], 'safe'],
            [['project_id', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['kp','rot', 'k_factor', 'wire_speed', 'interpass_temperature', 'heat_input'], 'number'],
            [['comment', 'signed_off', 'is_anomally'], 'string'],
            [['report_number', 'weld_number', 'welder', 'preheat', 'gas_flow', 'pass_number', 'amps', 'volt', 'rol', 'travel', 'hit', 'why_anomally'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function print_attributes()
    {
        return [        
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'weld_number' => Yii::$app->trans->getTrans('Weld Number'),
            'welder' => Yii::$app->trans->getTrans('Welder'),
            'pass_number' => Yii::$app->trans->getTrans('Pass Number'),
            'preheat' => Yii::$app->trans->getTrans('Preheat').'(Deg C)',
            'amps' => Yii::$app->trans->getTrans('Amps'),
            'volt' => Yii::$app->trans->getTrans('Volt'),
            'rot' => Yii::$app->trans->getTrans('Rot').'(sec)',
            'rol' => Yii::$app->trans->getTrans('Rol').'(mm)',
            'travel' => Yii::$app->trans->getTrans('Travel Speed').'(mm/min)',
            'heat_input' => Yii::$app->trans->getTrans('Heat Input').'(KJ/mm)',
            'interpass_temperature' => Yii::$app->trans->getTrans('Interpass Temp.'),
            'gas_flow' => Yii::$app->trans->getTrans('Gas Flow'),
            // 'hit' => Yii::$app->trans->getTrans('Hit'),
            'wire_speed' => Yii::$app->trans->getTrans('Wire Speed'),
            'k_factor' => Yii::$app->trans->getTrans('K Factor'),
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
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'weld_number' => Yii::$app->trans->getTrans('Weld Number'),
            'welder' => Yii::$app->trans->getTrans('Welder'),
            'preheat' => Yii::$app->trans->getTrans('Preheat').'(Deg C)',
            'gas_flow' => Yii::$app->trans->getTrans('Gas Flow'),
            'rot' => Yii::$app->trans->getTrans('Rot').'(sec)',
            'k_factor' => Yii::$app->trans->getTrans('K Factor'),
            'wire_speed' => Yii::$app->trans->getTrans('Wire Speed'),
            'interpass_temperature' => Yii::$app->trans->getTrans('Interpass Temp.'),
            'heat_input' => Yii::$app->trans->getTrans('Heat Input').'(KJ/mm)',
            'pass_number' => Yii::$app->trans->getTrans('Pass Number'),
            'amps' => Yii::$app->trans->getTrans('Amps'),
            'volt' => Yii::$app->trans->getTrans('Volt'),
            'rol' => Yii::$app->trans->getTrans('ROL').'(mm)',
            'travel' => Yii::$app->trans->getTrans('Travel Speed').'(mm/min)',
            'hit' => Yii::$app->trans->getTrans('Hit'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'is_anomally' => 'Is Anomally',
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'created_at' => 'Created At',
            'updated_at' => 'Update At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * @inheritdoc
     * @return ParameterQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ParameterQuery(get_called_class());
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
