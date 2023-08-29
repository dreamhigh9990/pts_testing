<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "defact_picture".
 *
 * @property int $id
 * @property int $section_id
 * @property string $section_type
 * @property string $image
 */
class Picture extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'defact_picture';
    }

    public function behaviors()
    {
        return [
        //     [
        //         'class' => \yii\behaviors\TimestampBehavior::className(),
        //         'attributes' => [
        //             \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
        //             \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
        //         ],
        //     ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['section_id', 'section_type'], 'required'],
		    [['image'], 'file', 'skipOnEmpty' => true, 'extensions'=>['xls','xlsx','txt','pdf','csv','doc','docx','jpg','jpeg','gif','png'], 'checkExtensionByMimeType'=>false, 'maxSize'=>1024 * 1024 * 500,'maxFiles'=>10],            
            [['section_id'], 'integer'],
            [['section_type'], 'string'],
            [['image','mime_type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'section_id' => 'Section ID',
            'section_type' => Yii::$app->trans->getTrans('Section Type'),
            'image' => Yii::$app->trans->getTrans('Files'),
            'mime_type' => Yii::$app->trans->getTrans('Mime Type'),
        ];
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
