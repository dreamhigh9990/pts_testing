<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "com_cathodic_protection".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property int $from_kp
 * @property int $to_kp
 * @property string $testpoint_type
 * @property string $pipe_potential1
 * @property string $pipe_potential2
 * @property string $zing_reference_potential
 * @property string $pipe1_to_zn
 * @property int $cp_posts
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
class Cathodicprotection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'com_cathodic_protection';
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
            [['date', 'report_number', 'from_kp', 'to_kp','from_weld','to_weld'], 'required'],
            [['date'], 'safe'],
            [['from_kp', 'to_kp','from_weld', 'to_weld'],'number'],
            [['cp_posts', 'project_id', 'qa_manager', 'created_by', 'updated_by', 'created_at', 'updated_at', 'is_deleted'], 'integer'],
            [['testpoint_type', 'comment', 'signed_off', 'is_anomally'], 'string'],
            [['report_number', 'why_anomally','pipe_potential1','pipe_potential2','zing_reference_potential','pipe1_to_zn'], 'string', 'max' => 255],
            // ['check_points', "checkCheckPoints"],
        ];
    }

    public function checkCheckPoints($attribute, $params) {
        $listItems = Yii::$app->general->TaxonomyDrop(18);

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
    public function print_attributes()
    {
        return [         
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'from_kp' => Yii::$app->trans->getTrans('From KP'),
            'to_kp' => Yii::$app->trans->getTrans('To KP'),
            'from_weld' => Yii::$app->trans->getTrans('From Weld'),
            'to_weld' => Yii::$app->trans->getTrans('To Weld'),
            'testpoint_type' => Yii::$app->trans->getTrans('Testpoint Type'),
            'pipe_potential1' => Yii::$app->trans->getTrans('Pipe Potential1'),
            'pipe_potential2' => Yii::$app->trans->getTrans('Pipe Potential2'),
            'zing_reference_potential' => Yii::$app->trans->getTrans('Zing Reference Potential'),
            'pipe1_to_zn' => Yii::$app->trans->getTrans('Pipe 1 To Zn Reference'),
            'cp_posts' => Yii::$app->trans->getTrans('Number of CP Posts'),
            'check_points' => Yii::$app->trans->getTrans('Check Points'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'from_kp' => Yii::$app->trans->getTrans('From KP'),
            'to_kp' => Yii::$app->trans->getTrans('To KP'),
            'from_weld' => Yii::$app->trans->getTrans('From Weld'),
            'to_weld' => Yii::$app->trans->getTrans('To Weld'),
            'testpoint_type' => Yii::$app->trans->getTrans('Testpoint Type'),
            'pipe_potential1' => Yii::$app->trans->getTrans('Pipe Potential1'),
            'pipe_potential2' => Yii::$app->trans->getTrans('Pipe Potential2'),
            'zing_reference_potential' => Yii::$app->trans->getTrans('Zing Reference Potential'),
            'pipe1_to_zn' => Yii::$app->trans->getTrans('Pipe 1 To Zn Reference'),
            'cp_posts' => Yii::$app->trans->getTrans('Number of CP Posts'),
            'check_points' => Yii::$app->trans->getTrans('Check Points'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
            'is_anomally' => 'Is Anomally',
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomally'),
        ];
    }

    /**
     * @inheritdoc
     * @return CathodicprotectionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CathodicprotectionQuery(get_called_class());
    }
    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {
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
