<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Cutting]].
 *
 * @see Cutting
 */
class CuttingQuery extends \yii\db\ActiveQuery
{
   
    public function active()
    {
        return $this->andWhere(['pipe_cuting.is_deleted'=>0,'pipe_cuting.project_id'=>Yii::$app->user->identity->project_id,'pipe_cuting.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['pipe_cuting.is_deleted'=>0,'pipe_cuting.project_id'=>Yii::$app->user->identity->project_id,'pipe_cuting.is_anomally'=>"Yes"]);
    }
    /**
     * @inheritdoc
     * @return Cuting[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Cuting|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
