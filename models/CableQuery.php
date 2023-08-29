<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Cable]].
 *
 * @see Cable
 */
class CableQuery extends \yii\db\ActiveQuery
{
   
    public function active()
    {
        return $this->andWhere(['cabling_drum.is_deleted'=>0,'cabling_drum.project_id'=>Yii::$app->user->identity->project_id,'cabling_drum.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['cabling_drum.is_deleted'=>0,'cabling_drum.project_id'=>Yii::$app->user->identity->project_id,'cabling_drum.is_anomally'=>"Yes"]);
    }
    
    public function all($db = null)
    {
        return parent::all($db);
    }
    public function one($db = null)
    {
        return parent::one($db);
    }
}
