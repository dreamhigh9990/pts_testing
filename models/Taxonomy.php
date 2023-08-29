<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "taxonomy".
 *
 * @property int $id
 * @property string $name
 * @property int $child_value
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
            [['child_value'], 'integer'],
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
            'child_value' => 'Child Value',
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
