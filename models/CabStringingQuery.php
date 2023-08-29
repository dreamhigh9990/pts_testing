<?php
namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[CabStringing]].
 *
 * @see CabStringing
 */
class CabStringingQuery extends \yii\db\ActiveQuery
{
    
    public function active()
    {
        return $this->andWhere(['cabling_stringing.is_deleted'=>0,'cabling_stringing.project_id'=>Yii::$app->user->identity->project_id,'cabling_stringing.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['cabling_stringing.is_deleted'=>0,'cabling_stringing.project_id'=>Yii::$app->user->identity->project_id,'cabling_stringing.is_anomally'=>"Yes"]);
    }

    /**
     * @inheritdoc
     * @return CabStringing[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }
    public function one($db = null)
    {
        return parent::one($db);
    }
}
