<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TaxonomyValue]].
 *
 * @see TaxonomyValue
 */
class TaxonomyValueQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['taxonomy_value.is_deleted'=>0]);
    }
}
