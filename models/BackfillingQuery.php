<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Backfilling]].
 *
 * @see Backfilling
 */
class BackfillingQuery extends \yii\db\ActiveQuery
{
  

    public function active()
    {
        return $this->andWhere(['civil_backfilling.is_deleted'=>0,'civil_backfilling.project_id'=>Yii::$app->user->identity->project_id,'civil_backfilling.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['civil_backfilling.is_deleted'=>0,'civil_backfilling.project_id'=>Yii::$app->user->identity->project_id,'civil_backfilling.is_anomally'=>"Yes"]);
    }

    /**
     * @inheritdoc
     * @return Backfilling[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Backfilling|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
