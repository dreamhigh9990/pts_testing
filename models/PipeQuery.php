<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Pipe]].
 *
 * @see Pipe
 */
class PipeQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['pipe.is_deleted'=>0,'pipe.project_id'=>Yii::$app->user->identity->project_id,'pipe.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['pipe.is_deleted'=>0,'pipe.project_id'=>Yii::$app->user->identity->project_id,'pipe.is_anomally'=>"Yes"]);
    }
    
    /**
     * @inheritdoc
     * @return Pipe[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Pipe|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
