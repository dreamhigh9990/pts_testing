<?php
namespace app\components;
use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Component;
class Anomaly extends Component
{

	public function makeAnomally($ExistData,$model){		
		$ExistData->is_active = 0;
		$ExistData->is_anomally  = "Yes";
		$ExistData->why_anomally = "Add new data with same pipe number";
		$ExistData->save();
		


		$model->is_anomally  = "Yes";
		$model->is_active    = 1;
		$model->why_anomally = "Pipe number is already assigned with this section";
		return $model;
	}
	public function pipe_anomaly($model,$warning=false){

		if ($model->isNewRecord){			
			if(!empty($model->pipe_number)){
			
				$ExistData = \app\models\Pipe::find()->where(['pipe_number'=>$model->pipe_number])->active()->one();					
	
				if(!empty($ExistData)){	
						if(Yii::$app->controller->id == "sync"){
							$model->is_anomally  = "Yes";
							$model->is_active    = 0;
							return $model;
						}				
						if($warning){
							return ['status'=>true,'message'=>'This pipe number is already exist with current project.']	;					
						}else{						
							$model  = $this->makeAnomally($ExistData,$model);
						}					
				} else {
					//logic by sagar
					if(Yii::$app->controller->id=="sync"){
						if(!$model->pups){
							$model->is_anomally  = "Yes";
							$model->is_active    =  0;
							$model->why_anomally = "Pipe number enter from mobile app so default is anomaly.";
						}
					}
				}
			}
		}else{	
			$oldAttributes = $model->getOldAttributes();
			if($oldAttributes['pipe_number'] != $model->pipe_number){
				$ExistData = \app\models\Pipe::find()->where(['pipe_number'=>$model->pipe_number])->active()->one();
				if(!empty($ExistData)){	
					if($warning){
						return ['status'=>true,'message'=>'This pipe number is already exist with current project.']	;				
					}else{
						$model  = $this->makeAnomally($ExistData,$model);
					}						
				}
			}
		}
		
		return $model;
	}
	public function pipe_reception_anomaly($model,$warning=false){	
		if ($model->isNewRecord){		
			$PipeExist = \app\models\Pipe::find()->where(['pipe_number'=>$model->pipe_number])->active()->one();	
			
			$CoreYieldStrength = \app\models\TaxonomyValue::find()->where(['taxonomy_id'=>12,'project_id'=>Yii::$app->user->identity->project_id])->active()->one();				
			if(!empty($CoreYieldStrength['value'])){
				$Pipe =\app\models\Pipe::find()->where(['pipe_number'=>$model->pipe_number])->active()->one();

				if( $warning && !empty($Pipe->yeild_strength) && $CoreYieldStrength['value'] > $Pipe->yeild_strength){
					return ['status'=>true,'message'=>'Yield Strength of the selected pipe number is below the Yield Strength Threshold.']	;					
				}
			}

			if(empty($PipeExist)){
				if($warning){
					return ['status'=>true,'message'=>'Pipe number is not available in pipe list for this project.']	;					
				}else{	
					$model->is_anomally  = "Yes";
					$model->is_active    =  0;
					$model->why_anomally = "Pipe number is not available in pipe list for this project.";
				}
			}
			$PipeExistInRec = \app\models\Reception::find()->where(['pipe_number'=>$model->pipe_number])->active()->one();					
			if(!empty($PipeExistInRec)){
				if($warning){
					return ['status'=>true,'message'=>'Pipe number is already available in reception list for this project.']	;					
				}else{
					$PipeExistInRec->is_anomally  = "Yes";
					$PipeExistInRec->is_active    =  0;
					$PipeExistInRec->why_anomally = "Pipe number receipted again for this project.";
					$PipeExistInRec->save(false);

					$model->is_anomally  = "Yes";
					$model->is_active    =  1;
					$model->why_anomally = "Pipe number is already receipted for this project.";
				}
			}
		}
		return $model;
	}
	public function pipe_transfer_anomaly($model,$warning=false){	
		if ($model->isNewRecord){	

			$CoreYieldStrength = \app\models\TaxonomyValue::find()->where(['taxonomy_id'=>12,'project_id'=>Yii::$app->user->identity->project_id])->active()->one();				
			if(!empty($CoreYieldStrength['value'])){
				$Pipe =\app\models\Pipe::find()->where(['pipe_number'=>$model->pipe_number])->active()->one();

				if($warning && !empty($Pipe->yeild_strength) && $CoreYieldStrength['value'] > $Pipe->yeild_strength){
					return ['status'=>true,'message'=>'Yield Strength of the selected pipe number is below the Yield Strength Threshold.']	;					
				}
			}

			$PipeExist = \app\models\Pipe::find()->where(['pipe_number'=>$model->pipe_number])->active()->one();								
			if(empty($PipeExist)){
				if($warning){
					return ['status'=>true,'message'=>'Pipe number is not available in pipe list for this project.']	;					
				}else{
					$model->is_anomally  = "Yes";
					$model->is_active    =  0;
					$model->why_anomally = "Pipe number is not available in pipe list for this project.";
				}
			}
		}
		return $model;
	}
	public function pipe_stringing_anomaly($model,$warning=false){	
		if ($model->isNewRecord){
			
			// as per client says - add pipe number validation for stringing
			$PipeExist = \app\models\Pipe::find()->where(['pipe_number'=>$model->pipe_number])->active()->one();
			if(empty($PipeExist)){	
				if($warning){
					return ['status'=>true,'message'=>'Pipe number does not available in pipe list for this project.']	;					
				}else{			
					$model->is_anomally  = "Yes";
					$model->is_active    =  0;
					$model->why_anomally = "Pipe number does not available in pipe list for this project.";
				}
			}

			$CoreYieldStrength = \app\models\TaxonomyValue::find()->where(['taxonomy_id'=>12,'project_id'=>Yii::$app->user->identity->project_id])->active()->one();				
			if(!empty($CoreYieldStrength['value'])){
				$Pipe =\app\models\Pipe::find()->where(['pipe_number'=>$model->pipe_number])->active()->one();

				if($warning && !empty($Pipe->yeild_strength) && $CoreYieldStrength['value']  >  $Pipe->yeild_strength){
					return ['status'=>true,'message'=>'Yield Strength of the selected pipe number is below the Yield Strength Threshold.']	;					
				}
			}
			
			// as per client say commenting below code
			// $PipeInReception = \app\models\Reception::find()->where(['pipe_number'=>$model->pipe_number,'location'=>$model->location])->active()->one();								
			// if(empty($PipeInReception)){				
			// 	if($warning){
			// 		return ['status'=>true,'message'=>'Pipe number does not available in reception list for this project.']	;					
			// 	}else{
			// 		$model->is_anomally  = "Yes";
			// 		$model->is_active    =  0;
			// 		$model->why_anomally = "Pipe number does not available in reception list for this project.";
			// 	}
			// }
			$PipeExistInStringing = \app\models\Stringing::find()->where(['pipe_number'=>$model->pipe_number])->active()->one();					
			if(!empty($PipeExistInStringing)){
				if($warning){
					return ['status'=>true,'message'=>'Pipe number is already strung for this project.']	;					
				}else{
					$PipeExistInStringing->is_anomally  = "Yes";
					$PipeExistInStringing->is_active    =  0;
					$PipeExistInStringing->why_anomally = "Pipe number strung again for this project.";
					$PipeExistInStringing->save(false);

					$model->is_anomally  = "Yes";
					$model->is_active    =  1;
					$model->why_anomally = "Pipe number is already strung for this project.";
				}
			}
		}
		return $model;
	}
	public function pipe_bending_anomaly($model,$warning=false){	
		if ($model->isNewRecord){		
			$PipeExist = \app\models\Pipe::find()->where(['pipe_number'=>$model->pipe_number])->active()->one();								
			if(empty($PipeExist)){	
				if($warning){
					return ['status'=>true,'message'=>'Pipe number does not available in pipe list for this project.']	;					
				}else{			
					$model->is_anomally  = "Yes";
					$model->is_active    =  0;
					$model->why_anomally = "Pipe number does not available in pipe list for this project.";
				}
			}
		}
		return $model;
	}
	public function pipe_cutting_anomaly($model,$warning=false){	
		if ($model->isNewRecord){		
			$PipeNo = $model->pipe_number;				
			// $PipeExist = \app\models\Pipe::find()->where(['OR',['pipe_number'=>$PipeNo],['pipe_number'=>$PipeNo.'/1'],['pipe_number'=>$PipeNo.'/2']])->active()->one();								
			$PipeExist = \app\models\Pipe::find()->where(['pipe_number' => $PipeNo])->active()->one();
			if(empty($PipeExist)){		
				if($warning){
					return ['status'=>true,'message'=>'Pipe number does not available in pipe list for this project. This record will be added to the anomaly list.'];
				}else{
					$model->is_anomally  = "Yes";
					$model->is_active    =  0;
					$model->why_anomally = "Pipe number does not available in pipe list for this project.";
				}
			} else {
				$getPipeCutData = $model::find()->where(['pipe_number' => $PipeNo])->asArray()->one();
				if(!empty($getPipeCutData)){
					if($warning){
						return ['status' => true, 'message' => 'The pipe number has already been cut.'];
					}
				}
			}
		}
		return $model;
	}
	public function pipe_cleargrade_anomaly($model,$warning=false){
		if ($model->isNewRecord){	
            $Cleargrade = \app\models\Cleargrade::find()->where([
				'AND',
				[
					'OR',
					['>', 'start_kp', (float)$model->start_kp],
					['>', 'end_kp', (float)$model->start_kp]
				],
				[
					'OR',
					['<', 'start_kp', (float)$model->end_kp],
					['<', 'end_kp', (float)$model->end_kp]
				]
			])->active()->one();
            if(!empty($Cleargrade)){
				if($warning){
					return ['status'=>true,'message'=>'The KP range entered already exists. Please select a different range to Clear & Grade'];
				} else {
					$model->is_anomally  = "Yes";
					$model->is_active    =  0;
					$model->why_anomally = 'The KP range entered already exists. Please select a different range to Clear & Grade';
				}
            }
		}
		return $model;
	}
	public function cable_anomaly($model,$warning=false){
		if ($model->isNewRecord){	
			if(!empty($model->drum_number)){
				$ExistData = \app\models\Cable::find()->where(['drum_number'=>$model->drum_number])->active()->one();					
				if(!empty($ExistData)){
					$ExistData->is_active = 0;
					$ExistData->is_anomally  = "Yes";
					$ExistData->why_anomally = "Add new data with same drum number";
					$ExistData->save(false);
					if($warning) {				
						return ['status'=>true,'message'=>"This drum number is already exist."];
					} else {
						$model->is_anomally  = "Yes";
						$model->is_active    = 1;
						$model->why_anomally = "This drum number is already exist.";
					}
				}	
			}
		}else{	
			$oldAttributes = $model->getOldAttributes();
			if($oldAttributes['drum_number'] != $model->drum_number){
				$ExistData = \app\models\Cable::find()->where(['drum_number'=>$model->drum_number])->active()->one();
				if(!empty($ExistData)){	
										
					$ExistData->is_active = 0;
					$ExistData->is_anomally  = "Yes";
					$ExistData->why_anomally = "Add new data with same drum number";
					$ExistData->save(false);
					if($warning) {				
						return ['status'=>true,'message'=>"Drum number is already assigned with this section"];
					} else {
						$model->is_anomally  = "Yes";
						$model->is_active    = 1;
						$model->why_anomally = "Drum number is already assigned with this section";
					}
				}
			}
		}
		return $model;
	}
	public function cable_stringing_anomaly($model,$warning=false){
		if ($model->isNewRecord){		
		
			$CabStringing = \app\models\Cable::find()->where(['drum_number'=>$model->drum_number])->active()->one();								
			if(empty($CabStringing)){
				if($warning) {				
					return ['status'=>true,'message'=>"Drum number is not available in Drum list for this project."];
				} else {				
					$model->is_anomally  = "Yes";
					$model->is_active    =  0;
					$model->why_anomally = "Drum number is not available in Drum list for this project.";
				}
			}
			$DrumExistInStringing = \app\models\CabStringing::find()->where(['drum_number'=>$model->drum_number])->active()->one();					
			if(!empty($DrumExistInStringing)){
				if($warning) {
					return ['status'=>true,'message'=>"Drum number strung again for this project"];
				} else {
					$DrumExistInStringing->is_anomally  = "Yes";
					$DrumExistInStringing->is_active    =  0;
					$DrumExistInStringing->why_anomally = "Drum number strung again for this project.";
					$DrumExistInStringing->save(false);

					$model->is_anomally  = "Yes";
					$model->is_active    =  1;
					$model->why_anomally = "Drum number is already strung for this project.";
				}
			}
		}
		return $model;
	}
	public function cable_splicing_anomaly($model,$warning=false){
		if ($model->isNewRecord){		
			$CabStringing = \app\models\CabStringing::find()->where(['drum_number'=>$model->drum_number])->active()->one();	
			if(empty($CabStringing)){
				if($warning){
					return ['status'=>true,'message'=>"Drum number is not available in Cable Stringing list for this project."];
				} else {
					$model->is_anomally  = "Yes";
					$model->is_active    =  0;
					$model->why_anomally = "Drum number is not available in Cable Stringing list for this project.";
				}
			}else{
				$CabStringing = \app\models\CabStringing::find()->where(['drum_number'=>$model->next_drum])->active()->one();								
				if(empty($CabStringing)){
					if($warning){
						return ['status'=>true,'message'=>"Next Drum number is not available in Cable Stringing list for this project."];
					} else {				
						$model->is_anomally  = "Yes";
						$model->is_active    =  0;
						$model->why_anomally = "Next Drum number is not available in Cable Stringing list for this project.";
					}
				}
			}

			$DrumExistInCabSplicing = \app\models\CabSplicing::find()->where(['drum_number'=>$model->drum_number])->active()->one();					
			if(!empty($DrumExistInCabSplicing)){
				if($warning){
					return ['status'=>true,'message'=>"Drum number splice again for this project."];
				} else {
					$DrumExistInCabSplicing->is_anomally  = "Yes";
					$DrumExistInCabSplicing->is_active    =  0;
					$DrumExistInCabSplicing->why_anomally = "Drum number splice again for this project.";
					$DrumExistInCabSplicing->save(false);

					$model->is_anomally  = "Yes";
					$model->is_active    =  1;
					$model->why_anomally = "Drum number is already splice for this project.";
				}
			}
			$NextDrumExistInCabSplicing = \app\models\CabSplicing::find()->where(['next_drum'=>$model->next_drum])->active()->one();					
			if(!empty($NextDrumExistInCabSplicing)){
				if($warning){
					return ['status'=>true,'message'=>"Next Drum number splice again for this project."];
				} else {
					$NextDrumExistInCabSplicing->is_anomally  = "Yes";
					$NextDrumExistInCabSplicing->is_active    =  0;
					$NextDrumExistInCabSplicing->why_anomally = "Next Drum number splice again for this project.";
					$NextDrumExistInCabSplicing->save(false);

					$model->is_anomally  = "Yes";
					$model->is_active    =  1;
					$model->why_anomally = "Next Drum number is already splice for this project.";
				}
			}

		}
		return $model;
	}	
	public function welding_anomaly($model,$warning=false){		
		if($model->isNewRecord){
				
			######################### Pipe Number #########################################

					#######################################################################
					##		1.Check Pipe Number is Strung at Given KP ?                  ##
					#######################################################################
					# Cases : Kp = 0,0.2,0.12 = Should Check Only 0
					$strungPipe = \app\models\Stringing::find()->where(['AND',['pipe_number'=>$model->pipe_number],
					['=','FLOOR(kp)',floor($model->kp)]])->active()->one();
					if(empty($strungPipe)){
						if($warning){
							return ['status'=>true,'message'=>"This pipe number was not strung kp -".$model->kp]	;					
						}else{
							$model->is_anomally  = "Yes";	
							$model->is_active  = 0;	
							$model->why_anomally = "This pipe number was not strung kp-".$model->kp;
							return $model;
						}
					}
					#######################################################################
					##		2.Check Pipe Number is Already welded at anywhere?           ##
					#######################################################################
					$welding =  \app\models\Welding::find()->where(['pipe_number' => $model->pipe_number, 'has_been_cut_out' => 'No'])->active()->one();
					if(!empty($welding)){
						if($warning){
							return ['status'=>true,'message'=>"This pipe number is already welded."]	;					
						}else{
							$model->is_anomally  = "Yes";	
							$model->why_anomally = "This pipe number was already in welding";	

							$welding->is_anomally = "Yes";
							$welding->why_anomally = "This pipe number was already in welding ".$model->kp;
							$welding->is_active  = 0;	
							$welding->save();
							return $model;
						}
					}
			######################### Next Pipe Number #####################################

					#######################################################################
					##		1.Check Next Pipe Number is Strung at Given KP or KP+1 ?     ##
					#######################################################################
					# Cases : Kp = 0,0.2,0.12 = Should Check Only 0
					$strungPipe = \app\models\Stringing::find()->where([ 'OR',
																	 ['pipe_number'=>$model->next_pipe,'FLOOR(kp)'=>floor($model->kp) ],
																	 ['pipe_number'=>$model->next_pipe,'FLOOR(kp)'=>floor($model->kp)+1]
																   ])->active()->one();
					if(empty($strungPipe)){
					
						if($warning){
							return ['status'=>true,'message'=>"This next pipe number was not strung at kp-".$model->kp.' or next kp-'.(floor($model->kp)+1)]	;					
						}else{
							$model->is_anomally  = "Yes";	
							$model->is_active    = 0;	
							$model->why_anomally = "This next pipe number was not strung at kp-".$model->kp.' or next kp-'.(floor($model->kp)+1);
							return $model;				
						}
					}
					#######################################################################
					##		2.Check Next Pipe Number is Already welded at anywhere?      ##
					#######################################################################
					$welding =  \app\models\Welding::find()->where(['next_pipe' => $model->next_pipe, 'has_been_cut_out' => 'No'])->active()->one();
					if(!empty($welding)){
						if($warning){
								return ['status'=>true,'message'=>"This next pipe number was already welded."];					
						}else{
							$model->is_anomally  = "Yes";	
							$model->why_anomally = "This next pipe was already in welding";	


							$welding->is_anomally = "Yes";
							$welding->why_anomally = "This next pipe was already in welding ".$model->kp;
							$welding->is_active  = 0;	
							$welding->save();
							return $model;
						}
					}

					#######################################################################
					##		Check weld number already exist for given kp                 ##
					#######################################################################

					$list = \app\models\Welding::find()->where(['kp' => $model->kp, 'weld_number' => $model->weld_number, 'has_been_cut_out' => 'No'])->active()->one();
					if(!empty($list)){
						if($warning){
							return ['status'=>true,'message'=>"This weld number was already assign for KP ".$model->kp]	;					
						}else{
							$model->is_anomally  = "Yes";	
							$model->why_anomally = "This weld number was already assign for KP ".$model->kp;
							
							
							$list->is_anomally = "Yes";
							$list->why_anomally = "Duplicate Weld Number at same KP ".$model->kp;
							$list->is_active  = 0;	
							$list->save();
							return $model;
						}
					}
			
		}
		return $model;
	}
	public function welding_param_anomaly($model,$ExistModel,$warning=false){		
		if($model->isNewRecord){	
			#######################################################################
			##		1 Check Weld Number Is Exist In Welding For Given Kp (Exact) ##
			#######################################################################		
			$list = \app\models\Welding::find()->where(['AND',['=', 'weld_number', $model->weld_number],['=','kp', $model->kp]])->active()->asArray()->one();
		    if(empty($list)){				
				if($warning){
					return ['status'=>true,'message'=>"Weld number does not exist in Welding  for kp-".$model->kp]	;					
				}else{
					$model->is_anomally     = "Yes";	
					$model->is_active       =  0;
					$model->why_anomally    = "Weld number does not exist in Welding  for  kp-".$model->kp;
				}
			}	
			#######################################################################
			##	2 Check Weld Number Is Exist In Paramtere For Given Kp (Exact)   ##
			#######################################################################	
			$Parameter = \app\models\Parameter::find()->where(['kp'=>$model->kp,'weld_number'=>$model->weld_number])->active()->one();
			if(!empty($Parameter)){
				if($warning){
					return ['status'=>true,'message'=>"This weld number is already exist in Parameter Check."]	;					
				}else{
					$Parameter->is_anomally  = "Yes";	
					$Parameter->is_active  = 0;	
					$Parameter->why_anomally = "Duplicate entries";	
					$Parameter->save(false);

					$model->is_anomally     = "Yes";	
					$model->is_active       =  1;
					$model->why_anomally    = "This weld number is already exist in Parameter Check";
				}
			}
		};
		return $model;
	}
	public function welding_repair_anomaly($model,$ExistModel,$warning=false){		
		if($model->isNewRecord){			
			// $list = \app\models\Ndt::find()->where([
			// 		'AND',
			// 		['=', 'weld_number', $model->weld_number],
			// 		['=','kp', $model->kp],
			// 		['OR',['=','outcome', "Rejected"],['=','outcome', "Repaired"], ['=','outcome', "Accepted"]]
			// 	])->active()->asArray()->one();
			// if(empty($list)){
			// 	if($warning){
			// 		return ['status'=>true,'message'=>"This weld number does not exist in NDT with a Rejected Outcome for KP".$model->kp]	;					
			// 	}else{
			// 		$model->is_anomally  = "Yes";
			// 		$model->is_active    =  0;
			// 		$model->why_anomally = "This weld number does not exist in NDT with a Rejected Outcome for KP".$model->kp;
			// 	}
			// }
			if(Yii::$app->controller->id == "sync"){
				$InputData 		= Yii::$app->getRequest()->getBodyParams(); 
				$data		    = $InputData['data'];
				if(!empty($InputData['data']) && !empty($InputData['data']['welding_repair'])){
						// print_r($InputData['data']);
						$Repair = count($InputData['data']['welding_repair']);
						$Ndt    = count($InputData['data']['welding_ndt']);
				}else{
					$Ndt = 0;
					$Repair = 0;
				}
			}else{
				$Ndt 	= \app\models\Ndt::find()->where([
					'AND',
					['=','weld_number',$model->weld_number],
					['=','kp',$model->kp],
					['OR',['=','outcome', "Rejected"],['=','outcome', "Accepted"]],
					['=','main_weld_id',$model->main_weld_id]
				])->active()->count();
				$Repair = \app\models\Weldingrepair::find()->where(['weld_number'=>$model->weld_number,'kp'=>$model->kp,'main_weld_id' => $model->main_weld_id])->active()->count();		
			}	
			// if(Yii::$app->controller->id != "sync"){
			// 	if($Ndt != $Repair+1){
			// 		if($warning){
			// 			return ['status'=>true,'message'=>"This weld has does not require repair at this time. Please ensure there is an NDT record for this weld and only if the weld is rejected can a repair be performed."];					
			// 		}else{
			// 			$model->is_anomally     = "Yes";	
			// 			$model->is_active       =  0;
			// 			$model->why_anomally    = "This weld has does not require repair at this time. Please ensure there is an NDT record for this weld and only if the weld is rejected can a repair be performed.";
			// 		}
			// 	}
			// }
		};
		return $model;
	}	
	public function welding_ndt_anomaly($model,$ExistModel,$warning=false){		
		if($model->isNewRecord){
			if(Yii::$app->controller->id == "sync"){
				$list = \app\models\Welding::find()->where(['AND', ['=', 'weld_number', $model->weld_number], ['=', 'kp',  $model->kp]])->active()->asArray()->one();
			}else{
				$list = \app\models\Welding::find()->where(['AND', ['=', 'weld_number', $model->weld_number], ['=', 'kp',  $model->kp], ['=', 'has_been_cut_out', 'No']])->active()->asArray()->one();
			}
			if(empty($list)){				
				if($warning){
					return ['status'=>true,'message'=>"Weld number does not exist in Welding  for kp-".$model->kp]	;					
				}else{
					$model->is_anomally     = "Yes";	
					$model->is_active       =  0;
					$model->why_anomally    = "Weld number does not exist in Welding  for  kp-".$model->kp;
				}
			}

			$Ndt 	= \app\models\Ndt::find()->where(['weld_number'=>$model->weld_number,'kp'=>$model->kp,'outcome'=>'Accepted', 'main_weld_id' => $model->main_weld_id])->active()->asArray()->one();
			if(!empty($Ndt['id'])){
				if($warning){
					return ['status'=>true,'message'=>"This weld has already been accepted through NDT"];					
				}else{
					$model->is_anomally     = "Yes";	
					$model->is_active       =  0;
					$model->why_anomally    = "This weld has already been accepted through NDT";
				}
			}

			// if(Yii::$app->controller->id == "sync"){
			// 	$InputData 		= Yii::$app->getRequest()->getBodyParams(); 
			// 	$data		    = $InputData['data'];
			// 	if(!empty($InputData['data']) && !empty($InputData['data']['welding_repair'])){
			// 			// print_r($InputData['data']);
			// 			$Repair = count($InputData['data']['welding_repair']);
			// 			$Ndt = count($InputData['data']['welding_ndt']);
			// 			// $RejectedNdT = [];
			// 			// if(!empty($InputData['data']['welding_ndt'])){
			// 			// 	foreach($InputData['data']['welding_ndt'] as $k=> $v){
			// 			// 		if($v['outcome']=="Rejected"){
			// 			// 			array_push($RejectedNdT,$v);
			// 			// 		}
			// 			// 	}
			// 			// }
			// 			// $Ndt = count($RejectedNdT);
			// 	}else{
			// 		$Ndt = 0;
			// 		$Repair = 0;
			// 	}
			// }else{
			// 	$Ndt 	= \app\models\Ndt::find()->where(['weld_number' => $model->weld_number, 'kp' => $model->kp, 'main_weld_id' => $model->main_weld_id])->active()->count();			
			// 	$Repair = \app\models\Weldingrepair::find()->where(['weld_number' => $model->weld_number, 'kp' => $model->kp, 'main_weld_id' => $model->main_weld_id])->active()->count();	
			// }
			
			// if($Ndt != $Repair){
			// 	if($warning){
			// 		return ['status'=>true,'message'=>"This Weld has not been repaired yet. Please perform a repair before doing NDT again."];					
			// 	}else{
			// 		$model->is_anomally     = "Yes";	
			// 		$model->is_active       =  0;
			// 		$model->why_anomally    = "This Weld has not been repaired yet. Please perform a repair before doing NDT again.";
			// 	}
			// }
		}
		return $model;
	}	
	public function welding_production_anomaly($model,$ExistModel,$warning=false){		
		if($model->isNewRecord){			
			$list = \app\models\Ndt::find()->where(['AND',['=', 'weld_number', $model->weld_number],['=','kp', $model->kp],['=','outcome', "Accepted"]])->active()->asArray()->one();
			if(empty($list)){

				if($warning){
					return ['status'=>true,'message'=>"This weld number does not exist in NDT with an Accepted Outcome for KP".$model->kp]	;					
				}else{
					$model->is_anomally     = "Yes";	
					$model->is_active    =  0;
					$model->why_anomally = "This weld number does not exist in NDT with an Accepted Outcome for KP".$model->kp;
				}
			}			
			$ProductionExist = \app\models\Production::find()->where(['AND',['=', 'weld_number', $model->weld_number],['=', 'kp', $model->kp]])->active()->one();
			if(!empty($ProductionExist)){
				if($warning){
					return ['status'=>true,'message'=>"This weld number is already in Coating Production for kp-".$model->kp]	;					
				}else{
					$ProductionExist->is_anomally  = "Yes";
					$ProductionExist->is_active    =  0;
					$ProductionExist->why_anomally = "Duplicate entery.";
					$ProductionExist->save(false);

					$model->is_anomally  = "Yes";	
					$model->is_active  = 1;	
					$model->why_anomally = "This weld number is already in Coating Production for kp-".$model->kp;	
				}
			}
		};
		return $model;
	}
	public function welding_coatingrepair_anomaly($model,$ExistModel,$warning=false){		
		if($model->isNewRecord){			
			$list = \app\models\Production::find()->where(['AND',['=', 'weld_number', $model->weld_number],['=','kp', $model->kp],['=','outcome', "Rejected"]])->active()->asArray()->one();
			if(empty($list)){
				if($warning){
					return ['status'=>true,'message'=>"This weld number does not exist in Coating Production with a Rejected Outcome for KP".$model->kp];					
				}else{
					$model->is_anomally  = "Yes";	
					$model->is_active    =  0;
					$model->why_anomally = "This weld number does not exist in Coating Production with a Rejected Outcome for KP".$model->kp;
				}
			}			
			$RepairExist = \app\models\Coatingrepair::find()->where(['AND',['=', 'weld_number', $model->weld_number],['=', 'kp', $model->kp]])->active()->one();
			if(!empty($RepairExist)){
				if($warning){
					return ['status'=>true,'message'=>"This weld number is already in Coating Repair"]	;					
				}else{
					
					$RepairExist->is_anomally  = "Yes";
					$RepairExist->is_active    =  0;
					$RepairExist->why_anomally = "Duplicate entery.";
					$RepairExist->save(false);

					$model->is_anomally  = "Yes";	
					$model->is_active  = 1;	
					$model->why_anomally = "This weld number is already in Coating Repair";	
				}
			}
		};
		return $model;
	}	
	public function rangeValidation($model,$tbl,$currentSec){
		$fromKP 	= $model->from_kp;
		$fromWeld   = $model->from_weld;
		$toKP 		= $model->to_kp;
		$toWeld 	= $model->to_weld;
		$fromWW = (($fromKP *1000000) + ($fromWeld * 0.0000001));
		$toWW = (($toKP * 1000000) + ($toWeld * 0.0000001));

		$sql = "SELECT * FROM (SELECT from_kp, from_weld, to_kp, to_weld, is_deleted, is_active,  project_id, ((from_kp * 1000000) + (from_weld * 0.0000001)) as from_ww,  ((to_kp * 1000000) + (to_weld * 0.0000001)) as to_ww FROM ".$tbl.") as ct 
		WHERE ((ct.from_ww > ".$fromWW." OR ct.to_ww > ".$fromWW.") AND (ct.to_ww < ".$toWW." OR ct.from_ww < ".$toWW.")) and ct.is_deleted = 0 and ct.is_active = 1 and ct.project_id = ".Yii::$app->user->identity->project_id." ORDER by ct.from_ww ASC";
		$Data = Yii::$app->db->createCommand($sql)->queryOne();
		if(!empty($Data)){
					$model->is_anomally  = "Yes";	
					$model->is_active  = 0;	
					$model->why_anomally = "The selected range has already been ".$currentSec;		
		}
		return $model;
	}
	public function rangeInParent($model,$parentTbl,$CurrentSection,$ParentSection){
		$list = Yii::$app->general->getAllWeldData($model->from_kp,$model->from_weld,$model->to_kp,$model->to_weld);					
		if(!empty($list)){
			foreach($list as $key => $ele){
				if(!empty($ele) && !empty($list[$key+1])){
					$fromKP 	= $ele['kp'];
					$fromWeld   = $ele['weld_number'];
					$toKP 		= $list[$key+1]['kp'];
					$toWeld 	= $list[$key+1]['weld_number'];
					
					$fromWW = (($fromKP *1000000) + ($fromWeld * 0.0000001));
					$toWW = (($toKP * 1000000) + ($toWeld * 0.0000001));
					$tbl =$parentTbl;
					$sql = "SELECT * FROM (SELECT from_kp, from_weld, to_kp, to_weld, is_deleted, is_active,  project_id, ((from_kp * 1000000) + (from_weld * 0.0000001)) as from_ww,  ((to_kp * 1000000) + (to_weld * 0.0000001)) as to_ww FROM ".$tbl.") as ct 
					WHERE ((ct.from_ww > ".$fromWW." OR ct.to_ww > ".$fromWW.") AND (ct.to_ww < ".$toWW." OR ct.from_ww < ".$toWW.")) and ct.is_deleted = 0 and ct.is_active = 1 and ct.project_id = ".Yii::$app->user->identity->project_id." ORDER by ct.from_ww ASC";
					$Data = Yii::$app->db->createCommand($sql)->queryOne();
					if(empty($Data)){
						$model->is_anomally  = "Yes";	
						$model->is_active  = 0;	
						$model->why_anomally = "The selected range has not been ".$ParentSection;		
						return $model;
					}
				}
			}
		}
		return $model;
	}

