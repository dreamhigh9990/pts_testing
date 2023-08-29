<?php
namespace app\models;
use Yii;
class Line extends \yii\db\ActiveRecord
{
   
    public static function tableName()
    {
        return 'admin_line_list';
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
            [['from_kp', 'from_geo_code', 'to_kp', 'to_geo_code'], 'required'],
            [['from_kp', 'to_kp'], 'number'],
            ['from_kp', "checkKpRange"],
            ['to_kp', "checkKpRange"],
            [['is_deleted', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['pipe_diameter', 'wall_thickness', 'depth_of_cover', 'coating_type', 'bend_location', 'road_crossing', 'river_crossing', 'foreign_service_crossing', 'fence_crossing', 'hdd_locations', 'backfill_material', 'marker_tape_location', 'comment'], 'safe'],
        ];
    }
    public function checkKpRange($attribute, $params)
    {
        if( $this->isNewRecord){
            if($this->from_kp >= $this->to_kp){
                $this->addError($attribute, 'from_kp is must be less than of to_kp');
            }

            // $Line = Line::find()->where(['AND',['<', 'from_kp', $this->$attribute],['>', 'to_kp', $this->$attribute]])->active()->one();
            // if(!empty($Line)){
            //     $this->addError($attribute, 'Invalide '.$attribute.' number, its already exist in some of range.');
            // }
        }
    }
    public function print_attributes()
    {
        return [          
            'pipe_diameter' => Yii::$app->trans->getTrans('Pipe Diameter'),
            'wall_thickness' => Yii::$app->trans->getTrans('Wall Thickness'),
            'depth_of_cover' => Yii::$app->trans->getTrans('Depth Of Cover'),
            'coating_type' => Yii::$app->trans->getTrans('Coating Type'),
            'bend_location' => Yii::$app->trans->getTrans('Bend Location'),
            'road_crossing' => Yii::$app->trans->getTrans('Road Crossing'),
            'river_crossing' => Yii::$app->trans->getTrans('River Crossing'),
            'foreign_service_crossing' => Yii::$app->trans->getTrans('Foreign Service Crossing'),
            'fence_crossing' => Yii::$app->trans->getTrans('Fence Crossing'),
            'hdd_locations' => Yii::$app->trans->getTrans('Hdd Locations'),
            'backfill_material' => Yii::$app->trans->getTrans('Backfill Material'),
            'marker_tape_location' => Yii::$app->trans->getTrans('Marker Tape Location'),
            'from_kp' => Yii::$app->trans->getTrans('From KP'),
            'from_geo_code' => Yii::$app->trans->getTrans('From Geo Code'),
            'to_kp' => Yii::$app->trans->getTrans('To KP'),
            'to_geo_code' => Yii::$app->trans->getTrans('To Geo Code'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pipe_diameter' => Yii::$app->trans->getTrans('Pipe Diameter'),
            'wall_thickness' => Yii::$app->trans->getTrans('Wall Thickness'),
            'depth_of_cover' => Yii::$app->trans->getTrans('Depth Of Cover'),
            'coating_type' => Yii::$app->trans->getTrans('Coating Type'),
            'bend_location' => Yii::$app->trans->getTrans('Bend Location'),
            'road_crossing' => Yii::$app->trans->getTrans('Road Crossing'),
            'river_crossing' => Yii::$app->trans->getTrans('River Crossing'),
            'foreign_service_crossing' => Yii::$app->trans->getTrans('Foreign Service Crossing'),
            'fence_crossing' => Yii::$app->trans->getTrans('Fence Crossing'),
            'hdd_locations' => Yii::$app->trans->getTrans('Hdd Locations'),
            'backfill_material' => Yii::$app->trans->getTrans('Backfill Material'),
            'marker_tape_location' => Yii::$app->trans->getTrans('Marker Tape Location'),
            'from_kp' => Yii::$app->trans->getTrans('From KP'),
            'from_geo_code' => Yii::$app->trans->getTrans('From Geo Code'),
            'to_kp' => Yii::$app->trans->getTrans('To KP'),
            'to_geo_code' => Yii::$app->trans->getTrans('To Geo Code'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'is_deleted' => Yii::$app->trans->getTrans('Is Deleted'),
            'created_by' => Yii::$app->trans->getTrans('Created By'),
            'updated_by' => Yii::$app->trans->getTrans('Updated By'),
            'created_at' => Yii::$app->trans->getTrans('Created At'),
            'updated_at' => Yii::$app->trans->getTrans('Updated At'),
        ];
    }
    public static function find()
    {
        return new LineQuery(get_called_class());
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
