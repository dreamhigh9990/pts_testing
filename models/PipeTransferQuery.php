<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[PipeTransfer]].
 *
 * @see PipeTransfer
 */
class PipeTransferQuery extends \yii\db\ActiveQuery
{
    
    public function active()
    {
        return $this->andWhere(['pipe_transfer.is_deleted'=>0,'pipe_transfer.project_id'=>Yii::$app->user->identity->project_id,'pipe_transfer.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['pipe_transfer.is_deleted'=>0,'pipe_transfer.project_id'=>Yii::$app->user->identity->project_id,'pipe_transfer.is_anomally'=>"Yes"]);
    }

    /**
     * @inheritdoc
     * @return PipeTransfer[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PipeTransfer|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
