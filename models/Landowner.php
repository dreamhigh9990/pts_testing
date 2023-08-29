<?php
namespace app\models;
use Yii;
class Landowner extends \yii\db\ActiveRecord
{
    
    public static function tableName()
    {
        return 'admin_landowner';
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
            [['from_kp', 'to_kp'], 'required'],
            [['from_kp', 'to_kp'], 'number'],
            ['from_kp', "from_kp"],
            ['to_kp', "to_kp"],
            [['from_geo_let', 'from_geo_long','to_geo_let','to_geo_long','project_id'], 'safe'],            
            [['signed_off', 'comment'], 'string'],
            [['project_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['landholder', 'site_reference','foregin_service','fencing_details', 'gate_management', 'stock_impact', 'vegetation_impact', 'weed_hygiene', 'from_geo_code', 'to_geo_code'], 'string', 'max' => 255],
        ];
    }
    public function from_kp($attribute, $params)
    {
        if($this->from_kp >= $this->to_kp){
            $this->addError($attribute, 'To kp is must be grater than From kp');
        }
        // $Line = Line::find()->where(['from_kp' => $this->from_kp])->active()->one();
        // if(empty($Line)){
        //     $this->addError($attribute, 'Invalide from_kp  number, its out of the line range.');
        // }
        
    }
    public function to_kp($attribute, $params)
    {
        if($this->from_kp >= $this->to_kp){
            $this->addError($attribute, 'To kp is must be grater than From kp');
        }
        // $Line = Line::find()->where(['to_kp' => $this->to_kp])->active()->one();
        // if(empty($Line)){
        //     $this->addError($attribute, 'Invalide to_kp  number, its out of the line range.');
        // }
        
    }
    public function print_attributes()
    {
        return [          
            'landholder' => Yii::$app->trans->getTrans('Landholder'),
            'site_reference' => Yii::$app->trans->getTrans('Site Reference'),
            'fencing_details' => Yii::$app->trans->getTrans('Fencing Details'),
            'gate_management' => Yii::$app->trans->getTrans('Gate Management'),
            'stock_impact' => Yii::$app->trans->getTrans('Stock Impact'),
            'vegetation_impact' => Yii::$app->trans->getTrans('Vegetation Impact'),
            'foregin_service' => Yii::$app->trans->getTrans('Foregin Service'), 
            'weed_hygiene' => Yii::$app->trans->getTrans('Weed Hygiene'),
            'from_kp' => Yii::$app->trans->getTrans('From KP'),
            'from_geo_code' => Yii::$app->trans->getTrans('From Geo Code'),
            'to_kp' => Yii::$app->trans->getTrans('To KP'),
            'to_geo_code' => Yii::$app->trans->getTrans('To Geo Code'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'signed_off' => Yii::$app->trans->getTrans('Sign Offs'),
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->trans->getTrans('ID'),
            'landholder' => Yii::$app->trans->getTrans('Landholder'),
            'site_reference' => Yii::$app->trans->getTrans('Site Reference'),
            'fencing_details' => Yii::$app->trans->getTrans('Fencing Details'),
            'gate_management' => Yii::$app->trans->getTrans('Gate Management'),
            'stock_impact' => Yii::$app->trans->getTrans('Stock Impact'),
            'vegetation_impact' => Yii::$app->trans->getTrans('Vegetation Impact'),
            'foregin_service' => Yii::$app->trans->getTrans('Foregin Service'),
            'weed_hygiene' => Yii::$app->trans->getTrans('Weed Hygiene'),
            'signed_off' => Yii::$app->trans->getTrans('Sign Offs'),
            'from_kp' => Yii::$app->trans->getTrans('From KP'),
            'from_geo_code' => Yii::$app->trans->getTrans('From Geo Code'),
            'to_kp' => Yii::$app->trans->getTrans('To KP'),
            'to_geo_code' => Yii::$app->trans->getTrans('To Geo Code'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'project_id' => Yii::$app->trans->getTrans('Project'),
            'created_by' => Yii::$app->trans->getTrans('Created By'),
            'updated_by' => Yii::$app->trans->getTrans('Updated By'),
            'created_at' => Yii::$app->trans->getTrans('Created At'),
            'updated_at' => Yii::$app->trans->getTrans('Updated At'),
        ];
    }
    public static function find()
    {
        return new LandownerQuery(get_called_class());
    }
    public function beforeSave($insert){
		if (parent::beforeSave($insert)) {				
            $this->project_id = empty($this->project_id) ? Yii::$app->user->identity->project_id : $this->project_id;
            $mo = Yii::$app->general->setTimestamp($this);
            $this->created_at =  $mo->created_at;
            $this->updated_at  = $mo->updated_at;
            
            if(empty($this->from_geo_let)){
                $this->from_geo_let = 0;
            }
            if(empty($this->from_geo_long)){
                $this->from_geo_long = 0;
            }
            if(empty($this->to_geo_let)){
                $this->to_geo_let = 0;
            }
            if(empty($this->to_geo_long)){
                $this->to_geo_long = 0;
            }
            
        	return true;
		} else {
			return false;
		}
	}
}
