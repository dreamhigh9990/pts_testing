<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "deleted_item".
 *
 * @property int $id
 * @property string $table_name
 * @property int $table_id
 * @property string $created_at
 */
class DeletedItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'deleted_item';
    }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }
    public function rules()
    {
        return [
            [['table_name', 'table_id'], 'required'],
            [['table_id'], 'integer'],
            [['created_at'], 'safe'],
            [['table_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'table_name' => Yii::t('app', 'Table Name'),
            'table_id' => Yii::t('app', 'Table ID'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }
}
