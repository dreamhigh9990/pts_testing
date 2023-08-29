<?php
namespace app\models;
use Yii;
use yii\base\Model;
class ClearanceReport extends Model
{
   public $from_kp;
   public $to_kp;
   public $from_weld;
   public $to_weld;
   
    public function rules()
    {
        return [
            [['from_kp', 'to_kp', 'from_weld', 'to_weld'],'required'],    
            [['from_kp', 'to_kp', 'from_weld', 'to_weld'],'number'],          
        ];
    }

    public function attributeLabels()
    {
        return [
            'from_kp' => Yii::$app->trans->getTrans('From KP'),
            'to_kp' => Yii::$app->trans->getTrans('To KP'),
            'from_weld' => Yii::$app->trans->getTrans('From Weld'),
            'to_weld' => Yii::$app->trans->getTrans('To Weld'),
        ];
    }
}
