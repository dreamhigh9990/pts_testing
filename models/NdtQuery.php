<?php

namespace app\models;
use yii;

/**
 * This is the ActiveQuery class for [[Ndt]].
 *
 * @see Ndt
 */
class NdtQuery extends \yii\db\ActiveQuery
{
    
    public function active()
    {
        return $this->andWhere(['welding_ndt.is_deleted'=>0,'welding_ndt.project_id'=>Yii::$app->user->identity->project_id,'welding_ndt.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['welding_ndt.is_deleted'=>0,'welding_ndt.project_id'=>Yii::$app->user->identity->project_id,'welding_ndt.is_anomally'=>"Yes"]);
    }

    /**
     * @inheritdoc
     * @return Ndt[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Ndt|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
