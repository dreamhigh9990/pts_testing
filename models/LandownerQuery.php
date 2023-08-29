<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Landowner]].
 *
 * @see Landowner
 */
class LandownerQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['admin_landowner.is_deleted'=>0,'admin_landowner.project_id'=>Yii::$app->user->identity->project_id]);
    }

    /**
     * @inheritdoc
     * @return Landowner[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Landowner|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
