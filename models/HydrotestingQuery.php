<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Hydrotesting]].
 *
 * @see Hydrotesting
 */
class HydrotestingQuery extends \yii\db\ActiveQuery
{
   
    public function active()
    {
        return $this->andWhere(['com_hydrotesting.is_deleted'=>0,'com_hydrotesting.project_id'=>Yii::$app->user->identity->project_id,'com_hydrotesting.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['com_hydrotesting.is_deleted'=>0,'com_hydrotesting.project_id'=>Yii::$app->user->identity->project_id,'com_hydrotesting.is_anomally'=>"Yes"]);
    }
    /**
     * @inheritdoc
     * @return Hydrotesting[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Hydrotesting|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
