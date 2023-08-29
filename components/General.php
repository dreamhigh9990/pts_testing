<?php
namespace app\components;
use yii\helpers\Url;
use Yii;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\base\Component;

use yii\base\InvalidConfigException; 

use app\models\Employee;

use app\models\Pipe;

use app\models\Welding;

use yii\web\UploadedFile;

use app\models\CsvImport;

use app\models\Taxonomy;

use app\models\TaxonomyValue;

use app\models\TaxonomyValueValue;
use app\models\Picture;
use app\models\Reception;
use app\models\Line;
use app\models\Cleargrade;
use app\models\Cable;
use app\models\CabStringing;
use app\models\Landowner;

class General extends Component
{
 
	
	public function setTimestamp($model){
		if ($model->isNewRecord){
			$model->created_at = !empty($model->created_at)?$model->created_at:time();
			$model->updated_at  = !empty($model->created_at)?$model->created_at:time(); 
		}else{
			if(Yii::$app->controller->id == 'sync' && Yii::$app->controller->action->id == 'save') {
				$model->updated_at = !empty($model->updated_at) ? $model->updated_at : time();
			} else {
				$model->updated_at = time();
			}
		}		
		return $model;
	}
	//========#######======######====== geo location validation =====#####
	public function validGeo($latlong){
		$result = false;
		if($latlong != ""){
			$result = preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?);[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $latlong);
		}
		return $result;
	}

//#################### TAXONOMY ################################
public function getTaxomonyData($id){
	$taxomonyDetails = TaxonomyValue::find()->where(['id'=>$id,'project_id'=>Yii::$app->user->identity->project_id])->active()->asArray()->one();
	return $taxomonyDetails;
}

public function getTaxoDataFromId($id){
	$taxomonyDetails = TaxonomyValue::find()->where(['id' => $id])->active()->asArray()->one();
	return $taxomonyDetails;
}
public function TaxonomyDrop($TaxonomyId,$is_id=false){
	if($TaxonomyId != 4){
		
		$TaxonomyDrop = TaxonomyValue::find()->where(['taxonomy_id'=>$TaxonomyId,'project_id'=>Yii::$app->user->identity->project_id])->active()->orderBy('value ASC')->asArray()->all();
	} else {
		$TaxonomyDrop = TaxonomyValue::find()->where(['taxonomy_id'=>$TaxonomyId])->active()->orderBy('value ASC')->asArray()->all();
	}
	 if(!empty($TaxonomyDrop)){
		 if(!$is_id){
			 return ArrayHelper::map($TaxonomyDrop,"value","value");
		 }else{
			return ArrayHelper::map($TaxonomyDrop,"id","value");
		 }
	 }
	 return array();
}
    public function TaxonomyChild($TaxonomyId){

        $Data = Taxonomy::find()->where(['id'=>$TaxonomyId])->one();

        $Taxarray = [];

        if(!empty($Data['child_value'])){

            $TaxonomyValueList = TaxonomyValue::find()->where(['taxonomy_id'=>$Data['child_value'],'project_id'=>Yii::$app->user->identity->project_id])->active()->asArray()->all();           

            if(!empty($TaxonomyValueList)){

                foreach($TaxonomyValueList as $ele){

                    array_push($Taxarray,array('id'=>$ele['id'],'value'=>htmlentities($ele['value'])));

                }

            }

        }

        return $Taxarray;       

	}
	public function getInsertedTaxonomyChild($id){
        $Taxarray = [];
        if(!empty($id)){
            $TaxonomyValueList = TaxonomyValueValue::find()->where(['parent_id'=>$id])->asArray()->all();  
            if(!empty($TaxonomyValueList)){
                foreach($TaxonomyValueList as $ele){
					$Taxarray[] = $ele['child_id'];
                }
            }
        }
        return $Taxarray;   
    }
