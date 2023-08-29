<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vehicle_schedule".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property int $project_id
 * @property string $location
 * @property string $sca_unit_number
 * @property string $vehicle_type
 * @property string $vehicle_number
 * @property string $inspection_frequency
 * @property string $part_list
 * @property string $signed_off
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 * @property int $is_active
 */
class VehicleSchedule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vehicle_schedule';
    }

    public $part_id;
    public $barcode;

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
            [['date', 'report_number', 'project_id', 'location', 'vehicle_type', 'vehicle_number', 'part_list', 'in_use', 'qa_manager', 'signed_off', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'required'],
            [['date'], 'safe'],
            [['project_id', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted', 'is_active'], 'integer'],
            [['part_list', 'signed_off'], 'string'],
            [['report_number', 'location', 'vehicle_type', 'vehicle_number'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function print_attributes(){
        return [
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'location' => Yii::$app->trans->getTrans('Location'),
            'vehicle_type' => Yii::$app->trans->getTrans('Vehicle Type'),
            'vehicle_number' => Yii::$app->trans->getTrans('SCA Unit Number'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->trans->getTrans('ID'),
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'location' => Yii::$app->trans->getTrans('Location'),
            'vehicle_type' => Yii::$app->trans->getTrans('Vehicle Type'),
            'vehicle_number' => Yii::$app->trans->getTrans('SCA Unit Number'),
            'inspection_frequency' => Yii::$app->trans->getTrans('Inspection Frequency'),
            'part_list' => Yii::$app->trans->getTrans('Part List'),
            'in_use' => Yii::$app->trans->getTrans('In Use'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'created_at' => Yii::$app->trans->getTrans('Created At'),
            'updated_at' => Yii::$app->trans->getTrans('Updated At'),
            'created_by' => Yii::$app->trans->getTrans('Created By'),
            'updated_by' => Yii::$app->trans->getTrans('Updated By'),
            'is_deleted' => Yii::$app->trans->getTrans('Is Deleted'),
            'is_active' => Yii::$app->trans->getTrans('Is Active'),
        ];
    }

    /**
     * @inheritdoc
     * @return VehicleScheduleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VehicleScheduleQuery(get_called_class());
    }

    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {			
            $this->project_id = empty($this->project_id) ? Yii::$app->user->identity->project_id : $this->project_id;
            $mo = Yii::$app->general->setTimestamp($this);
            $this->created_at = ($mo->created_at)-1;
            $this->updated_at = ($mo->updated_at)-1;
            return true;
		} else {
			return false;
		}
	}
}