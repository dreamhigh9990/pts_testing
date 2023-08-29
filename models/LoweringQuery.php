<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Lowering]].
 *
 * @see Lowering
 */
class LoweringQuery extends \yii\db\ActiveQuery
{
  
    public function active()
    {
        return $this->andWhere(['civil_lowering.is_deleted'=>0,'civil_lowering.project_id'=>Yii::$app->user->identity->project_id,'civil_lowering.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['civil_lowering.is_deleted'=>0,'civil_lowering.project_id'=>Yii::$app->user->identity->project_id,'civil_lowering.is_anomally'=>"Yes"]);
    }
    /**
     * @inheritdoc
     * @return Lowering[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Lowering|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
