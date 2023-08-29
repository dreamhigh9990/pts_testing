<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "civil_backfilling".
 *
 * @property int $id
 * @property string $date
 * @property int $report_number
 * @property int $from_kp
 * @property int $to_kp
 * @property int $from_weld
 * @property int $to_weld
 * @property string $backfilling_type
 * @property int $number_installed
 * @property string $check_points
 * @property string $cable_present
 * @property int $project_id
 * @property string $comment
 * @property int $qa_manager
 * @property string $signed_off
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $is_deleted
 * @property string $is_anomally
 * @property string $why_anomally
 */
class Backfilling extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'civil_backfilling';
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
            //     // if you're using datetime instead of UNIX timestamp:
            //     // 'value' => new Expression('NOW()'),
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
            // [['date', 'report_number', 'from_kp', 'to_kp', 'from_weld', 'to_weld', 'backfilling_type', 'check_points',], 'required'],
            [['date', 'report_number', 'from_kp', 'to_kp'], 'required'], //remove required validation from from_weld and to_weld as per client says
            [['date'], 'safe'],
            [['from_kp', 'to_kp', 'from_weld', 'to_weld'],'number'],
            [['number_installed', 'project_id', 'qa_manager', 'created_by', 'updated_by', 'created_at', 'updated_at', 'is_deleted'], 'integer'],
            [[ 'cable_present', 'comment', 'signed_off', 'is_anomally'], 'string'],
            [['why_anomally','report_number'], 'string', 'max' => 255],
            // ['check_points', "checkCheckPoints"],
            // ['backfilling_type', "checkBackfillingType"],
        ];
    }

    public function checkBackfillingType($attribute, $params) {
        $listItems = Yii::$app->general->TaxonomyDrop(14);

        if(!is_array($this->backfilling_type)){
            $this->backfilling_type = json_decode($this->backfilling_type,true);
        }

        if(count($this->backfilling_type) != count($listItems)){
            $this->addError('Backfilling Type', 'Please ensure all checkpoints are completed');
        }
    }
    
    public function checkCheckPoints($attribute, $params) {
        $listItems = Yii::$app->general->TaxonomyDrop(20);

        if(!is_array($this->check_points)){
            $this->check_points = json_decode($this->check_points,true);
        }

        if(count($this->check_points) != count($listItems)){
            $this->addError('Check Points', 'Please ensure all checkpoints are completed');
        }
    }

    /**
     * @inheritdoc
     */

    public function print_attributes(){
        return [
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'from_kp' => Yii::$app->trans->getTrans('From Chainage'),
            'to_kp' => Yii::$app->trans->getTrans('To Chainage'),
            // 'from_weld' => 'From Weld',
            // 'to_weld' => 'To Weld',
            'backfilling_type' => Yii::$app->trans->getTrans('Backfilling Type'),
            'number_installed' => Yii::$app->trans->getTrans('Number of Trench Breakers'),
            'check_points' => Yii::$app->trans->getTrans('Check Points'),
            'cable_present' => Yii::$app->trans->getTrans('Cable Present'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'created_by' => Yii::$app->trans->getTrans('User'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'from_kp' => Yii::$app->trans->getTrans('From Chainage'),
            'to_kp' => Yii::$app->trans->getTrans('To Chainage'),
            // 'from_weld' => 'From Weld',
            // 'to_weld' => 'To Weld',
            'backfilling_type' => Yii::$app->trans->getTrans('Backfilling Type'),
            'number_installed' => Yii::$app->trans->getTrans('Number of Trench Breakers'),
            'check_points' => Yii::$app->trans->getTrans('Check Points'),
            'cable_present' => Yii::$app->trans->getTrans('Cable Present'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
            'is_anomally' => 'Is Anomally',
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
        ];
    }

    /**
     * @inheritdoc
     * @return BackfillingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BackfillingQuery(get_called_class());
    }
    public function checkAnomaly(){
        if($this->isNewRecord){
            if($this->from_weld != ""){
                $fromweldlist =  Welding::find()->where(['weld_number'=>$this->from_weld])->active()->one();
                if(empty($fromweldlist)){
                    $this->is_anomally  = "Yes";	
                    $this->why_anomally = "This from weld number was not exist in welding";	
                }
            }
            if($this->to_weld != ""){
                $toweldlist =  Welding::find()->where(['weld_number'=>$this->to_weld])->active()->one();
                if(empty($toweldlist)){
                    $this->is_anomally  = "Yes";	
                    $this->why_anomally = "This to weld number was not exist in welding";	
                }
            }
        }
		return;
	}
    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {
            $this->checkAnomaly();
            if(Yii::$app->controller->id != "sync"){
                // check points
                if(!empty($this->check_points) && is_array($this->check_points)){
                    $this->check_points = json_encode($this->check_points);
                } else {
                    $this->check_points = '';
                }

                // backfilling types
                if(!empty($this->backfilling_type) && is_array($this->backfilling_type)){
                    $this->backfilling_type = json_encode($this->backfilling_type);
                } else {
                    $this->backfilling_type = '';
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
