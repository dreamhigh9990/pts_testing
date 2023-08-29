<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "civil_reinstatement".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property int $from_kp
 * @property int $to_kp
 * @property int $from_weld
 * @property int $to_weld
 * @property int $length
 * @property string $check_points
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
class Reinstatement extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'civil_reinstatement';
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
            // [['date', 'report_number', 'from_kp', 'to_kp', 'from_weld', 'to_weld', 'check_points'], 'required'],
            [['date', 'report_number', 'from_kp', 'to_kp'], 'required'], //remove required validation from from_weld and to_weld as per client says
            [['from_kp', 'to_kp', 'from_weld', 'to_weld','length', 'qty_markers_installed'],'number'],
            [['date'], 'safe'],
            [['project_id', 'qa_manager', 'created_by', 'updated_by', 'created_at', 'updated_at', 'is_deleted'], 'integer'],
            [[ 'comment', 'signed_off', 'is_anomally'], 'string'],
            [['report_number', 'why_anomally'], 'string', 'max' => 255],
            // ['check_points', "checkCheckPoints"],
        ];
    }

    public function checkCheckPoints($attribute, $params) {
        $listItems = Yii::$app->general->TaxonomyDrop(19);

        if(!is_array($this->check_points)){
            $this->check_points = json_decode($this->check_points,true);
        }

        if(count($this->check_points) != count($listItems)){
            $this->addError('Check Points', 'Please ensure all checkpoints are completed');
        }
    }

    public function print_attributes(){
        return [
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'from_kp' => Yii::$app->trans->getTrans('From Chainage'),
            'to_kp' => Yii::$app->trans->getTrans('To Chainage'),
            // 'from_weld' => 'From Weld',
            // 'to_weld' => 'To Weld',
            'length' => Yii::$app->trans->getTrans('Length').' (m)',
            'check_points' => Yii::$app->trans->getTrans('Check Points'),
            'qty_markers_installed' => Yii::$app->trans->getTrans('Qty of markers installed'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'created_by' => Yii::$app->trans->getTrans('User'),
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
            'from_kp' => Yii::$app->trans->getTrans('From Chainage'),
            'to_kp' => Yii::$app->trans->getTrans('To Chainage'),
            // 'from_weld' => 'From Weld',
            // 'to_weld' => 'To Weld',
            'length' => Yii::$app->trans->getTrans('Length').' (m)',
            'check_points' => Yii::$app->trans->getTrans('Check Points'),
            'qty_markers_installed' => Yii::$app->trans->getTrans('Qty of markers installed'),
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
     * @return ReinstatementQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ReinstatementQuery(get_called_class());
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
                if(!empty($this->check_points) && is_array($this->check_points)){
                    $this->check_points = json_encode($this->check_points);
                } else {
                    $this->check_points = '';
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
