<?php

namespace app\models;

use Yii;
/**
 * This is the ActiveQuery class for [[HazardReport]].
 *
 * @see HazardReport
 */
class HazardReportQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['hazard_report.is_deleted'=>0,'hazard_report.project_id'=>Yii::$app->user->identity->project_id,'hazard_report.is_active'=>1]);
    }
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return HazardReport|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
