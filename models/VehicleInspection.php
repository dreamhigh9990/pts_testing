<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vehicle_inspection".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property int $project_id
 * @property string $location
 * @property int $vehicle_id
 * @property string $service_due
 * @property string $geolocation
 * @property int $odometer_reading
 * @property int $qa_manager
 * @property string $signed_off
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 * @property int $is_active
 */
class VehicleInspection extends \yii\db\ActiveRecord
{
    public $vehicle_filter;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vehicle_inspection';
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
            [['date', 'report_number', 'project_id', 'location', 'vehicle_id', 'service_due', 'geolocation', 'odometer_reading', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'required'],
            [['date'], 'safe'],
            [['project_id', 'odometer_reading', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted', 'is_active'], 'integer'],
            [['service_due', 'signed_off'], 'string'],
            [['report_number', 'location', 'geolocation'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function print_attributes(){
        return [
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'location' => Yii::$app->trans->getTrans('app', 'Location'),
            'vehicle_id' => Yii::$app->trans->getTrans('app', 'SCA Unit Number'),
            'service_due' => Yii::$app->trans->getTrans('app', 'Service Due'),
            'signed_off' => Yii::$app->trans->getTrans('app', 'Signed Off'),
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
            'vehicle_id' => Yii::$app->trans->getTrans('SCA Unit Number'),
            'service_due' => Yii::$app->trans->getTrans('Service Due'),
            'geolocation' => Yii::$app->trans->getTrans('Geolocation'),
            'odometer_reading' => Yii::$app->trans->getTrans('Odometer Reading'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'created_at' => Yii::$app->trans->getTrans('Created At'),
            'updated_at' => Yii::$app->trans->getTrans('Updated At'),
            'created_by' => Yii::$app->trans->getTrans('Created By'),
            'updated_by' => Yii::$app->trans->getTrans('Updated By'),
            'is_deleted' => Yii::$app->trans->getTrans('Is Deleted'),
            'is_active' => Yii::$app->trans->getTrans('Is Active'),
            'vehicle_filter' => Yii::$app->trans->getTrans('Vehicle Filter'),
        ];
    }

    /**
     * @inheritdoc
     * @return VehicleInspectionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VehicleInspectionQuery(get_called_class());
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
