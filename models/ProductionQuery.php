<?php

namespace app\models;
use Yii;

/**
 * This is the ActiveQuery class for [[Production]].
 *
 * @see Production
 */
class ProductionQuery extends \yii\db\ActiveQuery
{
   
    public function active()
    {
        return $this->andWhere(['welding_coating_production.is_deleted'=>0,'welding_coating_production.project_id'=>Yii::$app->user->identity->project_id,'welding_coating_production.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['welding_coating_production.is_deleted'=>0,'welding_coating_production.project_id'=>Yii::$app->user->identity->project_id,'welding_coating_production.is_anomally'=>"Yes"]);
    }
    /**
     * @inheritdoc
     * @return Production[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Production|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