	public function productionCheck($model){
		$list = \app\models\Production::find()->select(['kp','weld_number'])->where([
			'kp'=>$model->from_kp,
			'weld_number'=>$model->from_weld,
			'outcome'=>'Accepted'
		])->active()->one();
		if(empty($list)){
			$checkFromWeldInRepair  = \app\models\Coatingrepair::find()
										->where(['weld_number' =>$model->from_weld, 'kp' =>$model->from_kp])
										->active()
										->asArray()
										->one();
			if(empty($checkFromWeldInRepair)){							
				$model->is_anomally  = "Yes";	
				$model->is_active  = 0;	
				$model->why_anomally ='"From" weld has not been coated';	
				return $model;
			}
		}

		$list = \app\models\Production::find()->select(['kp','weld_number'])->where([
			'kp'=>$model->to_kp,
			'weld_number'=>$model->to_weld,
			'outcome'=>'Accepted'
		])->active()->one();
		if(empty($list)){
			$checkFromWeldInRepair  = \app\models\Coatingrepair::find()
										->where(['weld_number' =>$model->to_weld, 'kp' =>$model->to_kp])
										->active()
										->asArray()
										->one();
			if(empty($checkFromWeldInRepair)){							
				$model->is_anomally  = "Yes";	
				$model->is_active  = 0;	
				$model->why_anomally = '"To" weld has not been coated';	
				return $model;
			}
		}
		return $model;

	}

