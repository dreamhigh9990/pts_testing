<?php

namespace app\modules\catalogue\models;

use Yii;

/**
 * This is the model class for table "taxonomy".
 *
 * @property int $id
 * @property string $name
 *
 * @property TaxonomyValue[] $taxonomyValues
 */
class Taxonomy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'taxonomy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxonomyValues()
    {
        return $this->hasMany(TaxonomyValue::className(), ['taxonomy_id' => 'id']);
    }
}
