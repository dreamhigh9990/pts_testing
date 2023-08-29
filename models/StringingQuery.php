<?php

namespace app\models;
use Yii;

/**
 * This is the ActiveQuery class for [[Stringing]].
 *
 * @see Stringing
 */
class StringingQuery extends \yii\db\ActiveQuery
{
   
    public function active()
    {
        return $this->andWhere(['pipe_stringing.is_deleted'=>0,'pipe_stringing.project_id'=>Yii::$app->user->identity->project_id,'pipe_stringing.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['pipe_stringing.is_deleted'=>0,'pipe_stringing.project_id'=>Yii::$app->user->identity->project_id,'pipe_stringing.is_anomally'=>"Yes"]);
    }
    /**
     * @inheritdoc
     * @return Stringing[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Stringing|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
