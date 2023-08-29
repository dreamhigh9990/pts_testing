<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Bending]].
 *
 * @see Bending
 */
class BendingQuery extends \yii\db\ActiveQuery
{
    
    public function active()
    {
        return $this->andWhere(['pipe_bending.is_deleted'=>0,'pipe_bending.project_id'=>Yii::$app->user->identity->project_id,'pipe_bending.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['pipe_bending.is_deleted'=>0,'pipe_bending.project_id'=>Yii::$app->user->identity->project_id,'pipe_bending.is_anomally'=>"Yes"]);
    }
    /**
     * @inheritdoc
     * @return Bending[]|array
     */
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
