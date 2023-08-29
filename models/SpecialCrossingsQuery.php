<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[SpecialCrossings]].
 *
 * @see SpecialCrossings
 */
class SpecialCrossingsQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['civil_special_crossings.is_deleted' => 0, 'civil_special_crossings.project_id' => Yii::$app->user->identity->project_id, 'civil_special_crossings.is_active' => 1]);
    }

    public function anomally()
    {
        return $this->andWhere(['civil_special_crossings.is_deleted' => 0, 'civil_special_crossings.project_id' => Yii::$app->user->identity->project_id, 'civil_special_crossings.is_anomally' => "Yes"]);
    }

    /**
     * {@inheritdoc}
     * @return SpecialCrossings[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SpecialCrossings|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
