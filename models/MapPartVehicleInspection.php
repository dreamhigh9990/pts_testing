<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "map_part_vehicle_inspection".
 *
 * @property int $id
 * @property string $date
 * @property int $inspection_id
 * @property int $part_id
 * @property int $que_id
 * @property string $status
 * @property string $defect_comments
 *
 * @property VehicleInspection $inspection
 */
class MapPartVehicleInspection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map_part_vehicle_inspection';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'inspection_id', 'part_id', 'que_id', 'status'], 'required'],
            [['date'], 'safe'],
            ['defect_comments', 'required', 'when' => function($model) {
                return $model->status == 'Needs Attention';
            }],
            [['inspection_id', 'part_id', 'que_id'], 'integer'],
            [['status'], 'string'],
            [['defect_comments'], 'string', 'max' => 255],
            [['inspection_id'], 'exist', 'skipOnError' => true, 'targetClass' => VehicleInspection::className(), 'targetAttribute' => ['inspection_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'inspection_id' => 'Inspection ID',
            'part_id' => 'Part ID',
            'que_id' => 'Que ID',
            'status' => 'Status',
            'defect_comments' => 'Defect Comments',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInspection()
    {
        return $this->hasOne(VehicleInspection::className(), ['id' => 'inspection_id']);
    }

    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {
            $mo = Yii::$app->general->setTimestamp($this);
            $this->created_at =  ($mo->created_at)-1;
            $this->updated_at  = ($mo->updated_at)-1;
            return true;
		} else {
			return false;
		}
	}
}
