<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pipe_bending".
 *
 * @property int $id
 * @property string $report_number
 * @property string $pipe_number
 * @property int $pipe_id
 * @property string $designation
 * @property double $angle
 * @property string $defacts
 * @property double $kp
 * @property string $position
 * @property string $pull_through_accepted
 * @property int $comment
 * @property string $qa_manager
 * @property string $signed_off
 * @property string $date
 * @property int $project_id
 * @property string $is_anomally
 * @property string $why_anomally
 * @property int $is_deleted
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class Bending extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $kp;
    public $wall_thikness;
    public static function tableName()
    {
        return 'pipe_bending';
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['report_number', 'pipe_number','angle','date', 'bending_checkpoints'], 'required'],
            [['report_number', 'pipe_number','angle','date'], 'required'],
            [['pipe_id','project_id', 'is_deleted', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['angle', 'kp', 'chainage'], 'number'],
            [['signed_off', 'is_anomally','comment'], 'string'],
            [['date'], 'safe'],
            // ['bending_checkpoints', "checkCheckPoints"],
            [['report_number', 'pipe_number', 'designation', 'position', 'why_anomally', 'pull_through_accepted'], 'string', 'max' => 255],
        ];
    }

    public function checkCheckPoints($attribute, $params)
    {
        $listItems = Yii::$app->general->TaxonomyDrop(33);

        $this->bending_checkpoints = $this->bending_checkpoints;
        if(!is_array($this->bending_checkpoints)){
            $this->bending_checkpoints = json_decode($this->bending_checkpoints,true);
        }

        if(count($this->bending_checkpoints) != count($listItems)){
            $this->addError('Bending Checks', 'Please ensure all checks are completed');
        }
    }

    public function print_attributes(){
        return [
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'pipe_number' => Yii::$app->trans->getTrans('Pipe Number'),
            'designation' => Yii::$app->trans->getTrans('Designation'),
            'angle' => Yii::$app->trans->getTrans('Angle'),
            // 'kp' => Yii::$app->trans->getTrans('KP'),
            'chainage' => Yii::$app->trans->getTrans('Chainage'),
            'position' => Yii::$app->trans->getTrans('Position'),
            'bending_checkpoints' => Yii::$app->trans->getTrans('Bending Checks'),
            'comment' => Yii::$app->trans->getTrans('Comment'),            
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'pipe_number' => Yii::$app->trans->getTrans('Pipe Number'),
            'pipe_id' => Yii::$app->trans->getTrans('Pipe ID'),
            'designation' => Yii::$app->trans->getTrans('Designation'),
            'angle' => Yii::$app->trans->getTrans('Angle'),
            // 'kp' => Yii::$app->trans->getTrans('KP'),
            'chainage' => Yii::$app->trans->getTrans('Chainage'),
            'position' => Yii::$app->trans->getTrans('Position'),
            'bending_checkpoints' => Yii::$app->trans->getTrans('Bending Checks'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'pull_through_accepted' => Yii::$app->trans->getTrans('Pull through accepted'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'date' => Yii::$app->trans->getTrans('Date'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'is_anomally' => 'Is Anomally',
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'is_deleted' => 'Is Deleted',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return BendingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BendingQuery(get_called_class());
    }
    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {
            if(Yii::$app->controller->id != "sync"){
                if(is_array($this->bending_checkpoints)){
                    echo 111;
                    $this->bending_checkpoints = json_encode($this->bending_checkpoints);
                } else {
                    echo 222;
                    $this->bending_checkpoints = '';
                }
            }
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
