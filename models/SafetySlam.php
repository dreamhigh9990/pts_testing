<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "safety_slam".
 *
 * @property int $id
 * @property string $name Name
 * @property string $crew Crew
 * @property string $location Location
 * @property string $task Task
 * @property string $date_time Date and Time
 * @property string $potential_hazards Potential Hazards
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property int $updated_at
 */
class SafetySlam extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'safety_slam';
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
            [
                'class'=>\yii\behaviors\BlameableBehavior::className(),
            ]
        ];
    }
    public function rules()
    {
        return [
            [['crew', 'location', 'task','name'], 'required'],
            [['task'], 'string'],
            [['date_time','potential_hazards','created_at','report_number'], 'safe'],
            [['created_by', 'updated_by', 'updated_at'], 'integer'],
            [['name', 'crew', 'location','report_number'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::$app->trans->getTrans('Full Name'),
            'crew' => Yii::$app->trans->getTrans('Crew'),
            'location' => Yii::$app->trans->getTrans('Location'),
            'task' => Yii::$app->trans->getTrans('Task'),
            'date_time' => Yii::$app->trans->getTrans('Date Time'),
            'potential_hazards' => Yii::$app->trans->getTrans('Potential Hazards'),
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            // 'name'=>'Report Name'
        ];
    }
    public static function find()
    {
        return new SlamQuery(get_called_class());
    }
}
