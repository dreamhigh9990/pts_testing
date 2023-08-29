<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Line]].
 *
 * @see Line
 */
class LineQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['admin_line_list.is_deleted'=>0,'admin_line_list.project_id'=>Yii::$app->user->identity->project_id]);
    }

    /**
     * @inheritdoc
     * @return Line[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Line|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