	public function civil_trenching_anomaly($model,$ExistModel,$warning=false){	
		
		if($model->isNewRecord){
			//############ Check Weld Number / Kp In Production ###################### 
			$model = $this->productionCheck($model);
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}						
			//############ Check Trench Allowed ###################### 
			$model = $this->rangeValidation($model,'civil_trenching','trenched');							
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}
		}
		return $model;
	}
	
	public function civil_lowering_anomaly($model,$ExistModel,$warning=false){	
		if($model->isNewRecord){
			//############ Check Weld Number / Kp In Production ###################### 
			$model = $this->productionCheck($model);
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}	

			$model = $this->rangeInParent($model,'civil_trenching','Lowering','trenched');
			if($model->is_anomally=="Yes" && $model->is_active    ==  0){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}	
			}
			$model = $this->rangeValidation($model,'civil_lowering','lowered');			
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}	
		}
		return $model;
	}	
	public function civil_backfilling_anomaly($model,$ExistModel,$warning=false){		
		if($model->isNewRecord){
			//############ Check Weld Number / Kp In Production ###################### 
			$model = $this->productionCheck($model);
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}
			$model = $this->rangeInParent($model,'civil_lowering','Backfilling','lowered');		
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}
			$model = $this->rangeValidation($model,'civil_backfilling','backfilled');			
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}
		}
		return $model;
	}

	public function civil_reinstatement_anomaly($model,$ExistModel,$warning=false){		
		if($model->isNewRecord){
			//############ Check Weld Number / Kp In Production ###################### 
			$model = $this->productionCheck($model);
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}				
			$model = $this->rangeInParent($model,'civil_backfilling','Reinstatement','backfilled');			
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}		
			$model = $this->rangeValidation($model,'civil_reinstatement','reinstated');			
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}
		}
		return $model;
	}
 	public function precom_cathodic_anomaly($model,$ExistModel,$warning=false){	

		if($model->isNewRecord){
			//############ Check Weld Number / Kp In Production ###################### 
			$model = $this->productionCheck($model);
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}	
			$model = $this->rangeInParent($model,'civil_reinstatement','Cathodic Protection','reinstated');				
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}
			$model = $this->rangeValidation($model,'com_cathodic_protection','cathodic protection');			
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}
		}
		return $model;
	}
	public function precom_cleangauge_anomaly($model,$ExistModel,$warning=false){		
		if($model->isNewRecord){
			//############ Check Weld Number / Kp In Production ###################### 
			$model = $this->productionCheck($model);
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}
			$model = $this->rangeInParent($model,'com_cathodic_protection','Cleangauge','cathodicprotection');			
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}
			$model = $this->rangeValidation($model,'com_clean_gauge','cleangauge');		
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}
		}
		return $model;
	}

	public function precom_hydrotesting_anomaly($model,$ExistModel,$warning=false){		
		if($model->isNewRecord){
			//############ Check Weld Number / Kp In Production ###################### 
			$model = $this->productionCheck($model);
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}	
			$model = $this->rangeInParent($model,'com_clean_gauge','Hydrotesting','cleangauge');		
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}
			$model = $this->rangeValidation($model,'com_hydrotesting','hydrotested');			
			if($model->is_active==0 && $model->is_anomally=="Yes"){
				if($warning){
					return ['status'=>true,'message'=>$model->why_anomally];
				}else{
					return $model;
				}
			}
		}
		return $model;
	}
}
?>