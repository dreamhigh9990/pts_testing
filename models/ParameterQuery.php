<?php

namespace app\models;
use yii;
/**
 * This is the ActiveQuery class for [[Parameter]].
 *
 * @see Parameter
 */
class ParameterQuery extends \yii\db\ActiveQuery
{
   
    
    public function active()
    {
        return $this->andWhere(['welding_parameter_check.is_deleted'=>0,'welding_parameter_check.project_id'=>Yii::$app->user->identity->project_id,'welding_parameter_check.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['welding_parameter_check.is_deleted'=>0,'welding_parameter_check.project_id'=>Yii::$app->user->identity->project_id,'welding_parameter_check.is_anomally'=>"Yes"]);
    }

    /**
     * @inheritdoc
     * @return Parameter[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Parameter|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
