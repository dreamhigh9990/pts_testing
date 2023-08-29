<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pipe_cuting".
 *
 * @property int $id
 * @property string $report_number
 * @property string $pipe_number
 * @property int $pipe_id
 * @property string $defacts
 * @property double $length_1
 * @property double $length_2
 * @property string $retain_pipe_number
 * @property double $kp
 * @property int $comment
 * @property int $qa_manager
 * @property string $signed_off
 * @property string $date
 * @property int $project_id
 * @property string $is_anomally
 * @property string $why_anomally
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class Cutting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $length;
    public $wall_thickness;
    // public $new_pipe_1;
    // public $new_pipe_2;
    public static function tableName()
    {
        return 'pipe_cuting';
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
            // [['report_number', 'pipe_number', 'length_1', 'length_2', 'retain_pipe_number','date'], 'required'], // remove retain_pipe_number as per client say
            [['report_number', 'pipe_number', 'length_1', 'length_2','date'], 'required'],   
            [['length_1','length_2'],'number','min'=>0],           
            ['length_1', 'required', 'when' => function ($model) {
                return true;
            }, 'whenClient' => "function (attribute, value) {
                if($('#cutting-length').val() > 0){
                 return $('#cutting-length_2').val($('#cutting-length').val()-$('#cutting-length_1').val()); 
                }          
               
            }"],
            ['length_2', 'required', 'when' => function ($model) {
                return true;
            }, 'whenClient' => "function (attribute, value) {
                if($('#cutting-length').val() > 0){
                 return $('#cutting-length_1').val($('#cutting-length').val()-$('#cutting-length_2').val()); 
                }
               
            }"],
            [['pipe_id','qa_manager', 'project_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['length_1', 'length_2'], 'number'],
            [['signed_off', 'is_anomally','comment'], 'string'],
            [['date'], 'safe'],
            [['report_number', 'pipe_number', 'retain_pipe_number', 'why_anomally', 'new_pipe_1', 'new_pipe_2'], 'string', 'max' => 255],
        ];
    }
    public function print_attributes(){
        return [
            'date' => Yii::$app->trans->getTrans('Date'), 
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'pipe_number' => Yii::$app->trans->getTrans('Pipe Number'),
            'wall_thickness' => Yii::$app->trans->getTrans('Wall Thickness'),
            'length_1' => Yii::$app->trans->getTrans('Length').' 1',
            'length_2' => Yii::$app->trans->getTrans('Length').' 2',
            'retain_pipe_number' => Yii::$app->trans->getTrans('Retain Pipe Number'),
            'comment' => Yii::$app->trans->getTrans('Comment'),          
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_number' => Yii::$app->trans->getTrans('Report Number'),
            'pipe_number' => Yii::$app->trans->getTrans('Pipe Number'),
            'new_pipe_1' => Yii::$app->trans->getTrans('Pipe').' 1',
            'new_pipe_2' => Yii::$app->trans->getTrans('Pipe').' 2',
            'length_1' => Yii::$app->trans->getTrans('Length').' 1',
            'length_2' => Yii::$app->trans->getTrans('Length').' 2',
            'retain_pipe_number' => Yii::$app->trans->getTrans('Retain Pipe Number'),
            'comment' => Yii::$app->trans->getTrans('Comment'),
            'qa_manager' => Yii::$app->trans->getTrans('QA Manager'),
            'signed_off' => Yii::$app->trans->getTrans('Signed Off'),
            'date' => Yii::$app->trans->getTrans('Date'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'is_anomally' => 'Is Anomally',
            'why_anomally' => Yii::$app->trans->getTrans('Why Anomaly'),
            'created_by' => 'Created By',
            'updated_by' => 'Update By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return CuttingQuery the active query used by this AR class.
     */  
    public static function find()
    {
        return new CuttingQuery(get_called_class());
    }
    
    public function updatePipeNumber(){
        $motherPipeNumber = $this->pipe_number;
        $pipe1 = $motherPipeNumber;
        $pipe2 = '';
        if(strpos($motherPipeNumber, '/') !== false){
            $pipeExpld = explode('/', $motherPipeNumber);
            if(!empty($pipeExpld)){
                $mainPipeNumber = reset($pipeExpld);
                $currentSub = end($pipeExpld);
                // check current sub
                $refPipeToCheckSub = $mainPipeNumber.'/';
                $getPipeData = $this->getPipeRefData($refPipeToCheckSub);
                if(!empty($getPipeData)){
                    $refPipeNum = $getPipeData->pipe_number;
                    $refPipeNumExpld = explode('/', $refPipeNum);
                    if(!empty($refPipeNumExpld)){
                        $refPipeCurrentSub = end($refPipeNumExpld);
                        $pipe2 = $mainPipeNumber.'/'.($refPipeCurrentSub+1);
                    } else {
                        $pipe2 = $mainPipeNumber.'/'.($currentSub+1);
                    }
                } else {
                    $pipe2 = $mainPipeNumber.'/'.($currentSub+1);
                }
            }
        } else {
            $refPipeToCheckSub = $motherPipeNumber.'/';
            $getPipeData = $this->getPipeRefData($refPipeToCheckSub);
            if(!empty($getPipeData)){
                $refPipeNum = $getPipeData->pipe_number;
                $refPipeNumExpld = explode('/', $refPipeNum);
                if(!empty($refPipeNumExpld)){
                    $refPipeCurrentSub = end($refPipeNumExpld);
                    $pipe2 = $motherPipeNumber.'/'.($refPipeCurrentSub+1);
                } else {
                    $pipe2 = $motherPipeNumber.'/1';
                }
            } else {
                $pipe2 = $motherPipeNumber.'/1';
            }
        }
        
        $pipeData = \app\models\Pipe::find()->where(['pipe_number' => $motherPipeNumber])->active()->one();
        //###################### Pipe ###########################
        if(!empty($pipeData)){
            $classes = [
                '\app\models\Reception',
                '\app\models\PipeTransfer',
                '\app\models\Bending',
                '\app\models\Stringing',
                '\app\models\Welding'
            ];

            foreach($classes as $className){
                if($className != '\app\models\Bending'){
                    $model = $className::find()->where(['pipe_number' => $motherPipeNumber])->active()->one();
                    if(!empty($model)){
                        $model->pipe_number = $pipe1;
                        $model->save();

                        // make a receipt for a new pipe, if mother pipe has already been receipted
                        if($className == '\app\models\Reception'){
                            $newReceptionPipe = $model;
                            $newReceptionPipe->pipe_number = $pipe2;
                            Yii::$app->general->cloneModel($className, $newReceptionPipe);
                        }
                    }
                } else {
                    $bendigList = \app\models\Bending::find()->where(['pipe_number' => $motherPipeNumber])->active()->all();
                    if(!empty($bendigList)){
                        foreach($bendigList as $ele){
                            $model = \app\models\Bending::find()->where(['id'=>$ele->id])->active()->one();
                            if(!empty($model)){
                                $model->pipe_number = $pipe1;
                                $model->save();
                            }
                        }
                    }
                }
            }

            $pipeData->pipe_number = $pipe1;
            $pipeData->length = $this->length_1;
            $pipeData->pups = 1;
            $pipeData->save();

            $modelTemp =  $pipeData;
            $modelTemp->pipe_number = $pipe2;
            $modelTemp->length = $this->length_2;
            $modelTemp->pups = 1;
            $modelTemp = Yii::$app->anomaly->pipe_anomaly($modelTemp);
            Yii::$app->general->cloneModel("\app\models\Pipe", $modelTemp);
        } else {
            $newPipe = new \app\models\Pipe();
            $newPipe->pipe_number = $pipe1;
            $newPipe->length = $this->length_1;
            $newPipe->pups = 1;
            $newPipe->is_anomally = "Yes";
            $newPipe->is_active = 0;
            $newPipe->why_anomally = "Parent Pipe is not in pipe list.";
            $newPipe = Yii::$app->anomaly->pipe_anomaly($newPipe);
            $newPipe->save();

            $newPipe = new \app\models\Pipe();
            $newPipe->pipe_number = $pipe2;
            $newPipe->length = $this->length_2;
            $newPipe->pups = 1;
            $newPipe->is_anomally = "Yes";
            $newPipe->is_active = 0;
            $newPipe->why_anomally = "Parent Pipe is not in pipe list.";
            $newPipe = Yii::$app->anomaly->pipe_anomaly($newPipe);
            $newPipe->save();
        }

        return array(
            'pipe_1' => $pipe1,
            'pipe_2' => $pipe2
        );
    }

    public static function getPipeRefData($pipenum){
        $getPipeData = \app\models\Pipe::find()->select(['id', 'pipe_number'])->where(['LIKE', 'pipe_number', $pipenum])->active()->orderBy('id DESC')->one();

        return $getPipeData;
    }

    public function UpdatePipeNumber1(){
        $PipeNumber = $this->pipe_number;
        $ReturnPipeNumber = $this->pipe_number.'/'.$this->retain_pipe_number;
        $Pipe = \app\models\Pipe::find()->where(['pipe_number' => $PipeNumber])->active()->one();
        //###################### Pipe ###########################
        if(!empty($Pipe)){
            $Classes = ['\app\models\Reception', '\app\models\PipeTransfer', '\app\models\Bending', '\app\models\Stringing', '\app\models\Welding'];
            foreach($Classes as $ClassName){
                if($ClassName != '\app\models\Bending'){
                    $model = $ClassName::find()->where(['pipe_number' => $PipeNumber])->active()->one();
                    if(!empty($model)){
                        $model->pipe_number = $ReturnPipeNumber;
                        $model->save();
                    }
                }else{
                    $BendigList = \app\models\Bending::find()->where(['pipe_number' => $PipeNumber])->active()->all();
                    if(!empty($BendigList)){
                        foreach($BendigList as $ele){
                            $model = \app\models\Bending::find()->where(['id'=>$ele->id])->active()->one();
                            if(!empty($model)){
                                $model->pipe_number = $ReturnPipeNumber;
                                $model->save();
                            }
                        }
                    }
                }
            }

            $Pipe->pipe_number =  $ReturnPipeNumber;
            $Pipe->length      =  $this->retain_pipe_number == 1 ? $this->length_1 : $this->length_2;
            $Pipe->pups        =  1;
            $Pipe->save();

            if($this->retain_pipe_number == 1){
                $length      =  $this->length_2;
                $pipe_number =  $PipeNumber.'/2';
            }else{
                $length      =  $this->length_1;
                $pipe_number =  $PipeNumber.'/1';
            }

            $modelTemp =  $Pipe;
            $modelTemp->pipe_number  = $pipe_number;
            $modelTemp->length       = $length;
            $modelTemp->pups         = 1;
            $modelTemp = Yii::$app->anomaly->pipe_anomaly($modelTemp);
            Yii::$app->general->cloneModel("\app\models\Pipe",$modelTemp);
        } else {
            $Pipe = new \app\models\Pipe();
            $Pipe->pipe_number = $PipeNumber.'/1';
            $Pipe->length = $this->length_1;
            $Pipe->pups = 1;
            $Pipe->is_anomally = "Yes";
            $Pipe->is_active = 0;
            $Pipe->why_anomally = "Parent Pipe is not in pipe list.";
            $Pipe = Yii::$app->anomaly->pipe_anomaly($Pipe);
            $Pipe->save();

            $Pipe = new \app\models\Pipe();
            $Pipe->pipe_number = $PipeNumber.'/2';
            $Pipe->length = $this->length_2;
            $Pipe->pups = 1;
            $Pipe->is_anomally = "Yes";
            $Pipe->is_active = 0;
            $Pipe->why_anomally = "Parent Pipe is not in pipe list.";
            $Pipe = Yii::$app->anomaly->pipe_anomaly($Pipe);
            $Pipe->save();
        }
    }

    public static function pipeNumbersAfterCut($motherPipeNumber){
        $pipe1 = $motherPipeNumber;
        $pipe = '';
        if(strpos($motherPipeNumber, '/') !== false){
            $pipeExpld = explode('/', $motherPipeNumber);
            if(!empty($pipeExpld)){
                $mainPipeNumber = reset($pipeExpld);
                $currentSub = end($pipeExpld);
                // check current sub
                $refPipeToCheckSub = $mainPipeNumber.'/';
                $getPipeData = Cutting::getPipeRefData($refPipeToCheckSub);
                if(!empty($getPipeData)){
                    $refPipeNum = $getPipeData->pipe_number;
                    $refPipeNumExpld = explode('/', $refPipeNum);
                    if(!empty($refPipeNumExpld)){
                        $refPipeCurrentSub = end($refPipeNumExpld);
                        $pipe2 = $mainPipeNumber.'/'.($refPipeCurrentSub+1);
                    } else {
                        $pipe2 = $mainPipeNumber.'/'.($currentSub+1);
                    }
                } else {
                    $pipe2 = $mainPipeNumber.'/'.($currentSub+1);
                }
            }
        } else {
            $refPipeToCheckSub = $motherPipeNumber.'/';
            $getPipeData = Cutting::getPipeRefData($refPipeToCheckSub);
            if(!empty($getPipeData)){
                $refPipeNum = $getPipeData->pipe_number;
                $refPipeNumExpld = explode('/', $refPipeNum);
                if(!empty($refPipeNumExpld)){
                    $refPipeCurrentSub = end($refPipeNumExpld);
                    $pipe2 = $motherPipeNumber.'/'.($refPipeCurrentSub+1);
                } else {
                    $pipe2 = $motherPipeNumber.'/1';
                }
            } else {
                $pipe2 = $motherPipeNumber.'/1';
            }
        }

        return [
            'pipe_1' => $pipe1,
            'pipe_2' => $pipe2
        ];
    }
    
    public function beforeSave($insert){

		if (parent::beforeSave($insert)) {					
			$this->project_id = empty($this->project_id) ? Yii::$app->user->identity->project_id : $this->project_id;
            if(Yii::$app->controller->id!="sync"){
                // $this->UpdatePipeNumber();
                $cutPipes = $this->updatePipeNumber();
                $newPipe1 = $cutPipes['pipe_1'];
                $newPipe2 = $cutPipes['pipe_2'];

                $this->new_pipe_1 = $newPipe1;
                $this->new_pipe_2 = $newPipe2;
            }
            $mo = Yii::$app->general->setTimestamp($this);
            $this->created_at =  $mo->created_at;
            $this->updated_at  = $mo->updated_at; 
        	return true;
		} else {
			return false;
		}
	}
}
