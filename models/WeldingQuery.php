<?php

namespace app\models;
use yii;
/**
 * This is the ActiveQuery class for [[Welding]].
 *
 * @see Welding
 */
class WeldingQuery extends \yii\db\ActiveQuery
{
  
    public function active()
    {
        return $this->andWhere(['welding.is_deleted'=>0,'welding.project_id'=>Yii::$app->user->identity->project_id,'welding.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['welding.is_deleted'=>0,'welding.project_id'=>Yii::$app->user->identity->project_id,'welding.is_anomally'=>"Yes"]);
    }
    /**
     * @inheritdoc
     * @return Welding[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Welding|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
