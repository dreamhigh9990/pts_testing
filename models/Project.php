<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "taxonomy_value".
 *
 * @property int $id
 * @property int $taxonomy_id
 * @property string $value
 * @property double $location_lat
 * @property double $location_long
 * @property int $project_id
 * @property int $is_deleted
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Pipe[] $pipes
 */
class Project extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'taxonomy_value';
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['value', 'required'],
            ['value','filter','filter'=>'trim'],
            [['taxonomy_id', 'project_id', 'is_deleted', 'created_at', 'updated_at'], 'integer'],
            [['location_lat', 'location_long'], 'number'],
            [['value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->trans->getTrans('ID'),
            'taxonomy_id' => Yii::$app->trans->getTrans('Taxonomy ID'),
            'value' => Yii::$app->trans->getTrans('Value'),
            'location_lat' => Yii::$app->trans->getTrans('Location Lat'),
            'location_long' => Yii::$app->trans->getTrans('Location Long'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'is_deleted' => Yii::$app->trans->getTrans('Is Deleted'),
            'created_at' => Yii::$app->trans->getTrans('Created At'),
            'updated_at' => Yii::$app->trans->getTrans('Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPipes()
    {
        return $this->hasMany(Pipe::className(), ['project_id' => 'id']);
    }
    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {	
            $this->taxonomy_id = 4;
        	return true;
		} else {
			return false;
		}
	}
}
