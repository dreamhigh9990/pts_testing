<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "welding".
 *
 * @property int $id
 * @property string $date
 * @property string $report_number
 * @property int $project_id
 * @property string $line_type
 * @property double $kp
 * @property string $pipe_number
 * @property string $next_pipe
 * @property string $geo_location
 * @property string $weld_type
 * @property string $weld_crossing
 * @property string $weld_number
 * @property string $weld_sub_type
 * @property string $WPS
 * @property string $electrodes
 * @property string $root_os
 * @property string $root_ts
 * @property string $hot_os
 * @property string $hot_ts
 * @property string $fill_os
 * @property string $fill_ts
 * @property string $cap_os
 * @property string $cap_ts
 * @property string $visual_acceptance
 * @property string $comment
 * @property string $signed_off
 * @property int $qa_manager
 * @property string $is_anomally
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class Welding extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'welding';
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
            [['date', 'kp', 'pipe_number', 'next_pipe', 'weld_type', 'weld_number', 'WPS', 'visual_acceptance'], 'required'],
            [['date', 'electrodes', 'weld_crossing', 'sequence'], 'safe'],
            [['project_id', 'qa_manager', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['line_type', 'visual_acceptance', 'signed_off', 'is_anomally', 'has_been_cut_out'], 'string'],
            [['kp','weld_number'], 'number'],
            ['next_pipe', 'compare', 'compareAttribute' => 'pipe_number', 'operator' => '!=', 'enableClientValidation' => true],
            // ['weld_crossing', 'required', 'when' => function ($model) {
            //     return $model->weld_type != 'W';
            // }],
            [['report_number', 'pipe_number', 'next_pipe', 'geo_location', 'weld_type', 'weld_number', 'weld_sub_type', 'WPS', 'root_os', 'root_ts', 'hot_os', 'hot_ts', 'fill_os', 'fill_ts', 'cap_os', 'cap_ts', 'comment','why_anomally'], 'string', 'max' => 255],
            ['geo_location', 'validateCoordinate'],
        ];
    }

    public function validateCoordinate($attribute, $params){
        $latlong = $this->$attribute;
        $result = Yii::$app->general->validGeo($latlong);
        if(!$result){
            $this->addError('Geo Location', 'Please enter a valid Geo Location.');
        }
    }

    public function print_attributes(){
        return [          
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'line_type' => Yii::$app->trans->getTrans('Join Type'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'weld_number' => Yii::$app->trans->getTrans('Weld Number'),
            'weld_type' => Yii::$app->trans->getTrans('Weld Type'),
            'weld_crossing' => Yii::$app->trans->getTrans('Weld Crossing'),
            'weld_sub_type' => Yii::$app->trans->getTrans('Weld Sub Type'),
            'pipe_number' => Yii::$app->trans->getTrans('Pipe Number'),
            'next_pipe' => Yii::$app->trans->getTrans('Next Pipe Number'),
            'geo_location' => Yii::$app->trans->getTrans('Geo Location'),
            'WPS' => Yii::$app->trans->getTrans('Wps'),
            'electrodes' => Yii::$app->trans->getTrans('Electrodes'),
            'root_os' => Yii::$app->trans->getTrans('Root Os'),
            'root_ts' => Yii::$app->trans->getTrans('Root Ts'),
            'hot_os' => Yii::$app->trans->getTrans('Hot Os'),
            'hot_ts' => Yii::$app->trans->getTrans('Hot Ts'),
            'fill_os' => Yii::$app->trans->getTrans('Fill Os'),
            'fill_ts' => Yii::$app->trans->getTrans('Fill Ts'),
            'cap_os' => Yii::$app->trans->getTrans('Cap Os'),
            'cap_ts' => Yii::$app->trans->getTrans('Cap Ts'),
            'visual_acceptance' => Yii::$app->trans->getTrans('Visual Acceptance'),
            'has_been_cut_out' => Yii::$app->trans->getTrans('Has been cut out?'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => Yii::$app->trans->getTrans('Date'),
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'line_type' => Yii::$app->trans->getTrans('Join Type'),
            'kp' => Yii::$app->trans->getTrans('KP'),
            'pipe_number' => Yii::$app->trans->getTrans('Pipe Number'),
            'next_pipe' => Yii::$app->trans->getTrans('Next Pipe Number'),
            'geo_location' => Yii::$app->trans->getTrans('Geo Location'),
            'weld_type' => Yii::$app->trans->getTrans('Weld Type'),
            'weld_crossing' => Yii::$app->trans->getTrans('Weld Crossing'),
            'weld_number' => Yii::$app->trans->getTrans('Weld Number'),
            'weld_sub_type' => Yii::$app->trans->getTrans('Weld Sub Type'),
            'WPS' => Yii::$app->trans->getTrans('Wps'),
            'electrodes' => Yii::$app->trans->getTrans('Electrodes'),
            'root_os' => Yii::$app->trans->getTrans('Root OS'),
            'root_ts' => Yii::$app->trans->getTrans('Root TS'),
            'hot_os' => Yii::$app->trans->getTrans('Hot OS'),
            'hot_ts' => Yii::$app->trans->getTrans('Hot TS'),
            'fill_os' => Yii::$app->trans->getTrans('Fill OS'),
            'fill_ts' => Yii::$app->trans->getTrans('Fill TS'),
            'cap_os' => Yii::$app->trans->getTrans('Cap OS'),
            'cap_ts' => Yii::$app->trans->getTrans('Cap TS'),
            'visual_acceptance' => Yii::$app->trans->getTrans('Visual Acceptance'),
            'has_been_cut_out' => Yii::$app->trans->getTrans('Has been cut out').'?',
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'is_anomally' => 'Is Anomally',
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
    public static function find()
    {
        return new WeldingQuery(get_called_class());
    }
    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {         

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
