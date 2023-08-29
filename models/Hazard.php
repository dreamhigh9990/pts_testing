<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hazard_report".
 *
 * @property int $id
 * @property string $crew
 * @property string $location
 * @property string $date_time
 * @property string $details
 * @property string $action
 * @property string $supervisor_in_charged
 * @property string $is_followup
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class Hazard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hazard_report';
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
            [['crew', 'location', 'details', 'action', 'supervisor_in_charged', 'is_followup','name'], 'required'],
            [['date_time','report_number','name'], 'safe'],
            [['details', 'action', 'supervisor_in_charged', 'is_followup'], 'string'],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['crew', 'location','report_number'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->trans->getTrans('ID'),
            'crew' => Yii::$app->trans->getTrans('Crew'),
            'location' => Yii::$app->trans->getTrans('Location'),
            'date_time' => Yii::$app->trans->getTrans('Date Time'),
            'details' => Yii::$app->trans->getTrans('Details'),
            'action' => Yii::$app->trans->getTrans('Immediate Action Taken'),
            'supervisor_in_charged' => Yii::$app->trans->getTrans('Has The Supervisor In Charge Been Informed ?'),
            'is_followup' => Yii::$app->trans->getTrans('Is Follow Up Action Required').' ?',
            'created_at' => Yii::$app->trans->getTrans('Created At'),
            'updated_at' => Yii::$app->trans->getTrans('Updated At'),
            'created_by' => Yii::$app->trans->getTrans('Created By'),
            'updated_by' => Yii::$app->trans->getTrans('Name'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'name' => Yii::$app->trans->getTrans('Full Name')
        ];
    }
    public static function find()
    {
        return new HazardReportQuery(get_called_class());
    }
}
