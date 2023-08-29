<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Cathodicprotection]].
 *
 * @see Cathodicprotection
 */
class CathodicprotectionQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['com_cathodic_protection.is_deleted'=>0,'com_cathodic_protection.project_id'=>Yii::$app->user->identity->project_id,'com_cathodic_protection.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['com_cathodic_protection.is_deleted'=>0,'com_cathodic_protection.project_id'=>Yii::$app->user->identity->project_id,'com_cathodic_protection.is_anomally'=>"Yes"]);
    }
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Cathodicprotection|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
