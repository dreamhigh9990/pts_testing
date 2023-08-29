<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "welding_repair".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property string $is_anomally
 * @property int $project_id
 * @property string $signed_off
 * @property int $qa_manager
 * @property double $kp
 * @property string $weld_number
 * @property string $weld_sub_type
 * @property string $welder
 * @property string $wps
 * @property string $electrodes
 * @property string $examination
 * @property string $excavation
 * @property string $repair_examination
 * @property string $comment
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted 
 * @property string $area
 * @property string $size 
 * @property string $excavation  
 * @property string $why_anomally  
 */
class Weldingrepair extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'welding_repair';
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
            // [['date', 'report_number', 'kp', 'weld_number', 'wps', 'weld_sub_type', 'welder', 'examination','repair_examination', 'main_weld_id'], 'required'],
            [['date', 'report_number', 'kp', 'weld_number', 'wps', 'weld_sub_type', 'welder', 'examination', 'repair_examination', 'main_weld_id'], 'required', 'when' => function ($model) {
                if($model->excavation == 'Cut-Out'){
                    return false;
                } else {
                    return true;
                }
            }, 'whenClient' => "function (attribute, value) {
                if($('#weldingrepair-excavation').val() == 'Cut-Out'){
                    return false;
                } else {
                    return true;
                }
            }"],
            [['date','electrodes','excavation'], 'safe'],
            [['is_anomally', 'signed_off', 'repair_examination', 'comment'], 'string'],
            [['project_id', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['kp'], 'number'],
            [['report_number', 'weld_number', 'weld_sub_type', 'welder', 'wps', 'examination','size','area'], 'string', 'max' => 255],
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
            'welder' => Yii::$app->trans->getTrans('Welder'),
            'electrodes' => Yii::$app->trans->getTrans('Electrodes'),
            'examination' => Yii::$app->trans->getTrans('Examination'),
            'excavation' => Yii::$app->trans->getTrans('Action Decided'),
            'area' => Yii::$app->trans->getTrans('Area'),
            'size' => Yii::$app->trans->getTrans('Size'),
            // 'new_ndt_result' => 'New Ndt Result',
            'repair_examination' => Yii::$app->trans->getTrans('Repair Examination'),
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
            'is_anomally' => 'Is Anomally',
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'weld_number' => Yii::$app->trans->getTrans('Weld Number'),
            'weld_sub_type' => Yii::$app->trans->getTrans('Weld Sub Type'),
            'welder' => Yii::$app->trans->getTrans('Welder'),
            'wps' => Yii::$app->trans->getTrans('Wps'),
            'electrodes' => Yii::$app->trans->getTrans('Electrodes'),
            'examination' => Yii::$app->trans->getTrans('Examination'),
            'excavation' => Yii::$app->trans->getTrans('Action Decided'),
            'repair_examination' => Yii::$app->trans->getTrans('Repair Examination'),
            'area' => Yii::$app->trans->getTrans('Area'),
            'size' => Yii::$app->trans->getTrans('Size'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'created_at' => 'Created At',
            'updated_at' => 'Update At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * @inheritdoc
     * @return WeldingrepairQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new WeldingrepairQuery(get_called_class());
    }

    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {
            
            if(Yii::$app->controller->id !="sync"){
                $weldingData = \app\models\Welding::find()->where(['kp'=>$this->kp,'weld_number'=>$this->weld_number, 'has_been_cut_out' => 'No'])->active()->one();
                if(!empty($weldingData)){
                    // $weldingData->weld_sub_type ="WR";
                    $weldingData->weld_sub_type = $this->weld_sub_type;
                    $weldingData->save();
                }
                // $Ndt = \app\models\Ndt::find()->where(['kp'=>$this->kp,'weld_number'=>$this->weld_number])->orderBy('id DESC')->active()->one();
                // if(!empty($Ndt)){
                //     $Ndt->outcome ="Repaired";
                //     $Ndt->save();
                // }
            }
            if(is_array($this->electrodes)){
				$this->electrodes =  json_encode($this->electrodes);
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
