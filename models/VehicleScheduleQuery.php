<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[VehicleSchedule]].
 *
 * @see VehicleSchedule
 */
class VehicleScheduleQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['vehicle_schedule.is_deleted' => 0, 'vehicle_schedule.project_id' => Yii::$app->user->identity->project_id, 'vehicle_schedule.is_active' => 1]);
    }
    

    /**
     * @inheritdoc
     * @return VehicleSchedule[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return VehicleSchedule|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