//################### END TAXONOMY  ###################
//################## DEFAULT FORM FIELDS ###################
    public function defautField($model,$form){
		ob_start();	
		if ($model->isNewRecord){
			$User   = Yii::$app->user->identity->username;
			$Qa 	= "";
			$Signed ="No";
			$date = date('Y-m-d');
			$EditUser = "";
		}else{
			$Employee  		 = Employee::find()->where(['id'=>$model->created_by])->asArray()->one();
			$EmployeeEdit   =  Employee::find()->where(['id'=>$model->updated_by])->asArray()->one();
			$Qa         = Employee::find()->where(['id'=>$model->qa_manager])->asArray()->one();
	
			$User       = !empty($Employee) ? $Employee['username']:"";
			$EditUser   = !empty($EmployeeEdit) ? $EmployeeEdit['username']:"";
			$Qa   		= !empty($Qa) ? $Qa['username']:"";	
			$Signed 	= $model->signed_off;	
			$date =       $model->date; 
		}		
	   ?>	
	    <?= $form->field($model, 'report_number',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['class'=>"form-control ",'disabled'=>"disabled"]) ?>
		<?= $form->field($model, 'date',['template' => '<div class="col-md-12 clearfix ">{label}{input}{error}{hint}</div>'])->textInput(['class'=>'form-control pickadate','value'=>$date,'disabled' => Yii::$app->general->isAllowed()]);?>		        
   		<?php if (!$model->isNewRecord){ ?>
			<div class="form-group clearfix">
				<div class="col-md-6 clearfix">
					<label class="control-label"><?= Yii::$app->trans->getTrans('Created By'); ?></label>
					<input type="text" class="form-control " value="<?= $User;?>" disabled="disabled">
				</div>
				<div class="col-md-6 clearfix">
					<label class="control-label"><?= Yii::$app->trans->getTrans('Updated By'); ?></label>
					<input type="text" class="form-control " value="<?= $EditUser;?>" disabled="disabled">
				</div>
			
			</div>		
			<div class="form-group clearfix">				
				<div class="col-md-6 clearfix">
					<label class="control-label"><?= Yii::$app->trans->getTrans('Signed Off').'?'; ?></label>
					<input type="text" class="form-control " value="<?= $Signed;?>" disabled="disabled">
				</div>
				<div class="col-md-6 clearfix">
					<label class="control-label"><?= Yii::$app->trans->getTrans('QA Manager'); ?></label>
					<input type="text" class="form-control " value="<?=$Qa;?>" disabled="disabled">
				</div>
			</div>
		<?php } ?>
	<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	public function civilFiled($model,$form){
		echo $this->defautField($model,$form);
		$disabled =  $model->isNewRecord ? false:true;	
		$disabled =  $model->isNewRecord && !Yii::$app->general->isAllowed()? false:true;

		$class = '';
		if(get_class($model) == 'app\models\Trenching'){
			$class = 'trenching-auto';
		}else if(get_class($model) == 'app\models\Lowering'){
			$class = 'lowering-auto';
		}else if(get_class($model) == 'app\models\Backfilling'){
			$class = 'backfilling-auto';
		}else if(get_class($model) == 'app\models\Reinstatement'){
			$class = 'reinstatement-auto';
		}

		echo $form->field($model, 'from_kp')->textInput(['class'=>"form-control parameter-kp-from ",'disabled'=>$disabled]);

		echo $form->field($model, 'to_kp')->textInput(['class'=>"form-control parameter-kp-to ",'disabled'=>$disabled]);

		// echo $form->field($model, 'from_weld')->textInput(['class'=>"form-control from-weld ".$class,'data-weld'=>"from",'disabled'=>$disabled]) ;

		// echo $form->field($model, 'to_weld')->textInput(['class'=>"form-control to-weld ".$class,'data-weld'=>"to",'disabled'=>$disabled]) ;
		
	} 
	public function weldField($model,$form){
		echo $this->defautField($model,$form);
		$class = "weld-number-auto";
		$kpClass = '';
		$wrRequired = '';
		if(get_class($model)=="app\models\Weldingrepair"){
			$class = "ndt-reject-weld";
			$wrRequired = 'wr-req-fields';
		}else if(get_class($model)=="app\models\Ndt"){
			$class = "parameter-weld";
		}else if(get_class($model)=="app\models\Production"){
			$class = "production-weld";
		}else if(get_class($model)=="app\models\Coatingrepair"){
			$class = "coatingrepair-weld";
			$kpClass = 'coatingrepair-kp';
		}
		$disabled =  $model->isNewRecord && !Yii::$app->general->isAllowed()? false:true;

		echo $form->field($model, 'kp', ['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['class'=>'form-control parameter-kp '.$kpClass.' '.$wrRequired, 'disabled'=>$disabled]);
		echo $form->field($model, 'weld_number',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['maxlength' => true,'class'=>'form-control '.$class.' '.$wrRequired, 'disabled'=>$disabled]); 
		
	}	
	public function cableFiled($model,$form){
	    echo $this->defautField($model,$form);
	}	
	public function precommFiled($model,$form,$type){
		echo $this->defautField($model,$form);
		$disabled =  $model->isNewRecord && !Yii::$app->general->isAllowed()? false:true;
		if($type != "surveying"){
			echo $form->field($model, 'from_kp')->textInput(['class'=>'form-control parameter-kp-from','disabled'=>$disabled]);
			echo $form->field($model, 'to_kp')->textInput(['class'=>'form-control parameter-kp-to','disabled'=>$disabled]);
		}
		if($type == "surveying"){
			echo $form->field($model, 'kp',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['disabled'=>$disabled]);
		}
		if($type != "surveying"){
			$class = '';
			if(get_class($model) == 'app\models\Cleangauge'){
				$class = 'cleangauge-auto';
			}else if(get_class($model) == 'app\models\Hydrotesting'){
				$class = 'hydrotesting-auto';
			}else if(get_class($model) == 'app\models\Cathodicprotection'){
				$class = 'cathodic-auto';
			}

			echo $form->field($model, 'from_weld')->textInput(['class'=>"form-control from-weld ".$class,'data-weld'=>"from", 'disabled'=>$disabled]);
			echo $form->field($model, 'to_weld')->textInput(['class'=>"form-control to-weld ".$class,'data-weld'=>"to", 'disabled'=>$disabled]);
		}
	}
	public function pipeFiled($model,$form){
		
		if(get_class($model)=="app\models\Stringing"){
			$class = "auto-pipe-from-reception form-control";
		}else if(get_class($model)=="app\models\Bending"){
			$class = "auto-pipe-for-bending form-control";
		} else if(get_class($model)=="app\models\Cutting") {
			$class = "auto-pipe duplicate-cutting-check form-control";
		}else{
			$class = "auto-pipe form-control";
		}

		if ($model->isNewRecord){ 			
			echo $form->field($model, 'pipe_number',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['maxlength' => true,'class'=>$class,'disabled' => Yii::$app->general->isAllowed()]);
		}else{
			$PipeData = Pipe::find()->select('pipe.*,pipe_stringing.kp,pipe_reception.location')
				->leftJoin('pipe_stringing','pipe.pipe_number=pipe_stringing.pipe_number AND pipe.project_id=pipe_stringing.project_id AND pipe_stringing.is_active=1 AND pipe_stringing.is_deleted=0 AND pipe_stringing.project_id='.Yii::$app->user->identity->project_id)
				->leftJoin('pipe_reception','pipe.pipe_number=pipe_reception.pipe_number AND pipe.project_id=pipe_reception.project_id AND pipe_reception.is_active=1 AND pipe_reception.is_deleted=0 AND pipe_reception.project_id='.Yii::$app->user->identity->project_id)
				->where(['LIKE','pipe.pipe_number',$model->pipe_number])
				->active()->limit(20)->asArray()->all();
			$PipeData = !empty($PipeData[0])?$PipeData[0]:array();
	?>
		<div class="form-group clearfix">
			<div class="col-md-12 clearfix">
				<label class="control-label"><?= Yii::$app->trans->getTrans('Pipe Number'); ?></label>
				<input type="text" class="form-control " value="<?=$model->pipe_number;?>" disabled="disabled">
			</div>
		</div>
		<?php		 
		}
		?>
		<?php if(get_class($model)=="app\models\Stringing" || get_class($model)=="app\models\PipeTransfer" || get_class($model)=="app\models\Reception"){?>
			<div class="form-group clearfix">
				<div class="col-md-6 col-sm-6 clearfix">
					<label class="control-label"><?= Yii::$app->trans->getTrans('Pipe Length'); ?></label>
					<input type="text" id="pipe_length" class="form-control" disabled value="<?= !empty($PipeData['length'])?$PipeData['length']:"";?>">
				</div>
				<div class="col-md-6 col-sm-6 clearfix">
					<label class="control-label"><?= Yii::$app->trans->getTrans('Pipe Heat No'); ?></label>
					<input type="text" id="pipe_heat_number" class="form-control" disabled value="<?= !empty($PipeData['heat_number'])?$PipeData['heat_number']:"";?>">
				</div>					
			</div>
			<div class="form-group clearfix">				
				<div class="col-md-12 col-sm-12 clearfix">
					<label class="control-label"><?= Yii::$app->trans->getTrans('Pipe Yield Strength'); ?></label>
					<input type="text" id="yeild_strength" class="form-control" disabled value="<?= !empty($PipeData['yeild_strength'])?$PipeData['yeild_strength']:"";?>">
				</div>					
			</div>
		<?php } ?>
		<?php if(get_class($model)=="app\models\Bending" || get_class($model)=="app\models\Cutting"){ ?>
			<?php if(get_class($model)=="app\models\Cutting"){ ?>
				<?= $form->field($model, 'new_pipe_1')->textInput(['maxlength' => true, 'class' => 'form-control', 'disabled' => true]); ?>
				<?= $form->field($model, 'length_1')->textInput(['maxlength' => true, 'class' => 'form-control', 'disabled' => Yii::$app->general->isAllowed()]); ?>
			<?php } ?>
			<div class="form-group clearfix">
				<div class="col-md-6 col-sm-6 clearfix">
					<label class="control-label"><?= Yii::$app->trans->getTrans('Pipe Wall Thickness'); ?></label>
					<input type="text" id="pipe_thickness" class="form-control" disabled value="<?= !empty($PipeData['wall_thikness'])?$PipeData['wall_thikness']:"";?>">
				</div>
				<div class="col-md-6 col-sm-6 clearfix">
					<?php if(get_class($model)=="app\models\Bending"){ ?>
						<?= $form->field($model, 'chainage',['template' => '<div class="col-md-12 col-sm-12 clearfix">{label}{input}{error}{hint}</div>'])->textInput(['maxlength' => true,'class'=>'form-control','disabled' => Yii::$app->general->isAllowed()]); ?>
					<?php } else { ?>
						<label class="control-label"><?= Yii::$app->trans->getTrans('KP'); ?></label>
						<input type="text" id="pipe_kp" class="form-control" disabled value="<?= !empty($PipeData['kp'])?$PipeData['kp']:"";?>">
					<?php } ?>
				</div>					
			</div>
		<?php } ?>
		<?php if(get_class($model)=="app\models\PipeTransfer"){ ?>
			<div class="form-group clearfix">
				<div class="col-md-12 clearfix">
					<label class="control-label"><?= Yii::$app->trans->getTrans('Pipe Current Location'); ?></label>
					<input type="text" id="pipe_location" class="form-control" disabled value="<?= !empty($PipeData['location'])?$PipeData['location']:"";?>">
				</div>			
			</div>
		<?php } ?>
	<?php
	}
	public function saveNdtdefects($Data,$weldNumber){		
		$d =array();	
		$Welding = \app\models\Welding::find()->where(['weld_number'=> $weldNumber])->active()->one();
		// print_r($Welding);
		// print_r($Data);die;
		// die;
		if(!empty($Welding)){
			if(!empty($Data['defect_position'])){
					foreach($Data['defect_position'] as $k => $item){
						if(!empty($Data['defects'][$k])){
							$d[$k]['defects'] 		  = $Data['defects'][$k];
							$d[$k]['defect_position'] = $item;
						}				
					}
			}			
			$Welding->ndt_defects = json_encode($d);
			$Welding->save(false);
		}	
		
	}
	public function ndtfield($result,$isDisable="",$edit=0){
	
		$defectPosition = [
			'Root OS' => 'Root OS',
			'Root TS' => 'Root TS',
			'Hot OS' => 'Hot OS',
			'Hot TS' => 'Hot TS',
			'Fill OS' => 'Fill OS',
			'Fill TS' => 'Fill TS',
			'Cap OS' => 'Cap OS',
			'Cap TS' => 'Cap TS',
		];
		?>
			         
		<?php
		$disabled  = Yii::$app->general->isAllowed();
		if(!empty($isDisable)){
			$disabled  = true	;
		}
	
		if(!empty($result)){			
			
		?>
	 	<?php  foreach($result as $k=>$def){ 
			 if(empty($def['defect_position'])){
				continue;
			 }?>
			<div class="form-group clearfix ddd"> 
				<div class="col-md-6 clearfix">
					<?= Html::dropDownList('Welding[defects][]',$def['defects'],Yii::$app->general->TaxonomyDrop(9),
					['class'=>" form-control",'disabled' => $disabled]);?>
					<?php if($isDisable == ""){?>
					<a class="removeField btn-sm btn-danger pull-right " style="position: absolute;right: 16px;top: 2px;"><i class="fa fa-remove"></i></a> 
					<?php } ?>  
				</div>
				<div class="col-md-6 clearfix">
					<?= Html::dropDownList('Welding[defect_position][]',$def['defect_position'],$defectPosition,
					['class'=>" form-control",'disabled' => $disabled]);?>                                
				</div>
				</div>
         <?php } ?>		 
		<?php }else if(!$edit){ ?>  
			<div class="form-group clearfix cfff">                        
					<div class="col-md-6 clearfix">
						<?php echo Html::dropDownList('Welding[defects][]','',Yii::$app->general->TaxonomyDrop(9),['class'=>" form-control",'disabled' => $disabled]);?>
						<?php if($isDisable == ""){ ?>
							<a class="removeField btn-sm btn-danger pull-right " style="position: absolute;right: 16px;top: 2px;"><i class="fa fa-remove"></i></a>   
						<?php } ?>
					</div>
					<div class="col-md-6 clearfix">
						<?php echo Html::dropDownList('Welding[defect_position][]','',$defectPosition,['class'=>" form-control",'disabled' => $disabled]);?>                                
					</div>
					</div>
		<?php } ?>
		
		<?php
	}
	public function savePipeDefect($PipeDefects,$pipeNumber){
		
		$PipeDefectsModel = \app\models\Pipe::find()->where(['pipe_number'=>$pipeNumber])->active()->one();		
		if(!empty($PipeDefectsModel)){				
			$PipeDefectsModel->defects = json_encode($PipeDefects);
			$PipeDefectsModel->save(false);	
		}
		
		return;
	}
	public function pipeDefects($result){
		if(!empty($result)){
		?>
		<div class="field-holder">
            <label class="col-md-12 clearfix" for="reception-transferred"><?= Yii::$app->trans->getTrans('Defects'); ?></label>
			<?php  foreach($result as $def){   ?>
					<div class="form-group clearfix">
						
						<div class="col-md-12 clearfix">
							<?= Html::dropDownList('Pipe[defects][]',$def,Yii::$app->general->TaxonomyDrop(8),
							['class'=>" form-control",'disabled' => Yii::$app->general->isAllowed()]);?>
							<a class="removeField btn-sm btn-danger pull-right " style="position: absolute;right: 16px;top: 2px;"><i class="fa fa-remove"></i></a>   
						</div>
					</div>
			<?php } ?>
        </div>
		<?php }else{ ?>
			<label class="col-md-12 clearfix" for="reception-transferred"><?= Yii::$app->trans->getTrans('Defects'); ?></label>
				<div class="field-holder">
				<!-- <label class="col-md-12 clearfix" for="reception-transferred">Defects</label>
                    <div class="form-group clearfix">
                        <div class="col-md-12 clearfix">                            
                            <?= Html::dropDownList('Pipe[defects][]', '',Yii::$app->general->TaxonomyDrop(8),
                            ['class'=>" form-control",'disabled' => Yii::$app->general->isAllowed()]);?>
                            <a class="removeField btn-sm btn-danger pull-right " style="position: absolute;right: 16px;top: 2px;"><i class="fa fa-remove"></i></a>
                        </div>
                    </div>  -->
                </div>
		<?php }  ?>
		<div class="col-md-12 clearfix" style="margin-top:10px">
              <button type="button" class="btn addField btn-sm btn-success m-t-10" disabled="<?php echo Yii::$app->general->isAllowed() ? 'disabled' : ''; ?>"><?= Yii::$app->trans->getTrans('Add Defect'); ?></button>
		</div>
		<?php
	}
	public function defautFileField($model,$form,$section){	?>
		<?php
		$Picture = new \app\models\Picture;
		?>
	
			<?php 
			if (get_class($model)=="app\models\Reception" 
			|| get_class($model)=="app\models\PipeTransfer" 
			|| get_class($model)=="app\models\Bending" 
			|| get_class($model)=="app\models\Cutting" 
			||get_class($model)=="app\models\Stringing") {?>
				<div class="main-holder">
				<?php
					$PipeDefects =  \app\models\Pipe::find()->where(['pipe_number'=>$model->pipe_number])->active()->asArray()->one();
					$result      =  !empty($PipeDefects['defects']) ? json_decode($PipeDefects['defects'],true):array();					
					
					Yii::$app->general->pipeDefects($result);  
					
				?>
			</div>	
			<?php  } ?>
			
		<?php if(!$this->isAllowed()){  ?>
	   			<?= $form->field($Picture, 'image[]',['template' => '<div class="col-md-7 clearfix">{label}{input}{error}{hint}</div>'])->fileInput(['multiple'=>true]) ?>
        <?php } ?>
		<div class="col-md-12 clearfix image-container">
			<div class="row">                                  
				<?php if (!$model->isNewRecord) { 
						echo $this->getImgesHtml($model->id,$section);
				}?>
			</div>
		</div>
		<?= $form->field($model, 'comment',['template' => '<div class="col-md-12 clearfix">{label}{input}{error}{hint}</div>'])->textarea(['rows' => 2, 'disabled' => Yii::$app->general->isAllowed()]) ?>                
	<?php } 
//################## END DEFAULT FORM FIELDS ###################
	public function pipeTransfer($model){
		if (!$model->isNewRecord) { 
			$TransferList = \app\models\PipeTransfer::find()->where(['pipe_number'=>$model->pipe_number])->active()->asArray()->all();
			if(!empty($TransferList)){
				echo'<label class="control-label" for="reception-comment">Transfer Report</label>';
				echo'<ul class="list-group">';
				foreach($TransferList as $transfer){
					echo'<li class="list-group-item">'.Html::a($transfer['report_number'].' : '.$transfer['new_location'],['/pipe/pipe-transfer/create','EditId'=>$transfer['id']]).'</li>';
				}
				echo' </ul>';
			}
		}
	}
//################## GRID BUTTON ########################
	public function doSignedoff($model){		
		$POST = Yii::$app->getRequest()->getBodyParams();
		if(!empty($POST['signedId'])){  	
				 $model::updateAll(['signed_off'=>'Yes','qa_manager'=>Yii::$app->user->identity->id],['IN','id',$POST['signedId']]);		
                 return array('status'=>true);
				
		}else{
            return array('status'=>false,'message'=>'Please select item to delete.');
		}
		return array('status'=>true,'message'=>'Selected items has been deleted.');
	}
	public function signOffButton($model){
		$html = "";
		if(Yii::$app->user->identity->type =="QA Manager" || Yii::$app->user->identity->type =="Admin"){
			$html = '<button type="button" url="pipe/default/signed-off?model='.$model.'"  class="pull-right mr-1 mb-1 btn btn-raised btn-outline-warning btn-min-width signed-selected"><i class="fa fa-check"></i> '.Yii::$app->trans->getTrans('Sign Off').'</button>';
		}
		return $html;
	}
	public function gridButton($model, $sort = ''){
		$cstClass = '';
		if($model == 'app\models\Pipe'){
			$cstClass = 'btn-sm';
		}
		$html =Html::a('<i class="fa fa-filter"></i> '.Yii::$app->trans->getTrans('Clear Filter'), 'create', ['class'=>'pull-right mr-1 mb-1 btn '.$cstClass.' btn-raised btn-outline-info btn-min-width']);
		if(!$this->isAllowed()){
			if($model == 'app\models\Pipe'){
				$html .='<button type="button" url="pipe/default/print?model='.$model.'Search&sort='.$sort.'"  class="mr-1 mb-1 btn '.$cstClass.' btn-raised btn-outline-primary btn-min-width print-selected"><i class="fa fa-print"></i> '.Yii::$app->trans->getTrans('Print selected').'</button>';
			} else {
				$html .='<button type="button" url="pipe/default/print?model='.$model.'Search"  class="mr-1 mb-1 btn '.$cstClass.' btn-raised btn-outline-primary btn-min-width print-selected"><i class="fa fa-print"></i> '.Yii::$app->trans->getTrans('Print selected').'</button>';
			}
			if(Yii::$app->user->identity->type=="Admin" ||Yii::$app->user->identity->type=="QA Manager"){
				$html .='<button type="button" url="pipe/default/delete-multiple?model='.$model.'"  class="mr-1 mb-1 btn '.$cstClass.' btn-raised btn-outline-danger btn-min-width delete-multipe"><i class="fa fa-times"></i> '.Yii::$app->trans->getTrans('Delete selected').'</button>';
				$html .='<a href ="'.\yii\helpers\Url::current(['delete-all' =>1]).'" data-confirm="Are you absolutely sure ? You will lose all the information.">
					<button class ="mr-1 mb-1 btn '.$cstClass.' btn-raised btn-outline-danger btn-min-width pull-right"><i class="fa fa-trash trash-icon"></i>'.Yii::$app->trans->getTrans('Delete All').'</button>
				</a>';
			}
		}
		return $html;
	
	}
	public function doprint($model){		
		$POST = Yii::$app->getRequest()->getBodyParams();
		if(!empty($POST['printid'])){  			
				 $Data = $model::find()->where(['IN','id',$POST['printid']])->asArray()->all();
                 return array('status'=>true,'data'=>$Data);				
		}else{
            return array('status'=>false,'message'=>'Please select item to delete.');
		}
		return array('status'=>true,'message'=>'Selected items has been deleted.');
	}
	public function welding($weldId,$time){

		$Welding = \app\models\Welding::find()->where(['IN','id',$weldId])->asArray()->all();
		// $PipeLinkedModels = ['\app\models\Parameter', '\app\models\Ndt', '\app\models\Weldingrepair', '\app\models\Production', '\app\models\Coatingrepair', '\app\models\Trenching', '\app\models\Lowering', '\app\models\Backfilling', '\app\models\Reinstatement', '\app\models\Cleangauge', '\app\models\Hydrotesting'];

		$PipeLinkedModels = ['\app\models\Parameter', '\app\models\Ndt', '\app\models\Weldingrepair', '\app\models\Production', '\app\models\Coatingrepair']; // only welding and coating module as per client says

		foreach($PipeLinkedModels as $model){
			if(!empty($Welding)){
				foreach($Welding as $kp => $weld){
					if($model=="\app\models\Trenching"||$model=="\app\models\Lowering"||$model=="\app\models\Backfilling"
						||$model=="\app\models\Reinstatement"||$model=="\app\models\Backfilling"|| $model=="\app\models\Cleangauge"
						||$model=="\app\models\Hydrotesting"){									
							$model::updateAll(['is_deleted'=>1,'updated_at'=>$time],
									[	'AND',
										['OR',
											['from_weld'=>$weld['weld_number'],'from_kp'=>$weld['kp']],
											['to_weld'=>$weld['weld_number'],'to_kp'=>$weld['kp']],										
										],
										['project_id'=>$weld['project_id']]
									]);

					}else{
						$model::updateAll(['is_deleted'=>1,'updated_at'=>$time],
						['AND',
							['=','weld_number',$weld['weld_number']],
							['=','kp',$weld['kp']],
							['project_id'=>$weld['project_id']]
						]);	
					}	
				}
			}					
		}
	}
    public function delete($model,$Ids=array()){  
	
		$POST = Yii::$app->getRequest()->getBodyParams();
		if(empty($POST['deleteId'])){ 
			$POST['deleteId'] = $Ids;
		}
	//.//	print_r($POST['deleteId']);die;
		if(!empty($POST['deleteId'])){ 
			$time = time();

			$model::updateAll(['is_deleted'=>1,'updated_at'=>$time],['IN','id',$POST['deleteId']]);

			if($model=="app\models\Pipe"){
				$Pipe =ArrayHelper::map(\app\models\Pipe::find()->where(['IN','id',$POST['deleteId']])->asArray()->all(),'id','pipe_number');
				$PipeLinkedModels = ['\app\models\Reception','\app\models\PipeTransfer','\app\models\Bending','\app\models\Stringing','\app\models\Welding','\app\models\Cutting'];
				foreach($PipeLinkedModels as $Table){
					if($Table == "\app\models\Welding"){
						$weldId = ArrayHelper::map(\app\models\Welding::find()->where(['OR',['IN','pipe_number',$Pipe],['IN','next_pipe',$Pipe]])->asArray()->all(),'id','id');
						$this->welding($weldId,$time);
						$Table::updateAll(['is_deleted'=>1,'updated_at'=>$time],['OR',['IN','pipe_number',$Pipe],['IN','next_pipe',$Pipe]]);
					}else{
						$Table::updateAll(['is_deleted'=>1,'updated_at'=>$time],['IN','pipe_number',$Pipe]);	
					}
				}
			}	
			if($model=="app\models\Welding"){			
				$this->welding($POST['deleteId'],$time);
			}	
			if($model=="app\models\Cable"){
				$Cab 		 	  = ArrayHelper::map(\app\models\Cable::find()->where(['IN','id',$POST['deleteId']])->all(),'id','drum_number');
				$PipeLinkedModels = ['\app\models\CabSplicing','\app\models\CabStringing'];
				foreach($PipeLinkedModels as $Table){
					$Table::updateAll(['is_deleted'=>1,'updated_at'=>$time],['IN','drum_number',$Cab]);
				}
			}
		}else{
            return array('status'=>false,'message'=>'Please select item to delete.');
		}
		return array('status'=>true,'message'=>'Selected items has been deleted.');

   }   
//################## END GRID BUTTON ########################	
	public function employeeList($type){
		if(!empty($type)){	
			$QaList = Employee::find()->where(['type'=>$type])->active()->orderBy('username ASC')->asArray()->all();  
		}else{
			$QaList = Employee::find()->where(1)->active()->orderBy('username ASC')->asArray()->all();  
		}
		 if(!empty($QaList)){
			 return ArrayHelper::map($QaList,"id","username");
		 }
		 return array();
	}		
	public function pipeHeatList(){
		$HeatList = Pipe::find()->select('heat_number')->distinct('heat_number')->active()->asArray()->all(); 	
		if(!empty($HeatList)){
			return ArrayHelper::map($HeatList,"heat_number","heat_number");
		}
		return array();
    }
################ IMAGE SECTION FUNCTION ############################
	public function deleteImg($name){		
		$Picture = Picture::find()->where(['image'=>$name])->one();
		if(!empty($Picture)){
			if(file_exists(Yii::$app->basePath.'/web/images/'.$Picture->image)){
				!unlink(Yii::$app->basePath.'/web/images/'.$Picture->image);
				$Picture->is_deleted = 1;
				$Picture->save(false);				
				return array('message'=>'Image has been deleted.','status'=>true);
			} else {
				$Picture->is_deleted = 1;
				$Picture->save(false);	
				return array('message'=>'Image has been deleted.','status'=>true);
			}
		}else{
			return array('message'=>'No image exist in database.','status'=>false);
		}
	}
	public function getImges($section_id,$section_type){
		return Picture::find()->select(['CONCAT("@web/images", "/", image) as image','image as image_name'])->where(['section_id'=>$section_id,'section_type'=>$section_type,'is_deleted'=>0])->asArray()->all();
	}
	public function getImgesHtml($section_id,$section_type){
			$contents= "";
		    $Picture = $this->getImges($section_id,$section_type);
			if(!empty($Picture)){
			    ob_start();
				foreach($Picture as $img){
					$ext = explode('.',$img['image_name']);  ?> 
                                                     
					  <div class="col-xl-4 col-lg-6 col-12 row">
							<div class="card text-center">
								<div class="card-body">
									<?php if(in_array(strtolower(end($ext)),['jpg','jpeg','gif','png'])){?>
									<a class="example-image-link" data-title="<?=$img['image_name'];?>" data-lightbox="example-set" href="<?= Url::to($img['image']); ?>" target="_blank">
									<img src="<?= Url::to('@web/images/site/picture.png'); ?>" class="">		
									</a> 
									<?php  } else{ ?>
									 <a data-pjax="0" href="<?= Url::to($img['image']); ?>" target="_blank">
									<img src="<?= Url::to('@web/images/site/docs.png'); ?>" class="">	
									</a> 
									<?php  }?>
									<?php if(!$this->isAllowed()){ ?>
									<button class="btn btn-danger btn-sm img-remove mt-1" data-image="<?=$img['image_name'];?>">Remove</button>
									<?php } ?>
								</div>
							</div>
						</div>
                        
						<?php
				}  
				$contents = ob_get_contents();
				ob_end_clean();
        }
		return $contents;
    }
	public function UploadImg($section_id,$section_type){		
		$Picture = new \app\models\Picture();
	    $Images  = UploadedFile::getInstances($Picture, 'image');	
		if(!empty($Images)){
			 foreach ($Images as $file) {
				    $filename 			= time().'-'.$file->name;
					$model	  			 = new \app\models\Picture();
					$model->section_id   = $section_id;
					$model->image	     = $filename;
					$model->mime_type    = mime_content_type($file->tempName);
					$model->section_type = $section_type;
			
					if ($model->save() === false && !$model->hasErrors()) {
						throw new \yii\web\HttpException(200, 'Failed to save defects in database :'.json_encode($model->hasErrors()));
					}else{
						if(!move_uploaded_file($file->tempName, \Yii::$app->basePath.'/web/images/'.$filename)){
							throw new \yii\web\HttpException(200, 'Error in file uploading');
						}
					}
            }
			return  array('status'=>true,'message'=>'Your file has been uploaded.');
			
		}else{
		  return array('status'=>true);
		}		
	}
################ END IMAGE SECTION FUNCTION ############################	
################ AUTO LIST  ############################	
    
	public function drumAutoList($drum_number,$fromStringing){
		if(!$fromStringing){
			$arr =Cable::find()->where(['LIKE','drum_number',$drum_number])->active()->limit(20)->asArray()->all();	
		}else{
			$arr =CabStringing::find()->where(['LIKE','drum_number',$drum_number])->active()->limit(20)->asArray()->all();	
		}
		return $arr;
	}	
	public function kpAutoList($kp_number, $type, $project){
		$field = '';
		if($type == "from"){
			$field = 'from_kp';
		} else if($type == "to"){
			$field = 'to_kp';
		}

		$arr = array();
		if($field != ''){
			$arr = Line::find()->where(['AND',['LIKE',$field,$kp_number],['project_id'=>$project]])->active()->asArray()->all();	
		}
		return $arr;
	} 
	
################ END AUTO LIST  ############################	
################ OTHER  ############################	
	public function makeAnomally($sectionData,$model){
		$sectionData->is_active = 0;
		$sectionData->is_anomally  = "Yes";
		$sectionData->why_anomally = "Add new data with same pipe number";
		$sectionData->save();

		$model->is_anomally  = "Yes";
		$model->is_active    = 1;
		$model->why_anomally = "Pipe number is already assigned with this section";
		return $model;
	}	
	public function getLocationGeoCode($locationId){
		if(!empty($locationId)){
			$locationDetails = TaxonomyValue::find()->where(['value'=>$locationId, 'project_id'=>Yii::$app->user->identity->project_id])->active()->asArray()->one();
			return $locationDetails;
		}
		return array();
	}
	
	public function reportNo($model,$report_prefix){
		$LoginUserId = Yii::$app->user->identity->id;

		if ($model->isNewRecord){			
			$model->report_number  =   $report_prefix."-".$LoginUserId.'-'.str_replace("-","",date('Y-m-d'));
			
		}
		return $model;

	}
	public function cloneModel($className,$model) {
		$attributes = $model->attributes;
		$newObj = new $className;
		foreach($attributes as  $attribute => $val) {
			if($attribute!="id"){
				$newObj->{$attribute} = $val;
			}
		}
		$newObj->save();
		return $newObj;
	}
	public function getModelData($model,$EditId){
		$model = $model::find()->where(['id'=>$EditId,'project_id'=>Yii::$app->user->identity->project_id,'is_deleted'=>0])->one();
		 if(!empty($model)){
			 return $model;
		 }else{
			return array('status'=>false,'message'=>'This item is not exist in this project');die;
		 }
	}
	public function isAllowed(){
		$type = Yii::$app->user->identity->type;
		if($type !="Client"){
			return false;
		}else{
			return true;
		}
	}

	public function clientViewFilter($url){
		return $url.'&view=1';
	}
	
	public function hasEditAccess($recordCreatedId){
		
		$type = Yii::$app->user->identity->type;
		$loggedId = Yii::$app->user->identity->id;
		
		if($type == 'Client'){
			if(isset($_GET['view']) && $_GET['view'] == 1) return true;
			return false;
		} else if($type == 'QA Manager' || $type == 'Admin') {
			return true;
		} else {
			if($loggedId == $recordCreatedId){
				return true;
			} else {
				return false;
			}
		}
	}

	public function logo(){
		$Setting = \app\models\Setting::find()->where(['id'=>1])->one();
		if(!empty($Setting)){
			return Yii::getAlias('@web').'/images/site/'.$Setting->value;
		}
		return "";
		
	}
	public function generateReport($weldBook=0){
		return '<a href ="'.\yii\helpers\Url::current(['download' =>1,'weldBook'=>$weldBook]).'" data-pjax="0" target="_blank" >
			<button class ="btn btn-raised btn-white btn-min-width mr-1 mb-1 black pull-right">'.Yii::$app->trans->getTrans('Export to XLS').'</button>
		</a>';
	}
	public function getCombineReportNo($kp,$weld){
		$html ='<table class="table">
					<tr>                                   
						<th>'.Yii::$app->trans->getTrans('Section').'</th>				
						<th>'.Yii::$app->trans->getTrans('Report Number').'</th>
					</tr>';                         
		$modelArray = [
				'Welding'  =>'\app\models\Welding',
				'Parameter'=>'\app\models\Parameter',
				'Ndt'	   =>'\app\models\Ndt',
				'Production'	   =>'\app\models\Production',
				'Trenching'	   =>'civil_trenching',
				'Lowering'	   =>'civil_lowering',
				'Backfilling'	   =>'civil_backfilling',
				'Reinstatement'	   =>'civil_reinstatement',
				'Cathodicprotection'	   => 'com_cathodic_protection',
				'Cleangauge'	   =>'com_clean_gauge',
				'Hydrotesting'	   =>'com_hydrotesting'
			];
			foreach($modelArray as $k=>$model){
				
				if($k=="Welding" || $k=="Parameter"|| $k=="Ndt"|| $k=="Production"){
					$rpw   = $model::find()->select(['report_number','id'])->where(['kp'=>$kp,'weld_number'=>$weld])->active()->asArray()->one();
					if(!empty($rpw['report_number'])){
						$url = "/welding/".strtolower($k).'/create/';
						$html .= "<tr><td>".$k." Report  </td><td>". \yii\helpers\Html::a($rpw['report_number'],[$url,'EditId'=>$rpw['id']],['target'=>'_blank','data-pjax'=>0]).'</td></tr>';
					}
				}
				if($k=="Trenching" || $k=="Lowering"|| $k=="Backfilling"|| $k=="Reinstatement"){
					
					
					$kpWW = (($kp * 1000000) + ($weld * 0.0000001));
					$sql = "SELECT * FROM (SELECT id,report_number,from_kp, from_weld, to_kp, to_weld, is_deleted, is_active,  project_id, ((from_kp * 1000000) + (from_weld * 0.0000001)) as from_ww,  ((to_kp * 1000000) + (to_weld * 0.0000001)) as to_ww FROM `".$model."` WHERE is_deleted = 0 and is_active = 1 and project_id = ".Yii::$app->user->identity->project_id.") as ct WHERE (ct.from_ww <= ".$kpWW." AND ct.to_ww >= ".$kpWW.") ORDER by ct.from_ww ASC";

					$rpw   = Yii::$app->db->createCommand($sql)->queryOne();
				
					if(!empty($rpw['report_number'])){
						$url = "/civil/".strtolower($k).'/create/';
						$html .= "<tr><td>".$k." Report  </td><td>". \yii\helpers\Html::a($rpw['report_number'],[$url,'EditId'=>$rpw['id']],['target'=>'_blank','data-pjax'=>0]).'</td></tr>';
					}
				}
				if($k=="Cathodicprotection" || $k=="Cleangauge"|| $k=="Hydrotesting"){
					$kpWW = (($kp * 1000000) + ($weld * 0.0000001));
					$sql = "SELECT * FROM (SELECT id,report_number,from_kp, from_weld, to_kp, to_weld, is_deleted, is_active,  project_id, ((from_kp * 1000000) + (from_weld * 0.0000001)) as from_ww,  ((to_kp * 1000000) + (to_weld * 0.0000001)) as to_ww FROM `".$model."` WHERE is_deleted = 0 and is_active = 1 and project_id = ".Yii::$app->user->identity->project_id.") as ct WHERE (ct.from_ww <= ".$kpWW." AND ct.to_ww >= ".$kpWW.") ORDER by ct.from_ww ASC";

					$rpw   = Yii::$app->db->createCommand($sql)->queryOne();

					if(!empty($rpw['report_number'])){
						$url = "/precommissioning/".strtolower($k).'/create/';
						$html .= "<tr><td>".$k." Report </td><td> ". \yii\helpers\Html::a($rpw['report_number'],[$url,'EditId'=>$rpw['id']],['target'=>'_blank','data-pjax'=>0]).'</td></tr>';
					}
				}
			
			}
		$html .= "</table>";
		return $html;
	}
	public function getAllWeldData($from_kp,$from_weld,$to_kp,$to_weld){

		$fromKP = $from_kp;
		$fromWeld = $from_weld;
		$toKP = $to_kp;
		$toWeld = $to_weld;
		$fromWW = (($fromKP * 1000000) + ($fromWeld * 0.0000001));
		$toWW = (($toKP * 1000000) + ($toWeld * 0.0000001));
		$query = "SELECT * FROM (SELECT kp, weld_number, is_deleted, is_active,  project_id, ((kp*1000000) + (weld_number * 0.0000001)) as kp_ww FROM welding_coating_production) as ct WHERE (ct.kp_ww >= ".$fromWW ." AND ct.kp_ww <= ".$toWW.") and ct.is_deleted = 0 and ct.is_active = 1 and ct.project_id = ".Yii::$app->user->identity->project_id." ORDER by ct.kp_ww ASC";
		$list = Yii::$app->db->createCommand($query)->queryAll();
		return $list;
	}
	public function downloadCsvforweldbook($data){
		
		$a  = array();
		$List = Yii::$app->general->TaxonomyDrop(6,true);	
		$empList = Yii::$app->general->employeeList("");
		$projectList = Yii::$app->general->TaxonomyDrop(4, true);

		if($data){
			foreach($data as $ele){
				$pipeDetails = \app\models\Pipe::find()->where(['pipe_number'=>$ele['pipe_number']])->active()->asArray()->one();
				$getNdtDetails = \app\models\Ndt::find()->where(['weld_number' => $ele['weld_number'], 'kp' => $ele['kp'], 'main_weld_id' => $ele['id']])->active()->asArray()->orderBy('Id DESC')->all();
				$weldRepairDetails = \app\models\Weldingrepair::find()->where(['weld_number' => $ele['weld_number'], 'kp' => $ele['kp'], 'main_weld_id' => $ele['id']])->active()->asArray()->orderBy('Id DESC')->one();
				$productionDetails = \app\models\Production::find()->where(['weld_number'=>$ele['weld_number'], 'kp' => $ele['kp']])->active()->asArray()->one();
				foreach($ele as $key=>$val){
					if($key == 'id')continue;
					if($key == 'ndt_defects')continue;
					if($key == 'is_anomally')continue;
					if($key == 'why_anomally')continue;
					if($key == 'updated_at')continue;
					if($key == 'updated_by')continue;
					if($key == 'is_deleted')continue;
					if($key == 'is_active')continue;
					if($key == 'created_at')continue;


					if($key == "WPS"){
						$v[$key] =  !empty($List[$val]) ? $List[$val] : "";
					}else{
						$v[$key] =  $val;
					}

					if($key == 'qa_manager'){
						$v[$key] =  !empty($empList[$val]) ? $empList[$val] : "";
					}
					
					if($key == 'created_by'){
						$v[$key] =  !empty($empList[$val]) ? $empList[$val] : "";
					}
					
					if($key == 'updated_by'){
						$v[$key] =  !empty($empList[$val]) ? $empList[$val] : "";
					}
					
					if($key == 'updated_at'){
						$v[$key] =  date('Y-m-d',$val);
					}
					
					if($key == 'project_id'){
						$v[$key] =  !empty($projectList[$val]) ? $projectList[$val] : $val;
					}

					if($key == 'electrodes'){
						//for pipe
						$v['wall_thickness'] = !empty($pipeDetails['wall_thikness']) ? $pipeDetails['wall_thikness'] : '-';
						$v['length'] = !empty($pipeDetails['length']) ? $pipeDetails['length'] : '';
						
						//for ndt
						$v['ndt_report'] = !empty($getNdtDetails[0]['report_number']) ? $getNdtDetails[0]['report_number'] : '-';
						// $v['ndt_result'] = !empty($getNdtDetails[0]['outcome']) ? $getNdtDetails[0]['outcome'] : '';
						$ndtResults = '';
						if(!empty($getNdtDetails)){
							$ndtResultArray = [];
							foreach($getNdtDetails as $ndt){
								$ndtResultArray[] = $ndt['outcome'].':'.$ndt['date'];
							}
							$ndtResults = implode(',', $ndtResultArray);
						}
						$v['ndt_result'] = $ndtResults;

						//for weld repair
						$v['weld_repair_report'] = !empty($weldRepairDetails['report_number']) ? $weldRepairDetails['report_number'] : '-';
						$v['weld_repair_welder'] = !empty($weldRepairDetails['welder']) ? $weldRepairDetails['welder'] : '-';
						$v['weld_repair_electrodes'] = !empty($weldRepairDetails['electrodes']) ? $weldRepairDetails['electrodes'] : '-';
						
						//for coating production
						$v['coating_report'] = !empty($productionDetails['report_number']) ? $productionDetails['report_number'] : '-';
						$v['applicator'] = !empty($productionDetails['abrasive_material']) ? $productionDetails['abrasive_material'] : '-';
						$v['material_batch_number'] = !empty($productionDetails['material_batch_number']) ? $productionDetails['material_batch_number'] : '-';
						$v['batch_number_a'] = !empty($productionDetails['batch_number_a']) ? $productionDetails['batch_number_a'] : '-';
						$v['batch_number_b'] = !empty($productionDetails['batch_number_b']) ? $productionDetails['batch_number_b'] : '-';
						$v['dft_1'] = !empty($productionDetails['dft']) ? $productionDetails['dft'] : '-';
						$v['dft_2'] = !empty($productionDetails['dft_2']) ? $productionDetails['dft_2'] : '-';
						$v['dft_3'] = !empty($productionDetails['dft_3']) ? $productionDetails['dft_3'] : '-';
						$v['dft_4'] = !empty($productionDetails['dft_4']) ? $productionDetails['dft_4'] : '-';
						$v['dft_5'] = !empty($productionDetails['dft_5']) ? $productionDetails['dft_5'] : '-';
						$v['dft_6'] = !empty($productionDetails['dft_6']) ? $productionDetails['dft_6'] : '-';
						$v['check_points'] = !empty($productionDetails['checkpoint']) ? $productionDetails['checkpoint'] : '-';
					}
				}
				$key = array_keys($v);
				array_push($a,$v);
			}
			if(!empty($key)){
				foreach($key as $k => $v){
					if($v == 'project_id')$v = 'project_name';
					if($v == 'line_type')$v = 'join_type';
					if($v == 'next_pipe')$v = 'next_pipe_number';
					$key[$k] = ucwords(str_replace('_',' ',$v));
				}
			}
			$data = array_merge(array($key),$a);

			$filename = time().'weldbook.xlsx';
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename={$filename}.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			$outputBuffer = fopen("php://output", 'w');
			foreach($data as $val) {
				fputcsv($outputBuffer, $val);
			}
			fclose($outputBuffer);die;
		}
	}
	public function downloadCsvPlantDashboard($data){
		$a = array();
		$empList = Yii::$app->general->employeeList("");
		$projectList = Yii::$app->general->TaxonomyDrop(4, true);

		if($data){
			foreach($data as $ele){
				$getVehicleNumber = \app\models\VehicleSchedule::find()->select('report_number, vehicle_number')->where(['id' => $ele['vehicle_id']])->active()->asArray()->one();
				$getMapDetails = \app\models\MapPartVehicleInspection::find()->select('inspection_id')->where(['inspection_id' => $ele['id'], 'status' => 'Fail'])->asArray()->all();
				foreach($ele as $key => $val){
					if($key == 'id')continue;
					if($key == 'date')continue;
					if($key == 'location')continue;
					if($key == 'vehicle_id')continue;
					if($key == 'geolocation')continue;
					if($key == 'created_at')continue;
					if($key == 'is_deleted')continue;
					if($key == 'is_active')continue;


					if($key == 'report_number'){
						$v['schedule_record'] = !empty($getVehicleNumber['report_number']) ? $getVehicleNumber['report_number'] : '';
						$v['vehicle_number'] = !empty($getVehicleNumber['vehicle_number']) ? $getVehicleNumber['vehicle_number'] : '';
						$v['inspection_record'] = $ele[$key];
						if(strtotime(date('Y-m-d')) > strtotime($ele['date'])){
							$v['inspection_date'] = 'Overdue - '.date('d/m/Y', strtotime($ele['date']));
						} else {
							$v['inspection_date'] = 'Today - '.date('d/m/Y', strtotime($ele['date']));
						}
					}

					if($key == 'service_due'){
						$v[$key] = $val;

						//for status column
						$status = [];
						if($val == 'Yes'){
							$status[] = 'Service Due';
						}
						
						$getMapDetails = \app\models\MapPartVehicleInspection::find()->select('inspection_id')->where(['inspection_id' => $ele['id'], 'status' => 'Fail'])->asArray()->all();
						if(!empty($getMapDetails)){
							$status[] = 'Issues Present';
						}

						if(strtotime(date('Y-m-d')) > strtotime($ele['date'])){
							$status[] = 'Overdue Inspection';
						} else {
							$status[] = 'Today Inspection';
						}

						$v['status'] = !empty($status) ? implode(',', $status) : '-';

						// for odometer
						$v['odometer_reading'] = !empty($ele['odometer_reading']) ? $ele['odometer_reading'] : '-';						
					}

					if($key == 'signed_off'){
						$v[$key] = $val;
					}
					
					if($key == 'qa_manager'){
						$v[$key] = !empty($empList[$val]) ? $empList[$val] : "";
					}
					
					if($key == 'created_by'){
						$v[$key] = !empty($empList[$val]) ? $empList[$val] : "";
					}
					
					if($key == 'updated_by'){
						$v[$key] = !empty($empList[$val]) ? $empList[$val] : "";
					}
					
					if($key == 'updated_at'){
						$v[$key] = date('Y-m-d',$val);
					}
					
					if($key == 'project_id'){
						$v[$key] = !empty($projectList[$val]) ? $projectList[$val] : $val;
					}
				}
				$key = array_keys($v);
				array_push($a,$v);
			}
			if(!empty($key)){
				foreach($key as $k => $v){
					if($v == 'project_id')$v = 'project_name';
					if($v == 'report_number')$v = 'Inspection Record';
					$key[$k] = ucwords(str_replace('_',' ',$v));
				}
			}
			$data = array_merge(array($key),$a);

			$filename = time().'_plant_dashboard.xlsx';
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename={$filename}.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			$outputBuffer = fopen("php://output", 'w');
			foreach($data as $val) {
				fputcsv($outputBuffer, $val);
			}
			fclose($outputBuffer);die;
		}
	}
	public function globalDownload($query, $app = false){
		$file = \Yii::createObject([
			'class' => 'codemix\excelexport\ExcelFile',
			'sheets' => [        
				'Active Users' => [
					'class' => 'codemix\excelexport\ActiveExcelSheet',
					'query' => $query, 
				],
			],
		]);
		$filename = time().'weldbook.xlsx';
		$path = \Yii::getAlias('@webroot').'/excel/'.$filename;
		$dPath = Url::base().'/excel/'.$filename;       
		$file->saveAs($path);

		$download_file = Yii::$app->basePath."/web/excel/".$filename;
		if($app){
			return Yii::$app->request->hostInfo.Yii::getAlias('@web').'/excel/'.$filename;die;
		}
		if(file_exists($download_file)){
			header('Content-Disposition: attachment; filename='.$filename);  
			readfile($download_file); 
			exit;
		}
	}
################ END OTHER  ############################
	public function getWeldDaily($date){
		$MianLineWeld = \app\models\Welding::find()->where(['date'=>$date,'line_type'=>"Main Line"])->active()->count();
		$TieLineWeld = \app\models\Welding::find()->where(['date'=>$date,'line_type'=>"Tie Line"])->active()->count();
	}

	public function getWeldAll($filtertype,$daterange){
		// $MianLineWeld = \app\models\Welding::find()->where(['line_type'=>"Main Line"])->active()->count();
		// $TieLineWeld = \app\models\Welding::find()->where(['line_type'=>"Tie Line"])->active()->count();
		$dd = [];
		$joins = [['line_type'=>"Main Line"],['line_type'=>"Tie Line"]];
		if($filtertype == "weekly"){
			$dx = explode("-",$daterange);
			$startDate   = date('Y-m-d',strtotime($dx[0]));
			$endDate = date('Y-m-d',strtotime($dx[1]));

		}else if($filtertype=="daily"){	
			$startDate   = date('Y-m-d',strtotime($daterange));
			$endDate   = date('Y-m-d',strtotime("+1 day",strtotime($startDate)));
		}else{
			$endDate   = date('Y-m-d');
			$startDate = date('Y-m-d',strtotime("-2 year",strtotime($endDate)));
			
		}
		foreach($joins as $join){

				$TotalWeldCount =0; $TotalWeldLength = 0;$TotalWeldRepairCount = 0;    
				$Welding =  \app\models\Welding::find()->select(['welding.date','welding.root_os','welding.root_ts','welding.hot_os','welding.hot_ts','welding.fill_os',
							'welding.fill_ts','welding.cap_os','welding.cap_ts','welding.weld_number','welding.kp'])
							// ->leftJoin('pipe','welding.pipe_number=pipe.pipe_number AND welding.project_id=pipe.project_id AND welding.is_active=pipe.is_active')
							->where(['between', 'welding.date',$startDate,$endDate])
							->andWhere($join)
							->active()->asArray()->all();
				if(!empty($Welding[0])){   
					//print_r($Welding);die;
					foreach($Welding as $Weld){
						
						$WelderCount = 0;        
						$WelderWeldPosition = array();  
						
						$pos = array('root_os','root_ts','hot_os','hot_ts','fill_os','fill_ts','cap_os','cap_ts');    
						foreach($Weld as $key => $v){
								
								if(in_array($key,$pos) &&!empty($v)){

									$WelderCount    =  $WelderCount+1;
									// $TotalWeldCount =  $TotalWeldCount+1;                                        
									array_push($WelderWeldPosition,$key);
								}
						} 

						$TotalWeldCount =  $TotalWeldCount+1;
						$Weldingrepair = \app\models\Ndt::find()->where(['weld_number'=>$Weld['weld_number'], 'kp'=>$Weld['kp']])->andWhere(['outcome' => 'Rejected'])->active()->all();
						
						if(!empty($Weldingrepair)){
							foreach($Weldingrepair as $s){
								$defect = json_decode($s['ndt_defact'],true);
								
								if(!empty($defect)){
								//	print_r($defect);die;
									foreach($defect as $k => $x){
										
										$dp =   str_replace(" ","_",strtolower($x['defect_position']));                                  
										if (in_array($x['defect_position'], $WelderWeldPosition) || in_array($dp, $WelderWeldPosition)  ){
											//   echo 1;
											$TotalWeldRepairCount = $TotalWeldRepairCount + 1;
											//isset($_GET['DEV'])?print_r($x['defect_position']):""; 
											if(isset($dd[$x['defects']])){
												$dd[$x['defects']] = $dd[$x['defects']]+1;
											}else{
												$dd[$x['defects']] = 1;
											}
											

										}
									}
								}                                   
							}                       
						}  
					}   
				}
				$k = str_replace(" ","_",$join['line_type']);
				$mainArray[$k]['total_weld']    = $TotalWeldCount;
				$mainArray[$k]['total_repair']  = $TotalWeldRepairCount;
				$mainArray['defect'] = $dd;

                            
		}
	//	isset($_GET['DEV'])?die:"";     
		return $mainArray;
	}

	public function getWeldRepairDaily($date){
		$MianLineWeldRepair = \app\models\Weldingrepair::find()
		->leftJoin('welding','welding_repair.weld_number=welding.weld_number
		 AND welding_repair.project_id = welding.project_id 
		 AND welding.is_active=1
		 AND welding.is_deleted=0 
		 AND welding_repair.kp=welding.kp 
		 AND welding.project_id='.Yii::$app->user->identity->project_id)
		->where(['welding_repair.date'=>$date,'welding.line_type' => 'Main Line'])->active()->asArray()->all();    
		$TieLineWeldRepair = \app\models\Weldingrepair::find()
		->leftJoin('welding','welding_repair.weld_number=welding.weld_number 
		AND welding_repair.project_id = welding.project_id 
		AND welding.is_active=1 AND welding.is_deleted=0
		AND welding_repair.kp=welding.kp 
	    AND welding.project_id='.Yii::$app->user->identity->project_id)
		->where(['welding_repair.date'=>$date,'welding.line_type' => 'Tie Line'])->active()->asArray()->all();

		return array('MianLineWeldRepair'=>count($MianLineWeldRepair),'TieLineWeldRepair'=>count($TieLineWeldRepair));
	}

	public function getWeldRepairAll(){
		
		$MianLineWeldRepair = \app\models\Weldingrepair::find()
		->leftJoin('welding','welding_repair.weld_number=welding.weld_number 
			AND welding_repair.kp=welding.kp 
			AND welding_repair.project_id = welding.project_id AND welding.is_active=1
			AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id
		 )
		->where(['welding.line_type' => 'Main Line'])->active()->asArray()->all(); 
		  
		$TieLineWeldRepair = \app\models\Weldingrepair::find()
		->leftJoin('welding','welding_repair.weld_number=welding.weld_number
		 AND welding_repair.project_id = welding.project_id 
		 AND welding.is_active=1
		 AND welding.is_deleted=0 
		 AND welding_repair.kp=welding.kp 
		 AND welding.project_id='.Yii::$app->user->identity->project_id)
		->where(['welding.line_type' => 'Tie Line'])->active()->asArray()->all();

		return array('MianLineWeldRepair'=>count($MianLineWeldRepair),'TieLineWeldRepair'=>count($TieLineWeldRepair));
	}

	public function getWeldCutToday($data){
		$MianLineCut = \app\models\Ndt::find()
		->leftJoin('welding','welding_ndt.weld_number=welding.weld_number
		AND welding_ndt.kp=welding.kp 
	    AND welding_ndt.project_id = welding.project_id
		AND welding.is_active=1 
		AND welding.is_deleted=1 AND 
		welding.project_id='.Yii::$app->user->identity->project_id)
		->where(['welding_ndt.date'=>$data,'welding.line_type'=>'Main Line','welding_ndt.outcome'=>'Cut Out'])->active()->asArray()->all();    
		
		$TieLineCut = \app\models\Ndt::find()
		->leftJoin('welding','welding_ndt.weld_number=welding.weld_number 
		AND welding_ndt.kp=welding.kp 
		AND welding_ndt.project_id = welding.project_id 
		AND welding.is_active=1 AND welding.is_deleted=1 AND welding.project_id='.Yii::$app->user->identity->project_id)
		->where(['welding_ndt.date'=>$data,'welding.line_type'=>'Tie Line','welding_ndt.outcome'=>'Cut Out'])->active()->asArray()->all();

		return array('MianLineCut'=>count($MianLineCut),'TieLineCut'=>count($TieLineCut));
	}

	public function getWeldCutAll($filterType, $dateRange){
		// $MianLineCut = \app\models\Ndt::find()
		// ->leftJoin('welding','welding_ndt.weld_number=welding.weld_number AND welding_ndt.kp=welding.kp  
		// AND  welding_ndt.project_id = welding.project_id
		// AND welding.is_active=1 AND welding.is_deleted=1 AND welding.project_id='.Yii::$app->user->identity->project_id)
		// ->where(['welding.line_type'=>'Main Line','welding_ndt.outcome'=>'Cut Out'])->active()->asArray()->all();    
		
		// $TieLineCut = \app\models\Ndt::find()
		// ->leftJoin('welding','welding_ndt.weld_number=welding.weld_number AND welding_ndt.kp=welding.kp 
		// AND welding_ndt.project_id = welding.project_id 
		// AND welding.is_active=1 AND welding.is_deleted=1 AND welding.project_id='.Yii::$app->user->identity->project_id)
		// ->where(['welding.line_type'=>'Tie Line','welding_ndt.outcome'=>'Cut Out'])->active()->asArray()->all();

		if($filterType == 'weekly') {
			$dateExplode = explode('-', $dateRange);
			$startDate = date('Y-m-d', strtotime($dateExplode[0]));
			$endDate = date('Y-m-d', strtotime($dateExplode[1]));
		} else if($filterType == 'daily') {
			$startDate = date('Y-m-d', strtotime($dateRange));
			$endDate = date('Y-m-d', strtotime("+1 day", strtotime($startDate)));
		} else {
			$endDate = date('Y-m-d');
			$startDate = date('Y-m-d',strtotime("-2 year",strtotime($endDate)));
		}
		
		$MianLineCut = \app\models\Welding::find()->select('id')->where(['line_type' => 'Main Line', 'has_been_cut_out' => 'Yes'])->andWhere(['between', 'date', $startDate, $endDate])->active()->count();
		$TieLineCut = \app\models\Welding::find()->select('id')->where(['line_type' => 'Tie Line', 'has_been_cut_out' => 'Yes'])->andWhere(['between', 'date', $startDate, $endDate])->active()->count();

		return array('MianLineCut' => $MianLineCut, 'TieLineCut' => $TieLineCut);
	}

	public function getWeldNdtToday($date){
		$MianLineNdt = \app\models\Ndt::find()
		->leftJoin('welding','welding_ndt.weld_number=welding.weld_number AND welding_ndt.kp=welding.kp  AND welding_ndt.project_id = welding.project_id AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)
		->where(['welding_ndt.date'=>$date,'welding.line_type'=>'Main Line'])->active()->asArray()->all();

		$TieLineNdt = \app\models\Ndt::find()
		->leftJoin('welding','welding_ndt.weld_number=welding.weld_number AND welding_ndt.kp=welding.kp  AND welding_ndt.project_id = welding.project_id
		AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)
		->where(['welding_ndt.date'=>$date,'welding.line_type'=>'Tie Line'])->active()->asArray()->all();

		return array('MianLineNdt'=>count($MianLineNdt),'TieLineNdt'=>count($TieLineNdt));
	}

	public function getWeldNdtAll($filterType, $dateRange){
		if($filterType == 'weekly') {
			$dateExplode = explode('-', $dateRange);
			$startDate = date('Y-m-d', strtotime($dateExplode[0]));
			$endDate = date('Y-m-d', strtotime($dateExplode[1]));
		} else if($filterType == 'daily') {
			$startDate = date('Y-m-d', strtotime($dateRange));
			$endDate = date('Y-m-d', strtotime("+1 day", strtotime($startDate)));
		} else {
			$endDate = date('Y-m-d');
			$startDate = date('Y-m-d',strtotime("-2 year",strtotime($endDate)));
		}
		
		$MianLineNdt = \app\models\Ndt::find()
		->leftJoin('welding','welding_ndt.weld_number=welding.weld_number AND welding_ndt.kp=welding.kp  AND welding_ndt.project_id = welding.project_id AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)
		->where(['welding.line_type'=>'Main Line'])->andWhere(['between', 'welding_ndt.date', $startDate, $endDate])->active()->asArray()->all();

		$TieLineNdt = \app\models\Ndt::find()
		->leftJoin('welding','welding_ndt.weld_number=welding.weld_number AND welding_ndt.kp=welding.kp  AND welding_ndt.project_id = welding.project_id
		AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)
		->where(['welding.line_type'=>'Tie Line'])->andWhere(['between', 'welding_ndt.date', $startDate, $endDate])->active()->asArray()->all();

		return array('MianLineNdt'=>count($MianLineNdt),'TieLineNdt'=>count($TieLineNdt));
	}

	public function getWeldCoatedToday($date){
		$MianLineCoated = \app\models\Production::find()
		->leftJoin('welding','welding_coating_production.weld_number=welding.weld_number AND welding_coating_production.kp=welding.kp AND welding_coating_production.project_id = welding.project_id AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)
		->where(['welding_coating_production.date'=>$date,'welding.line_type'=>'Main Line'])->active()->asArray()->all();

		$TieLineCoated = \app\models\Production::find()
		->leftJoin('welding','welding_coating_production.weld_number=welding.weld_number AND welding_coating_production.kp=welding.kp AND welding_coating_production.project_id = welding.project_id AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)
		->where(['welding_coating_production.date'=>$date,'welding.line_type'=>'Tie Line'])->active()->asArray()->all();

		return array('MianLineCoated'=>count($MianLineCoated),'TieLineCoated'=>count($TieLineCoated));
	}

	public function getWeldCoatedAll($filterType, $dateRange){
		if($filterType == 'weekly') {
			$dateExplode = explode('-', $dateRange);
			$startDate = date('Y-m-d', strtotime($dateExplode[0]));
			$endDate = date('Y-m-d', strtotime($dateExplode[1]));
		} else if($filterType == 'daily') {
			$startDate = date('Y-m-d', strtotime($dateRange));
			$endDate = date('Y-m-d', strtotime("+1 day", strtotime($startDate)));
		} else {
			$endDate = date('Y-m-d');
			$startDate = date('Y-m-d',strtotime("-2 year",strtotime($endDate)));
		}

		$MianLineCoated = \app\models\Production::find()
		->leftJoin('welding','welding_coating_production.weld_number=welding.weld_number AND welding_coating_production.kp=welding.kp AND welding_coating_production.project_id = welding.project_id AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)
		->where(['welding.line_type'=>'Main Line'])->andWhere(['between', 'welding_coating_production.date', $startDate, $endDate])->active()->asArray()->all();

		$TieLineCoated = \app\models\Production::find()
		->leftJoin('welding','welding_coating_production.weld_number=welding.weld_number AND welding_coating_production.kp=welding.kp AND welding_coating_production.project_id = welding.project_id AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)
		->where(['welding.line_type'=>'Tie Line'])->andWhere(['between', 'welding_coating_production.date', $startDate, $endDate])->active()->asArray()->all();

		return array('MianLineCoated'=>count($MianLineCoated),'TieLineCoated'=>count($TieLineCoated));
	}

	//repair by defect type
	public function getRepairByDefectType($type,$dateArray = array()){
		if(!empty($dateArray)){
			$start = date('Y-m-d',strtotime($dateArray[0]));
			$end = date('Y-m-d',strtotime($dateArray[1]));
			
			$defectData = \app\models\Welding::find()->select('ndt_defects')->where(['between', 'date',$start, $end ])->active()->asArray()->all();
		} else {
			$defectData = \app\models\Welding::find()->select('ndt_defects')->active()->asArray()->all();
		}
		
		$totalCount = 0;
	
		if(!empty($defectData)){

			foreach($defectData as $defect){
				$decodeDefect = json_decode($defect['ndt_defects'], true);
				if(!empty($decodeDefect)){
					
					foreach($decodeDefect as $defects){
						if(!empty($defects) && $type==$defects['defects']){
							$totalCount++;
						}
					}
				}	
				
			}
			return $totalCount;
		}
		return 0;
	}
	public function exportImport($section){
		if(!Yii::$app->general->isAllowed()){
			$url = Url::to(['/pipe/pipe/csv-'.$section]);
			$cstClass = '';
			if($section == 'import'){
				$cstClass = 'btn-sm';
			}
		?>
		<button type="button" class="mr-1 mb-1 float-right btn <?= $cstClass; ?> btn-raised btn-outline-secondary btn-min-width" style="position:relative">
			<form method="post" enctype="multipart/form-data" action="<?=$url;?>" id="import-csv">
				<i class="fa fa-upload"></i>
				<input type="file" name="CsvImport[file]" class="inp-file" action="<?=$url;?>"> <?= Yii::$app->trans->getTrans('Import (.csv)'); ?>
				<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />  
			</form>
        </button>                         
        <a class="pull-right mr-1 mb-1 btn <?= $cstClass; ?> btn-raised btn-outline-orange btn-min-width" href="<?= Url::to("@web/".$section.'.csv'); ?>" target="_blank" data-pjax=0><i class="fa fa-file-o ex-icon"></i><?= Yii::$app->trans->getTrans('Sample CSV File'); ?></a>
		<?php
		}
	}

	public function ndtImport(){
		if(!Yii::$app->general->isAllowed()){
			$url = Url::to(['/welding/ndt/csv-import']);
		?>
		<button type="button" class="mr-1 mb-1 float-right btn btn-raised btn-outline-secondary btn-min-width" style="position:relative">
			<form method="post" enctype="multipart/form-data" action="<?= $url;?>" id="import-csv">
				<i class="fa fa-upload"></i>
				<input type="file" name="CsvImport[file]" class="inp-file" action="<?= $url;?>"> <?= Yii::$app->trans->getTrans('Import (.csv)'); ?>
				<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
			</form>
		</button>
		<a class="pull-right mr-1 mb-1 btn btn-raised btn-outline-orange btn-min-width" href="<?= Url::to("@web/".'ndt_import_sample.csv'); ?>" target="_blank" data-pjax=0><i class="fa fa-file-o ex-icon"></i><?= Yii::$app->trans->getTrans('Sample CSV File'); ?></a>
		<?php
		}
	}

	public function makeJsonDecode($json){
		$jsnDec = $json;
		$count = 0;
		while(is_string($jsnDec) && $count < 6){
			$jsnDec = json_decode($jsnDec, true);
			$count++;
		}

		return $jsnDec;
	}

	public function getFilters($query = false){
		$params = $_GET;
		if(!empty($params)){
			if($query){
				return http_build_query($params);
			} else {
				$queryParams = [];
				foreach($params as $key => $val){
					if(is_array($val)){
						foreach($val as $k => $v){
							$queryParams[$key.'['.$k.']'] = $v;
						}
					} else {
						$queryParams[$key] = $val;
					}
				}
				return $queryParams;
			}
		} else {
			return '';
		}
	}

	public function getSummaryText($data){
		$start = $data['start'];
		$end = $data['end'];
		$total = $data['total'];

		return Yii::$app->trans->getTrans('Showing')." <b>".$start." - ".$end."</b> ".Yii::$app->trans->getTrans('of')." <b>".$total."</b> ".Yii::$app->trans->getTrans('items');
	}
}
?>