<?php
namespace app\models;

use Yii;
use yii\base\Model;

class CsvImport extends Model{
    public $file;
    
    public function rules(){
        return [
			['project_id','required'],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'csv'],
        ];
    }
    
    public function attributeLabels(){
        return [
            'file'=>'Select File',
        ];
    }
}
?>