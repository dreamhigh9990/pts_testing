<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Reception]].
 *
 * @see Reception
 */
class ReceptionQuery extends \yii\db\ActiveQuery
{
    
    public function active()
    {
        return $this->andWhere(['pipe_reception.is_deleted'=>0,'pipe_reception.project_id'=>Yii::$app->user->identity->project_id,'pipe_reception.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['pipe_reception.is_deleted'=>0,'pipe_reception.project_id'=>Yii::$app->user->identity->project_id,'pipe_reception.is_anomally'=>"Yes"]);
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
