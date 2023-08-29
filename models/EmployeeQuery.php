<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Employee]].
 *
 * @see Employee
 */
class EmployeeQuery extends \yii\db\ActiveQuery
{
   
    public function active()
    {
        return $this->andWhere(['user.is_deleted'=>0]);
    }
    /**
     * @inheritdoc
     * @return Employee[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Employee|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
