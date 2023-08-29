<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cabling_drum".
 *
 * @property int $id
 * @property string $drum_number
 * @property string $drum_cable
 * @property double $length
 * @property string $brand
 * @property double $cores
 * @property string $standard
 * @property string $colour
 * @property string $comment
 * @property int $project_id
 * @property string $is_anomally
 * @property string $why_anomally
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $update_at
 * @property int $is_deleted
 */
class Cable extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cabling_drum';
    }

    public function behaviors()
    {
        return [
            // [
            //     'class' => \yii\behaviors\TimestampBehavior::className(),
            //     'attributes' => [
            //         \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
            //         \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
            //     ],
            // ],
            [
                'class'=>\yii\behaviors\BlameableBehavior::className(),
            ]
        ];
    }
    public function rules()
    {
        return [
            [['drum_number'], 'required'],
            ['drum_number', 'filter', 'filter'=>'trim'],
            [['length', 'cores'], 'number'],
            [['comment', 'is_anomally'], 'string'],
            [['project_id', 'created_by', 'updated_by', 'created_at', 'updated_at', 'is_deleted'], 'integer'],
            [['drum_number', 'drum_cable', 'brand', 'standard', 'colour', 'why_anomally'], 'string', 'max' => 255],
        ];
    }   
    /**
     * @inheritdoc
     */
    public function print_attributes()
    {
        return [
           
            'drum_number' => Yii::$app->trans->getTrans('Drum Number'),
            'drum_cable' => Yii::$app->trans->getTrans('Drum Cable'),
            'length' => Yii::$app->trans->getTrans('Length'),
            'brand' => Yii::$app->trans->getTrans('Brand'),
            'cores' => Yii::$app->trans->getTrans('Cores'),
            'standard' => Yii::$app->trans->getTrans('Standard'),
            'colour' => Yii::$app->trans->getTrans('Colour'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->trans->getTrans('ID'),
            'drum_number' => Yii::$app->trans->getTrans('Drum Number'),
            'drum_cable' => Yii::$app->trans->getTrans('Type of Cable'),
            'length' => Yii::$app->trans->getTrans('Length'),
            'brand' => Yii::$app->trans->getTrans('Brand'),
            'cores' => Yii::$app->trans->getTrans('Cores'),
            'standard' => Yii::$app->trans->getTrans('Standard'),
            'colour' => Yii::$app->trans->getTrans('Colour'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'is_anomally' => Yii::$app->trans->getTrans('Is Anomally'),
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'created_by' => Yii::$app->trans->getTrans('Created By'),
            'updated_by' => Yii::$app->trans->getTrans('Update By'),
            'created_at' => Yii::$app->trans->getTrans('Created At'),
            'updated_at' => Yii::$app->trans->getTrans('Update At'),
            'qa_manager' => Yii::$app->trans->getTrans('Qa Manager'),
            'is_deleted' => Yii::$app->trans->getTrans('Is Deleted'),
        ];
    }

    /**
     * @inheritdoc
     * @return CableQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CableQuery(get_called_class());
    }
    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {					
            $this->project_id = empty($this->project_id) ? Yii::$app->user->identity->project_id : $this->project_id;

            $mo = Yii::$app->general->setTimestamp($this);
            $this->created_at =  $mo->created_at;
            $this->updated_at  = $mo->updated_at; 
            
        	return true;
		} else {
			return false;
		}
	}
}
