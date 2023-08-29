<?php

namespace app\modules\catalogue\models;

use Yii;

/**
 * This is the model class for table "taxonomy_value".
 *
 * @property int $id
 * @property int $taxonomy_id
 * @property string $value
 *
 * @property Taxonomy $taxonomy
 */
class TaxonomyValue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'taxonomy_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['taxonomy_id', 'value'], 'required'],
            [['taxonomy_id'], 'integer'],
            [['value'], 'string', 'max' => 255],
			['value', 'unique', 'targetAttribute' => 'taxonomy_id'],
            [['taxonomy_id'], 'exist', 'skipOnError' => true, 'targetClass' => Taxonomy::className(), 'targetAttribute' => ['taxonomy_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'taxonomy_id' => 'Taxonomy ID',
            'value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxonomy()
    {
        return $this->hasOne(Taxonomy::className(), ['id' => 'taxonomy_id']);
    }
}
