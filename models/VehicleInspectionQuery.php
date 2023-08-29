<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[VehicleInspection]].
 *
 * @see VehicleInspection
 */
class VehicleInspectionQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['vehicle_inspection.is_deleted' => 0, 'vehicle_inspection.project_id' => Yii::$app->user->identity->project_id, 'vehicle_inspection.is_active' => 1]);
    }

    /**
     * @inheritdoc
     * @return VehicleInspection[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return VehicleInspection|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
