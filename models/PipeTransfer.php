<?php

namespace app\models;
use app\models\Pipe;
use Yii;

/**
 * This is the model class for table "pipe_transfer".
 *
 * @property int $id
 * @property string $report_number
 * @property string $pipe_number
 * @property int $pipe_id
 * @property string $new_location
 * @property string $current_location
 * @property string $truck
 * @property string $defacts
 * @property string $comment
 * @property string $signed_off
 * @property int $qa_manager
 * @property string $date
 * @property int $project_id
 * @property string $is_anomally
 * @property string $why_anomally
 * @property int $created_at
 * @property int $updated_at
 */
class PipeTransfer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $heat_number;
    public $pipe_length;
    public static function tableName()
    {
        return 'pipe_transfer';
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
            [['pipe_number', 'new_location', 'date'], 'required'],
            [['pipe_id','project_id', 'created_at', 'updated_at', 'qa_manager'], 'integer'],
            [[ 'comment', 'signed_off', 'is_anomally'], 'string'],
            [['date'], 'safe'],
            [['report_number', 'pipe_number', 'new_location', 'current_location', 'why_anomally','truck'], 'string', 'max' => 255],
        ];
    }
    public function print_attributes(){
        return [
            'date' => Yii::$app->trans->getTrans('Date'),
            'pipe_weight' => Yii::$app->trans->getTrans('Pipe Weight'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'pipe_number' => Yii::$app->trans->getTrans('Pipe Number'),
            'new_location' => Yii::$app->trans->getTrans('New Location'),
            'current_location' => Yii::$app->trans->getTrans('Current Location'),
            'truck' => Yii::$app->trans->getTrans('Truck'),
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
            'new_location' => Yii::$app->trans->getTrans('New Location'),
            'current_location' => Yii::$app->trans->getTrans('Current Location'),
            'truck' => Yii::$app->trans->getTrans('Truck'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'date' => Yii::$app->trans->getTrans('Date'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'is_anomally' => 'Is Anomally',
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    /**
     * @inheritdoc
     * @return PipeTransferQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PipeTransferQuery(get_called_class());
    }
    public function updateLocation(){
      
         $Reception = \app\models\Reception::find()->where(['pipe_number'=>$this->pipe_number])->active()->one();
         if(!empty($Reception)){
            $Reception->location =   $this->new_location;
            $Reception->transferred =   "Yes";
            $Reception->save();
         }


         $Stringing = \app\models\Stringing::find()->where(['pipe_number'=>$this->pipe_number])->active()->one();
        
         if(!empty($Stringing)){
            $Stringing->relocated        =   "Yes";
            $Stringing->save();
         }
         return;
        
    }
    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {
            $this->updateLocation();            		
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
