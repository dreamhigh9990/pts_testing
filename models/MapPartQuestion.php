<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "map_part_question".
 *
 * @property int $id
 * @property int $part_id
 * @property string $question
 * @property int $created_at
 * @property int $updated_at
 *
 * @property TaxonomyValue $part
 */
class MapPartQuestion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map_part_question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['part_id', 'question', 'created_at', 'updated_at'], 'required'],
            [['part_id', 'created_at', 'updated_at'], 'integer'],
            [['question'], 'string', 'max' => 350],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaxonomyValue::className(), 'targetAttribute' => ['part_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'part_id' => 'Part ID',
            'question' => 'Question',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPart()
    {
        return $this->hasOne(TaxonomyValue::className(), ['id' => 'part_id']);
    }

    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {
            $mo = Yii::$app->general->setTimestamp($this);
            $this->created_at =  $mo->created_at;
            $this->updated_at  = $mo->updated_at;
            return true;
		} else {
			return false;
		}
	}
}
