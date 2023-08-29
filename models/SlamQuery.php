<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[SafetySlam]].
 *
 * @see SafetySlam
 */
class SlamQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['safety_slam.is_deleted'=>0,'safety_slam.project_id'=>Yii::$app->user->identity->project_id,'safety_slam.is_active'=>1]);
    }

    /**
     * @inheritdoc
     * @return SafetySlam[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SafetySlam|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
