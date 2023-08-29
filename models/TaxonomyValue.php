<?php

namespace app\models;

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
            [['taxonomy_id', 'value'], 'required'],
            ['value','filter','filter'=>'trim'],
            [['taxonomy_id', 'project_id'], 'integer'],
            [['value', 'location_lat', 'location_long','batch_number','type','size'], 'string', 'max' => 255],
            //['value', 'unique', 'filter' => ['<>', 'is_deleted', 1]],
            [['taxonomy_id'], 'exist', 'skipOnError' => true, 'targetClass' => Taxonomy::className(), 'targetAttribute' => ['taxonomy_id' => 'id']],

            ['taxonomy_id', 'required', 'when' => function ($model) {
               
            }, 'whenClient' => "function (attribute, value) {
                $('.electrode').hide();
                if(value==5){
                    $('.electrode').show();
                }
            }"]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'taxonomy_id' => Yii::$app->trans->getTrans('Catalouge Type'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'value' => Yii::$app->trans->getTrans('Value'),
            'type' => Yii::$app->trans->getTrans('Type'),
            'size' => Yii::$app->trans->getTrans('Size')
            
        ];
    }
    public function getTaxonomy()
    {
        return $this->hasOne(Taxonomy::className(), ['id' => 'taxonomy_id']);
    }
    public static function getTaxonomyList(){
        $list = Taxonomy::find()->where(['!=','id','4'])->asArray()->all();
        return $list;
    }
    public static function find()
    {
        return new TaxonomyValueQuery(get_called_class());
    }
    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {  
            $this->project_id = Yii::$app->user->identity->project_id;	
            $this->value = str_replace("'","`",$this->value);
            $this->value = str_replace('"',"`",$this->value);
        	return true;
		} else {
			return false;
		}
	}

}
