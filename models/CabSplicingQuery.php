<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[CabSplicing]].
 *
 * @see CabSplicing
 */
class CabSplicingQuery extends \yii\db\ActiveQuery
{
    
    public function active()
    {
        return $this->andWhere(['cabling_splicing.is_deleted'=>0,'cabling_splicing.project_id'=>Yii::$app->user->identity->project_id,'cabling_splicing.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['cabling_splicing.is_deleted'=>0,'cabling_splicing.project_id'=>Yii::$app->user->identity->project_id,'cabling_splicing.is_anomally'=>"Yes"]);
    }
    /**
     * @inheritdoc
     * @return CabSplicing[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CabSplicing|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
