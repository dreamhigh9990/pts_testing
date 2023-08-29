<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Cleangauge]].
 *
 * @see Cleangauge
 */
class CleangaugeQuery extends \yii\db\ActiveQuery
{
    
    public function active()
    {
        return $this->andWhere(['com_clean_gauge.is_deleted'=>0,'com_clean_gauge.project_id'=>Yii::$app->user->identity->project_id,'com_clean_gauge.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['com_clean_gauge.is_deleted'=>0,'com_clean_gauge.project_id'=>Yii::$app->user->identity->project_id,'com_clean_gauge.is_anomally'=>"Yes"]);
    }
    /**
     * @inheritdoc
     * @return Cleangauge[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Cleangauge|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
