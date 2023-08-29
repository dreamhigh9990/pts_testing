<?php

namespace app\modules\pipe\controllers;
use yii\helpers\ArrayHelper;
use Yii;
use app\models\Pipe;
use app\models\PipeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\User;
use Da\QrCode\QrCode;
use yii\helpers\Html;
class DefaultController extends Controller
{
	public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [                    
                    // allow authenticated users
                    [
                        'allow' => true,
                        'actions' => ['auto-list','index','print','auto-list-from-reception','delete-multiple','signed-off','delete-img','changeproject','chekproject',
                                    'pipe-auto-list','pipe-auto-list-for-stringing','pipe-auto-list-for-welding','pipe-auto-list-for-bending','getdefectfield',
                                    'add-defect-field'
                                    ],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }
    public function actionAddDefectField(){
        ob_start();
    ?>
        <div class="form-group clearfix">
            <div class="col-md-12 clearfix">
                    <?= Html::dropDownList('Pipe[defects][]', '',Yii::$app->general->TaxonomyDrop(8),
                    ['class'=>" form-control",'disabled' => Yii::$app->general->isAllowed()]);?>
                    <a class="removeField btn-sm btn-danger pull-right " style="position: absolute;right: 16px;top: 2px;"><i class="fa fa-remove"></i></a>               
            </div>
        </div>  
     <?php
      $res['html'] = ob_get_clean();
      echo json_encode($res);die;
    }
    public function actionGetdefectfield($PipeNumber){ 
        $PipeDefects =  \app\models\Pipe::find()->where(['pipe_number'=>$PipeNumber])->active()->asArray()->one();
        $result      =  json_decode($PipeDefects['defects'],true);
        ob_start();
        ?>       
        <?php
        if (!empty($result)) {
             Yii::$app->general->pipeDefects($result);      
           
         }else{ ?>
                <label class="col-md-12 clearfix" for="reception-transferred">Defects</label>
                <div class="field-holder">
                    
                </div>
                <div class="col-md-12 clearfix" style="margin-top:10px">
                        <a class="addField btn-sm btn-success m-t-10">Add Defect</a>
                </div>			 
        <?php       
      }
      $res['html'] = ob_get_clean();
      echo json_encode($res);die;
    }
    public function actionIndex(){
	 	return $this->render('index');
    }
    public function actionPipeAutoList($pipe_number){	
        $arr =Pipe::find()->select('pipe.*,pipe_stringing.kp,pipe_reception.location')
					->leftJoin('pipe_stringing','pipe.pipe_number=pipe_stringing.pipe_number AND pipe.project_id=pipe_stringing.project_id AND pipe_stringing.is_active=1 AND pipe_stringing.is_deleted=0 AND pipe_stringing.project_id='.Yii::$app->user->identity->project_id)
					->leftJoin('pipe_reception','pipe.pipe_number=pipe_reception.pipe_number AND pipe.project_id=pipe_reception.project_id AND pipe_reception.is_active=1 AND pipe_reception.is_deleted=0 AND pipe_reception.project_id='.Yii::$app->user->identity->project_id)
					->where(['LIKE','pipe.pipe_number',$pipe_number])
                    ->active()->limit(20)->asArray()->all();	
        echo json_encode($arr);	die;
    }
    
    public function actionPipeAutoListForStringing($pipe_number,$location){	
        $arr =Pipe::find()->select(['pipe_reception.*','pipe.*'])
            ->leftJoin('pipe_reception','pipe.pipe_number=pipe_reception.pipe_number AND pipe.project_id=pipe_reception.project_id  AND pipe_reception.is_active=1 AND pipe_reception.is_deleted=0 AND pipe_reception.project_id='.Yii::$app->user->identity->project_id)
            ->where(['AND',['LIKE','pipe_reception.pipe_number',$pipe_number],['pipe_reception.location'=>$location]])
            ->active()->limit(20)->asArray()->all();
       echo json_encode($arr);	die;
    }
    
    public function actionPipeAutoListForWelding($pipe_number, $kp,$next=""){
        $kp     = floor($kp);
        if($next=="yes"){
            $nextKp = $kp+1;
            $arr = \app\models\Stringing::find()->where(['OR',['AND',['LIKE', 'pipe_number', $pipe_number],['=','FLOOR(kp)',$kp]],['AND',['LIKE', 'pipe_number', $pipe_number],['=','FLOOR(kp)',$nextKp]]])->active()->limit(20)->asArray()->all();
        }
        else{	
            $arr = \app\models\Stringing::find()->where(['AND',['LIKE', 'pipe_number', $pipe_number],['=','FLOOR(kp)',$kp]])->active()->limit(20)->asArray()->all();
        }
        echo json_encode($arr);	die;	
    }
    public function actionPipeAutoListForBending($pipe_number){
        // as per client says, commenting the code - unlink pipes from stringing
        // $arr = Pipe::find()->select(['pipe_stringing.*','pipe.*'])
        //     ->leftJoin('pipe_stringing','pipe.pipe_number=pipe_stringing.pipe_number AND pipe.project_id=pipe_stringing.project_id  AND pipe_stringing.is_active=1 AND pipe_stringing.is_deleted=0 AND pipe_stringing.project_id='.Yii::$app->user->identity->project_id)
        //     ->where(['AND',['LIKE','pipe_stringing.pipe_number',$pipe_number]])
        //     ->active()->limit(20)->asArray()->all();

        $arr = Pipe::find()->select(['pipe.*'])
            ->where(['AND', ['LIKE', 'pipe.pipe_number', $pipe_number]])
            ->active()->limit(20)->asArray()->all();
            echo json_encode($arr);
        die;

      	// $arr = \app\models\Stringing::find()->where(['LIKE', 'pipe_number', $pipe_number])->active()->limit(20)->asArray()->all();        
        // echo json_encode($arr);	die;	
    }

	public function actionDeleteMultiple($model){       
		Yii::$app->general->delete($model);		
		echo json_encode(array('status'=>true,'message'=>'Selected items has been deleted.'));die;
    }

	public function actionSignedOff($model){		 
		Yii::$app->general->doSignedoff($model);		
		echo json_encode(array('status'=>true,'message'=>'Selected items has been deleted.'));die;
    }

	public function actionPrint($model, $sort = ''){
        $searchmodel = '\\'.$model;
        $Ids  =   !empty($_POST['id'])?$_POST['id']:array();
        $model =  '\\'.str_replace("Search","",$model);
      
        if($model == '\app\models\Reception'){
            $Data =   $model::find()->select(['pipe.weight as pipe_weight','pipe_reception.*'])
                      ->leftJoin('pipe','pipe_reception.pipe_number = pipe.pipe_number AND pipe_reception.project_id = pipe.project_id AND 
                      pipe_reception.is_active = pipe.is_active AND  pipe_reception.is_deleted = pipe.is_deleted ')
                      ->where(['IN','pipe_reception.id',$Ids])->asArray()
                      ->all();
        }if($model == '\app\models\PipeTransfer'){
            $Data =   $model::find()->select(['pipe.weight as pipe_weight','pipe_transfer.*'])
                      ->leftJoin('pipe','pipe_transfer.pipe_number = pipe.pipe_number AND pipe_transfer.project_id = pipe.project_id AND 
                      pipe_transfer.is_active = pipe.is_active AND  pipe_transfer.is_deleted = pipe.is_deleted ')
                      ->where(['IN','pipe_transfer.id',$Ids])->asArray()
                      ->all();                     
        }else if($model == '\app\models\Pipe'){
            if(!empty($sort)){
                $Data = $model::find()->where(['IN','id',$Ids])->orderBy('pipe_number '.$sort)->asArray()->all();
            } else {
                $Data = $model::find()->where(['IN','id',$Ids])->orderBy('pipe_number DESC')->asArray()->all();
            }
        }else {
            $Data =   $model::find()->where(['IN','id',$Ids])->orderBy('id DESC')->asArray()->all();
        }
		$html = $this->renderAjax('print',['model'=>$searchmodel,'Data'=>$Data]);  
        echo json_encode(array('html'=>$html));die;  
	}	
	public function actionDeleteImg($ImgName){
		echo json_encode(Yii::$app->general->deleteImg($ImgName));die;
	}
    public function actionChangeproject(){
		if(Yii::$app->user->identity === ''){
			$result['success']=false;
			$result['message']="You are not login Please login first";
			return json_encode($result);die;
		}
        if(empty($_POST['projectid'])){
            $result['success']=false;
            $result['message']="Please first select one project";
            return json_encode($result);die;
		}		
		$uid=Yii::$app->user->identity->id;
        
        $Usermodel = User::find()->where(['id'=>$uid])->one(); 
        $Usermodel->project_id=$_POST['projectid'];
        $Usermodel->save(false);
        $result['success']=true;
        $result['message']="Project changed successfully";
        return json_encode($result);die;
	}
}
