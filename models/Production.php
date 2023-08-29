<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "welding_coating_production".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property int $project_id
 * @property double $kp
 * @property string $weld_number
 * @property double $dew_point
 * @property double $temperature
 * @property string $abrasive_material
 * @property string $material_batch_number
 * @property double $surface_profile
 * @property string $batch_number_a
 * @property string $batch_number_b
 * @property string $steel_adhesion
 * @property string $fbe_adhesion
 * @property double $dft
 * @property string $checkpoint
 * @property string $outcome
 * @property string $comment
 * @property string $signed_off
 * @property int $qa_manager
 * @property string $is_anomally
 * @property string $why_anomally
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 */
class Production extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'welding_coating_production';
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
            // [['date', 'report_number', 'kp', 'weld_number', 'checkpoint','outcome'], 'required'],
            [['date', 'report_number', 'kp', 'weld_number','outcome', 'main_weld_id', 'dew_point', 'temperature', 'substrate_temprature', 'humidity'], 'required'],
            [['date', 'checkpoint','outcome'], 'safe'],
            [['project_id', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['kp', 'dew_point', 'temperature', 'substrate_temprature', 'humidity', 'surface_profile', 'dft', 'dft_2', 'dft_3', 'dft_4', 'dft_5', 'dft_6', 'salt_testing'], 'number'],
            [['abrasive_material', 'comment', 'signed_off', 'is_anomally', 'batch_number_a', 'batch_number_b'], 'string'],
            [['report_number', 'weld_number', 'material_batch_number', 'steel_adhesion', 'fbe_adhesion', 'why_anomally'], 'string', 'max' => 255],
            // ['checkpoint', "checkCheckPoints"],
        ];
    }

    public function checkCheckPoints($attribute, $params) {
        if(Yii::$app->controller->id !="sync"){
            $listItems = Yii::$app->general->TaxonomyDrop(23);

            if(!is_array($this->checkpoint)){
                $this->checkpoint = json_decode($this->checkpoint,true);
            }

            if(count($this->checkpoint) != count($listItems)){
                $this->addError('Check Points', 'Please ensure all checkpoints are completed');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function print_attributes()
    {
        return [          
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'weld_number' => Yii::$app->trans->getTrans('Weld Number'),
            'temperature' => Yii::$app->trans->getTrans('Ambient Temperature'),
            'substrate_temprature' => Yii::$app->trans->getTrans('Substrate Temprature'),
            'humidity' => Yii::$app->trans->getTrans('Humidity'),
            'dew_point' => Yii::$app->trans->getTrans('Dew Point'),
            'abrasive_material' => Yii::$app->trans->getTrans('Applicator'),
            'material_batch_number' => Yii::$app->trans->getTrans('Material Batch number'),
            'surface_profile' => Yii::$app->trans->getTrans('Surface Profile'),
            'batch_number_a' => Yii::$app->trans->getTrans('Batch Number').' A',
            'batch_number_b' => Yii::$app->trans->getTrans('Batch Number').' B',
            'steel_adhesion' => Yii::$app->trans->getTrans('Steel Adhesion'),
            'fbe_adhesion' => Yii::$app->trans->getTrans('FBE Adhesion'),
            'weld_type' => Yii::$app->trans->getTrans('Weld Type'),
            'weld_sub_type' => Yii::$app->trans->getTrans('Weld Sub Type'),
            'dft' => Yii::$app->trans->getTrans('DFT').' 1',
            'dft_2' => Yii::$app->trans->getTrans('DFT').' 2',
            'dft_3' => Yii::$app->trans->getTrans('DFT').' 3',
            'dft_4' => Yii::$app->trans->getTrans('DFT').' 4',
            'dft_5' => Yii::$app->trans->getTrans('DFT').' 5',
            'dft_6' => Yii::$app->trans->getTrans('DFT').' 6',
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'outcome' => Yii::$app->trans->getTrans('Outcome')
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'weld_number' => Yii::$app->trans->getTrans('Weld Number'),
            'temperature' => Yii::$app->trans->getTrans('Ambient Temperature'),
            'substrate_temprature' => Yii::$app->trans->getTrans('Substrate Temprature'),
            'humidity' => Yii::$app->trans->getTrans('Humidity'),
            'dew_point' => Yii::$app->trans->getTrans('Dew Point'),
            'abrasive_material' => Yii::$app->trans->getTrans('Applicator'),
            'material_batch_number' => Yii::$app->trans->getTrans('Material Batch number'),
            'surface_profile' => Yii::$app->trans->getTrans('Surface Profile'),
            'batch_number_a' => Yii::$app->trans->getTrans('Batch Number').' A',
            'batch_number_b' => Yii::$app->trans->getTrans('Batch Number').' B',
            'steel_adhesion' => Yii::$app->trans->getTrans('Steel Adhesion'),
            'fbe_adhesion' => Yii::$app->trans->getTrans('FBE Adhesion'),
            'dft' => Yii::$app->trans->getTrans('DFT').' 1',
            'dft_2' => Yii::$app->trans->getTrans('DFT').' 2',
            'dft_3' => Yii::$app->trans->getTrans('DFT').' 3',
            'dft_4' => Yii::$app->trans->getTrans('DFT').' 4',
            'dft_5' => Yii::$app->trans->getTrans('DFT').' 5',
            'dft_6' => Yii::$app->trans->getTrans('DFT').' 6',
            'checkpoint' => Yii::$app->trans->getTrans('Check Points'),
            'salt_testing' => Yii::$app->trans->getTrans('Salt Testing'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'is_anomally' => 'Is Anomally',
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'is_deleted' => 'Is Deleted',
            'outcome' => Yii::$app->trans->getTrans('Outcome')
        ];
    }

    /**
     * @inheritdoc
     * @return ProductionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductionQuery(get_called_class());
    }

    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {	
            if(Yii::$app->controller->id != "sync"){
                if(!empty($this->checkpoint) && is_array($this->checkpoint)){
                    $this->checkpoint = json_encode($this->checkpoint);
                } else {
                    $this->checkpoint = '';
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
