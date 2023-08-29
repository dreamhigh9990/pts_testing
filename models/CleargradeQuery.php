<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Bending]].
 *
 * @see Bending
 */
class CleargradeQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['pipe_cleargrade.is_deleted'=>0,'pipe_cleargrade.project_id'=>Yii::$app->user->identity->project_id,'pipe_cleargrade.is_anomally'=>'No']);
    }
    public function anomally()
    {
        return $this->andWhere(['pipe_cleargrade.is_deleted'=>0,'pipe_cleargrade.project_id'=>Yii::$app->user->identity->project_id,'pipe_cleargrade.is_anomally'=>"Yes"]);
    }
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Bending|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
