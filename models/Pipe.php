<?php
namespace app\models;
use Yii;
/**
 * @property int $id
 * @property string $pipe_number
 * @property double $wall_thikness
 * @property double $weight
 * @property double $heat_number
 * @property double $yeild_strength
 * @property string $length
 * @property double $od
 * @property string $coating_type
 * @property double $plate_number
 * @property double $ship_out_number
 * @property string $vessel
 * @property string $hfb
 * @property string $mto_number
 * @property string $mto_certificate
 * @property string $mill
 * @property string $comments
 * @property int $project_id
 * @property string $created_by
 * @property string $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $is_active
 *
 * @property TaxonomyValue $project
 * @property PipeBending[] $pipeBendings
 * @property PipeCuting[] $pipeCutings
 * @property PipeReception[] $pipeReceptions
 * @property PipeStringing[] $pipeStringings
 * @property PipeTransfer[] $pipeTransfers
 */
class Pipe extends \yii\db\ActiveRecord
{
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
    public static function tableName()
    {
        return 'pipe';
    }
    public function rules()
    {
        return [
            [['pipe_number'], 'required'],
            ['pipe_number', 'filter', 'filter'=>'trim'],
            [['wall_thikness', 'weight', 'heat_number', 'yeild_strength', 'length', 'od', 'plate_number', 'ship_out_number'], 'number','min'=>0],
            [['comments'], 'string'],
            [['project_id', 'created_at', 'updated_at','created_by', 'updated_by'], 'integer'],
            [['pipe_number', 'coating_type', 'vessel', 'hfb', 'mto_number', 'mto_certificate', 'mill'], 'string', 'max' => 255],            
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pipe_number' => Yii::$app->trans->getTrans('Pipe Number'),
            'wall_thikness' => Yii::$app->trans->getTrans('Wall Thickness'),
            'weight' => Yii::$app->trans->getTrans('Weight (tonnes)'),
            'heat_number' => Yii::$app->trans->getTrans('Heat Number'),
            'yeild_strength' => Yii::$app->trans->getTrans('Yield Strength'),
            'length' => Yii::$app->trans->getTrans('Length'),
            'od' => Yii::$app->trans->getTrans('OD (mm)'),
            'coating_type' => Yii::$app->trans->getTrans('Coating Type'),
            'plate_number' => Yii::$app->trans->getTrans('Plate Number'),
            'ship_out_number' => Yii::$app->trans->getTrans('Ship Out Number'),
            'vessel' => Yii::$app->trans->getTrans('Vessel'),
            'hfb' => Yii::$app->trans->getTrans('HFB'),
            'mto_number' => Yii::$app->trans->getTrans('MTO Number'),
            'mto_certificate' => Yii::$app->trans->getTrans('MTO Certificate'),
            'mill' => Yii::$app->trans->getTrans('Mill'),
            'comments' => Yii::$app->trans->getTrans('Comment'),
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'project_id' => 'Project ID',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public static function find()
    {
        return new PipeQuery(get_called_class());
    }
    public function beforeSave($insert){
        
		if (parent::beforeSave($insert)) {			
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
