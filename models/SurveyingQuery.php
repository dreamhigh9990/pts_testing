<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Surveying]].
 *
 * @see Surveying
 */
class SurveyingQuery extends \yii\db\ActiveQuery
{
    
    public function active()
    {
        return $this->andWhere(['com_surveying.is_deleted'=>0,'com_surveying.project_id'=>Yii::$app->user->identity->project_id]);
    }
    public function anomally()
    {
        return $this->andWhere(['com_surveying.is_deleted'=>0,'com_surveying.project_id'=>Yii::$app->user->identity->project_id,'com_surveying.is_anomally'=>"Yes"]);
    }

    /**
     * @inheritdoc
     * @return Surveying[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Surveying|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
