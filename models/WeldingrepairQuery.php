<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Weldingrepair]].
 *
 * @see Weldingrepair
 */
class WeldingrepairQuery extends \yii\db\ActiveQuery
{
    
    public function active()
    {
        return $this->andWhere(['welding_repair.is_deleted'=>0,'welding_repair.project_id'=>Yii::$app->user->identity->project_id,'welding_repair.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['welding_repair.is_deleted'=>0,'welding_repair.project_id'=>Yii::$app->user->identity->project_id,'welding_repair.is_anomally'=>"Yes"]);
    }

    /**
     * @inheritdoc
     * @return Weldingrepair[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Weldingrepair|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
