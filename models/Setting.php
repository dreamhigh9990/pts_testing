<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property int $id
 * @property string $name
 * @property string $value
 */
class Setting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['value', 'required'],
            [['value'], 'file', 'skipOnEmpty' => false, 'extensions'=>['jpg','jpeg','gif','png'], 'checkExtensionByMimeType'=>false, 'maxSize'=>1024 * 1024 * 2,'maxFiles'=>1],            
           
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::$app->trans->getTrans('Name'),
            'value' => Yii::$app->trans->getTrans('Value'),
        ];
    }
}
