<?php

namespace app\models;
use Yii;
/**
 * This is the ActiveQuery class for [[Reinstatement]].
 *
 * @see Reinstatement
 */
class ReinstatementQuery extends \yii\db\ActiveQuery
{
    
    public function active()
    {
        return $this->andWhere(['civil_reinstatement.is_deleted'=>0,'civil_reinstatement.project_id'=>Yii::$app->user->identity->project_id,'civil_reinstatement.is_active'=>1]);
    }
    public function anomally()
    {
        return $this->andWhere(['civil_reinstatement.is_deleted'=>0,'civil_reinstatement.project_id'=>Yii::$app->user->identity->project_id,'civil_reinstatement.is_anomally'=>"Yes"]);
    }


    /**
     * @inheritdoc
     * @return Reinstatement[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Reinstatement|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
