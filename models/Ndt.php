<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "welding_ndt".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property int $project_id
 * @property double $kp
 * @property string $weld_number
 * @property string $outcome
 * @property string $comment
 * @property string $is_anomally
 * @property string $why_anomally
 * @property string $signed_off
 * @property int $qa_manager
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 */
class Ndt extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'welding_ndt';
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
            [['date', 'report_number', 'kp', 'weld_number', 'outcome', 'main_weld_id'], 'required'],
            [['date'], 'safe'],
            [['project_id', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['kp'], 'number'],
            [['outcome', 'is_anomally', 'signed_off'], 'string'],            
            [['report_number', 'weld_number', 'comment', 'why_anomally'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function print_attributes(){
        return [
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'weld_number' => Yii::$app->trans->getTrans('Weld Number'),
            'weld_type' => Yii::$app->trans->getTrans('Weld Type'),
            'weld_sub_type' => Yii::$app->trans->getTrans('Weld Sub Type'),
            'outcome' => Yii::$app->trans->getTrans('Outcome'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'weld_number' => Yii::$app->trans->getTrans('Weld Number'),          
            'outcome' => Yii::$app->trans->getTrans('Outcome'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'is_anomally' => 'Is Anomally',
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * @inheritdoc
     * @return NdtQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NdtQuery(get_called_class());
    }

    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {
            if(Yii::$app->controller->id !="sync"){
                // if($this->outcome =="Rejected"){

                //     $Weldingrepair             = \app\models\Weldingrepair::find()->where(['kp'=>$this->kp,'weld_number'=>$this->weld_number])->active()->one();       
                //     $weld_sub_type= !empty($Weldingrepair)?"RWR":"RW";
                //     $wellding = \app\models\Welding::find()->where(['kp'=>$this->kp,'weld_number'=>$this->weld_number])->active()->one();
                //     if(!empty($wellding) && !empty($weld_sub_type)){
                //         $wellding->weld_sub_type = $weld_sub_type;
                //         $wellding->save();
                //     }  
                // }                
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
