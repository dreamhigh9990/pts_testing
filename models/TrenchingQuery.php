<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Trenching]].
 *
 * @see Trenching
 */
class TrenchingQuery extends \yii\db\ActiveQuery
{
    
    public function active()
    {
        return $this->andWhere(['civil_trenching.is_deleted'=>0,'civil_trenching.project_id'=>Yii::$app->user->identity->project_id,'civil_trenching.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['civil_trenching.is_deleted'=>0,'civil_trenching.project_id'=>Yii::$app->user->identity->project_id,'civil_trenching.is_anomally'=>"Yes"]);
    }

    /**
     * @inheritdoc
     * @return Trenching[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Trenching|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
