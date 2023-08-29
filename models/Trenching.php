<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "civil_trenching".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property int $from_kp
 * @property int $to_kp
 * @property int $from_weld
 * @property int $to_weld
 * @property int $width
 * @property int $depth
 * @property string $comment
 * @property int $qa_manager
 * @property string $signed_off
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at 
 * @property string $pre_start
 * @property string $during_trenching
 * @property int $project_id
 * @property string $is_anomally
 * @property string $why_anomally
 */
class Trenching extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'civil_trenching';
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
            // [['date', 'report_number', 'from_kp', 'to_kp', 'from_weld', 'to_weld', 'width', 'depth','pre_start','during_trenching'], 'required'],
            [['date', 'report_number', 'from_kp', 'to_kp', 'width', 'depth'], 'required'], //remove required validation from from_weld and to_weld as per client says
            [['date'], 'safe'],
            [['from_kp','to_kp','from_weld','to_weld','width','depth'],'number'],
            [['qa_manager', 'created_by', 'updated_by', 'created_at', 'updated_at','project_id'], 'integer'],
            [['comment', 'signed_off','is_anomally'], 'string'],
            [['report_number','why_anomally'], 'string', 'max' => 255],
            // ['pre_start', "checkPreStart"],
            // ['during_trenching', "checkDuringTrenching"],
        ];
    }

    public function checkPreStart($attribute, $params) {
        $listItems = Yii::$app->general->TaxonomyDrop(16);

        if(!is_array($this->pre_start)){
            $this->pre_start = json_decode($this->pre_start,true);
        }

        if(count($this->pre_start) != count($listItems)){
            $this->addError('Pre Start', 'Please ensure all checkpoints are completed');
        }
    }

    public function checkDuringTrenching($attribute, $params) {
        $listItems = Yii::$app->general->TaxonomyDrop(17);

        if(!is_array($this->during_trenching)){
            $this->during_trenching = json_decode($this->during_trenching,true);
        }

        if(count($this->during_trenching) != count($listItems)){
            $this->addError('During Trenching', 'Please ensure all checkpoints are completed');
        }
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
            'width' => Yii::$app->trans->getTrans('Width').' (mm)',
            'depth' => Yii::$app->trans->getTrans('Depth').' (mm)',
            'pre_start' => Yii::$app->trans->getTrans('Pre Start'),
            'during_trenching' => Yii::$app->trans->getTrans('During Trenching'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function print_attributes()
    {
        return [
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'from_kp' => Yii::$app->trans->getTrans('From Chainage'),
            'to_kp' => Yii::$app->trans->getTrans('To Chainage'),
            // 'from_weld' => 'From Weld',
            // 'to_weld' => 'To Weld',
            'width' => Yii::$app->trans->getTrans('Width').' (mm)',
            'depth' => Yii::$app->trans->getTrans('Depth').' (mm)',
            'pre_start' => Yii::$app->trans->getTrans('Pre Start'),
            'during_trenching' => Yii::$app->trans->getTrans('During Trenching'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'created_by' => Yii::$app->trans->getTrans('User'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off')
        ];
    }
    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {
            if(Yii::$app->controller->id != "sync"){
                // pre start
                if(!empty($this->pre_start) && is_array($this->pre_start)){
                    $this->pre_start = json_encode($this->pre_start);
                } else {
                    $this->pre_start = '';
                }

                // during trenching
                if(!empty($this->during_trenching) && is_array($this->during_trenching)){
                    $this->during_trenching = json_encode($this->during_trenching);
                } else {
                    $this->during_trenching = '';
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

    /**
     * @inheritdoc
     * @return TrenchingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TrenchingQuery(get_called_class());
    }
}
