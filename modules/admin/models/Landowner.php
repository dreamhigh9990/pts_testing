<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "landowner".
 *
 * @property int $id
 * @property string $landholder
 * @property string $site_reference
 * @property string $fencing_details
 * @property string $gate_management
 * @property string $stock_impact
 * @property string $vegetation_impact
 * @property string $weed_hygiene
 * @property string $sign_offs
 * @property double $from_kp
 * @property string $from_geo_code
 * @property double $to_kp
 * @property string $form_geo_code
 * @property string $comment
 * @property int $created_by
 * @property int $project_id
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class Landowner extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'landowner';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['landholder', 'from_kp', 'to_kp','project_id'], 'required'],
            [['sign_offs', 'comment'], 'string'],
            [['from_kp', 'to_kp'], 'number'],
            [['created_by', 'updated_by', 'created_at', 'updated_at','project_id'], 'integer'],
            [['landholder', 'site_reference', 'fencing_details', 'gate_management', 'stock_impact', 'vegetation_impact', 'weed_hygiene', 'from_geo_code', 'form_geo_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'landholder' => 'Landholder',
            'site_reference' => 'Site Reference',
            'fencing_details' => 'Fencing Details',
            'gate_management' => 'Gate Management',
            'stock_impact' => 'Stock Impact',
            'vegetation_impact' => 'Vegetation Impact',
            'weed_hygiene' => 'Weed Hygiene',
            'sign_offs' => 'Sign Offs',
            'from_kp' => 'From Kp',
            'from_geo_code' => 'From Geo Code',
            'to_kp' => 'To Kp',
            'form_geo_code' => 'Form Geo Code',
            'comment' => 'Comment',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
			
        ];
    }
	public function beforeSave($insert){
		if (parent::beforeSave($insert)) {
			if(!empty(Yii::$app->user->identity->auth_key)){
				Yii::$app->general->validProjectId($this->project_id);
				if ($this->isNewRecord){	
					$this->created_at = time();
					$this->updated_at = time();
					$this->updated_at = time();
					$this->created_by = Yii::$app->user->identity->id;
					$this->updated_by = Yii::$app->user->identity->id;
				}else{			
					$this->updated_at = time();
					$this->updated_by = Yii::$app->user->identity->id;
				}
				return true;
			}else{				
				throw new \yii\web\NotFoundHttpException('Invalid or missing access token');
			}
		}else{
			return false;
		}
	}
}
