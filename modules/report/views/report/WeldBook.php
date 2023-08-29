<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use yii\widgets\Pjax;
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
$this->title = "Weld Book";
?>
  <?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<section id="basic-form-layouts">
	<div class="row match-height">
		<div class="col-md-12">
			<div class="card" >
            <div class="card-header">
					<h4 class="card-title" id="basic-layout-form"><?= Yii::$app->trans->getTrans('Weld Book'); ?>
						<button type="button" class="btn btn-success pull-right make-sequence"><?= Yii::$app->trans->getTrans('Sequence Manually'); ?></button>
						<?= Yii::$app->general->generateReport(1);?>
					</h4>
				</div>
				<div class="card-body">
					<!-- <div class="table-responsive"> -->
						<?= GridView::widget([
							'dataProvider' => $dataProvider,
							'filterModel' => $searchModel,
							"filterSelector" => "select[name='per-page']",
							'summary' => Yii::$app->general->getSummaryText(array('start' => "{begin}", 'end' => "{end}", 'total' => "{totalCount}")),
							'layout' => '<div class="row mb-1"><div class="col-md-6"><div class="summary">{summary}</div></div><div class="col-md-6 right">'.$perpage.'</div></div>'."\n".'<div class="table-responsive long">{items}</div>'."\n".'{pager}',
							'emptyText' => Yii::$app->trans->getTrans('No results found.'),
							'columns' => [
								[
									'attribute' => 'date',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false
								],
								[
									'attribute' => 'report_number',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false
								],
//                                [
//                                    'attribute' => 'production_report_number',
//                                    'headerOptions' => ['style' => 'width:10%'],
//                                    'filter' => false,
//                                    'value' => function($model){
//                                        $Pipe = \app\models\Production::find()->select(['report_number'])->where(['project_id'=>$model->project_id])->active()->asArray()->one();
//                                        return !empty($Pipe['report_number']) ? $Pipe['report_number'] : '-';
//                                    }
//                                ],
//                                [
//                                    'attribute' => 'weld_repair_report_number',
//                                    'headerOptions' => ['style' => 'width:10%'],
//                                    'filter' => false,
//                                    'value' => function($model){
//                                        $Pipe = \app\models\Weldingrepair::find()->select(['report_number'])->where(['project_id'=>$model->project_id])->active()->asArray()->one();
//                                        return !empty($Pipe['report_number']) ? $Pipe['report_number'] : '-';
//                                    }
//                                ],
								[
									'attribute' => 'geo_location',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false
								],
								[
									'attribute' => 'kp',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => '<input type="text" name ="WeldingSearch[kp]" class="form-control" value="'.$searchModel->kp.'" style="width:100px">'
								],
								[
									'attribute' => 'weld_number',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => '<input type="text" name ="WeldingSearch[weld_number]" class="form-control" value="'.$searchModel->weld_number.'" style="width:100px">'
								],
								[
									'attribute' => 'line_type',
									'filter' => ['Main Line' => Yii::$app->trans->getTrans('Main Line'), 'Tie Line' => Yii::$app->trans->getTrans('Tie Line')],
									'headerOptions' => ['style' => 'width:10%'],
									'value' => function($model){
										if($model->line_type == 'Main Line'){
											return Yii::$app->trans->getTrans('Main Line');
										} else if($model->line_type == 'Tie Line'){
											return Yii::$app->trans->getTrans('Tie Line');
										}
									}
								],
								'pipe_number',
								'next_pipe',
								'sequence',
								[
									'attribute' => 'visual_acceptance',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => ['Yes' => 'Yes', 'No' => 'No',]
								],
								[
									'attribute' => 'has_been_cut_out',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => ['Yes' => 'Yes', 'No' => 'No',]
								],
								[
									'attribute' => 'weld_type',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false,
									'value' => function ($model) {
										return !empty($model->weld_type) ? $model->weld_type : "-";
									},
								],
								[
									'attribute' => 'weld_sub_type',
									'filter' => false,
									'value' => function ($model) {
										return !empty($model->weld_sub_type) ? $model->weld_sub_type : "-";
									},
								],
								[
									'attribute' => 'weld_crossing',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false,
									'value' => function ($model) {
										return !empty($model->weld_crossing) ? $model->weld_crossing : "-";
									},
								],
								[
									'attribute' => 'root_os',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false,
									'value' => function ($model) {
										return !empty($model->root_os) ? $model->root_os : "-";
									},
								],
								[
									'attribute' => 'root_ts',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false,
									'value' => function ($model) {
										return !empty($model->root_ts) ? $model->root_ts : "-";
									},
								],
								[
									'attribute' => 'hot_os',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false,
									'value' => function ($model) {
										return !empty($model->hot_os) ? $model->hot_os : "-";
									},
								],
								[
									'attribute' => 'hot_ts',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false,
									'value' => function ($model) {
										return !empty($model->hot_ts) ? $model->hot_ts : "-";
									},
								],
								[
									'attribute' => 'fill_os',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false,
									'value' => function ($model) {
										return !empty($model->fill_os) ? $model->fill_os : "-";
									},
								],
								[
									'attribute' => 'fill_ts',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false,
									'value' => function ($model) {
										return !empty($model->fill_ts) ? $model->fill_ts : "-";
									},
								],
								[
									'attribute' => 'cap_os',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false,
									'value' => function ($model) {
										return !empty($model->cap_os) ? $model->cap_os : "-";
									},
								],
								[
									'attribute' => 'cap_ts',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false,
									'value' => function ($model) {
										return !empty($model->cap_ts) ? $model->cap_ts : "-";
									},
								],
								[
									'attribute' => 'WPS',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false,
									'value' => function ($model) {
										$List = Yii::$app->general->TaxonomyDrop(6,true);
										return !empty($List[$model->WPS]) ? $List[$model->WPS] : "-";
									},
								],
								[
									'attribute' => 'electrodes',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false,
									'value' => function ($model) {
										return !empty($model->electrodes) ? $model->electrodes : "-";
									},
								],
								[
									'attribute' => 'pipe_number',
									'label' => 'Wall Thickness',
									'filter' => false,
									'value' => function ($model) {
										$Pipe = \app\models\Pipe::find()->select(['wall_thikness'])->where(['pipe_number'=>$model->pipe_number])->active()->asArray()->one();
										return !empty($Pipe['wall_thikness']) ? $Pipe['wall_thikness'] : '-';
									},
								],
								[
									'attribute' => 'pipe_number',
									'label' => 'Length',
									'filter' => false,
									'value' => function ($model) {
										$Pipe = \app\models\Pipe::find()->select(['length'])->where(['pipe_number'=>$model->pipe_number])->active()->asArray()->one();
										return !empty($Pipe['length']) ? $Pipe['length'] : '-';
									},
								],
								[
									'attribute' => 'weld_number',
									'label' => 'NDT Report',
									'filter' => false,
									'value' => function ($model) {
										// $Weld = \app\models\Ndt::find()->select(['report_number'])->where([
										// 	'AND',
										// 	['=', 'weld_number', $model->weld_number],
										// 	['=', 'kp', $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->orderBy('Id DESC')->one();
										$Weld = \app\models\Ndt::find()->select(['report_number'])->where(['main_weld_id' => $model->id])->active()->asArray()->orderBy('Id DESC')->one();
										return !empty($Weld['report_number']) ? $Weld['report_number'] : '-';
									},
								],
								[
									'attribute' => 'weld_number',
									'label' => 'NDT Result',
									'filter' => false,
									'format' => 'raw',
									'value' => function ($model) {
										// $Weld = \app\models\Ndt::find()->where(['weld_number'=>$model->weld_number, 'kp'=>$model->kp])->active()->asArray()->orderBy('Id DESC')->one();
										// return $Weld['outcome'];
										// $ndtData = \app\models\Ndt::find()->select(['date', 'outcome'])->where([
										// 	'AND',
										// 	['weld_number' => $model->weld_number],
										// 	['kp' => $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->orderBy('Id DESC')->all();
										$ndtData = \app\models\Ndt::find()->select(['date', 'outcome'])->where(['main_weld_id' => $model->id])->active()->asArray()->orderBy('Id DESC')->all();
										if(!empty($ndtData)){
											$result = '<span class="wb-ndt-result">';
											$i = 1;
											foreach($ndtData as $ndt){
												$result .= '<span>'.$i.') <b>'.$ndt['outcome'].'</b><small>'.$ndt['date'].'</small></span>';
												$i++;
											}
											$result .= '</span>';
											return $result;
										} else {
											return '-';
										}
									},
								],
								[
									'attribute' => 'weld_number',
									'label' => 'Weld Repair Report',
									'filter' => false,
									'value' => function ($model) {
										// $weldRepairData = \app\models\Weldingrepair::find()->select(['report_number'])->where([
										// 	'AND',
										// 	['weld_number' => $model->weld_number],
										// 	['kp' => $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->orderBy('Id DESC')->one();
										$weldRepairData = \app\models\Weldingrepair::find()->select(['report_number'])->where(['kp' => $model->kp,'weld_number'=>$model->weld_number])->active()->asArray()->orderBy('Id DESC')->one();
										return !empty($weldRepairData['report_number']) ? $weldRepairData['report_number'] : '-';
									}
								],
								[
									'attribute' => 'weld_number',
									'label' => 'Weld Repair Welder',
									'filter' => false,
									'value' => function ($model) {
										// $weldRepairData = \app\models\Weldingrepair::find()->select(['welder'])->where([
										// 	'AND',
										// 	['weld_number' => $model->weld_number],
										// 	['kp' => $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->orderBy('Id DESC')->one();
										$weldRepairData = \app\models\Weldingrepair::find()->select(['welder'])->where(['kp' => $model->kp,'weld_number'=>$model->weld_number])->active()->asArray()->orderBy('Id DESC')->one();
										return !empty($weldRepairData['welder']) ? $weldRepairData['welder'] : '-';
									}
								],
								[
									'attribute' => 'weld_number',
									'label' => 'Weld Repair Electrodes',
									'filter' => false,
									'value' => function ($model) {
										// $weldRepairData = \app\models\Weldingrepair::find()->select(['electrodes'])->where([
										// 	'AND',
										// 	['weld_number' => $model->weld_number],
										// 	['kp' => $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->orderBy('Id DESC')->one();
										$weldRepairData = \app\models\Weldingrepair::find()->select(['electrodes'])->where(['kp' => $model->kp,'weld_number'=>$model->weld_number])->active()->asArray()->orderBy('Id DESC')->one();
										return !empty($weldRepairData['electrodes']) ? $weldRepairData['electrodes'] : '-';
									}
								],
								[
									'attribute' => 'weld_number',
									'label' => 'Coating Report',
									'filter' => false,
									'value' => function ($model) {
										// $Production = \app\models\Production::find()->select(['report_number'])->where([
										// 	'AND',
										// 	['weld_number' => $model->weld_number],
										// 	['kp' => $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->one();
										$Production = \app\models\Production::find()->select(['report_number'])->where(['kp' => $model->kp,'weld_number'=>$model->weld_number])->active()->asArray()->one();
										return !empty($Production['report_number']) ? $Production['report_number'] : '-';
									},
								],
								[
									'attribute' => 'weld_number',
									'label' => 'Applicator',
									'filter' => false,
									'value' => function ($model) {
										$Production = \app\models\Production::find()->select(['abrasive_material'])->where(['kp' => $model->kp,'weld_number'=>$model->weld_number])->active()->asArray()->one();
										return !empty($Production['abrasive_material']) ? $Production['abrasive_material'] : '-';
									},
								],
								[
									'attribute' => 'weld_number',
									'label' => 'Material Batch Number',
									'filter' => false,
									'value' => function ($model) {
										// $Production = \app\models\Production::find()->select(['material_batch_number'])->where([
										// 	'AND',
										// 	['weld_number' => $model->weld_number],
										// 	['kp' => $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->one();
										$Production = \app\models\Production::find()->select(['material_batch_number'])->where(['kp' => $model->kp,'weld_number'=>$model->weld_number])->active()->asArray()->one();
										return !empty($Production['material_batch_number']) ? $Production['material_batch_number'] : '-';
									},
								],
								[
									'attribute' => 'weld_number',
									'label' => 'Batch Number A',
									'filter' => false,
									'value' => function ($model) {
										// $Production = \app\models\Production::find()->select(['batch_number_a'])->where([
										// 	'AND',
										// 	['weld_number' => $model->weld_number],
										// 	['kp' => $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->one();
										$Production = \app\models\Production::find()->select(['batch_number_a'])->where(['kp' => $model->kp,'weld_number'=>$model->weld_number])->active()->asArray()->one();
										return !empty($Production['batch_number_a']) ? $Production['batch_number_a'] : '-';
									},
								],
								[
									'attribute' => 'weld_number',
									'label' => 'Batch Number B',
									'filter' => false,
									'value' => function ($model) {
										// $Production = \app\models\Production::find()->select(['batch_number_b'])->where([
										// 	'AND',
										// 	['weld_number' => $model->weld_number],
										// 	['kp' => $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->one();
										$Production = \app\models\Production::find()->select(['batch_number_b'])->where(['kp' => $model->kp,'weld_number'=>$model->weld_number])->active()->asArray()->one();
										return !empty($Production['batch_number_b']) ? $Production['batch_number_b'] : '-';
									},
								],
								[
									'attribute' => 'weld_number',
									'label' => 'DFT 1',
									'filter' => false,
									'value' => function ($model) {
										// $Production = \app\models\Production::find()->select(['dft'])->where([
										// 	'AND',
										// 	['weld_number' => $model->weld_number],
										// 	['kp' => $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->one();
										$Production = \app\models\Production::find()->select(['dft'])->where(['kp' => $model->kp,'weld_number'=>$model->weld_number])->active()->asArray()->one();
										return !empty($Production['dft']) ? $Production['dft'] : '-';
									},
								],
								[
									'attribute' => 'weld_number',
									'label' => 'DFT 2',
									'filter' => false,
									'value' => function ($model) {
										// $Production = \app\models\Production::find()->select(['dft_2'])->where([
										// 	'AND',
										// 	['weld_number' => $model->weld_number],
										// 	['kp' => $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->one();
										$Production = \app\models\Production::find()->select(['dft_2'])->where(['kp' => $model->kp,'weld_number'=>$model->weld_number])->active()->asArray()->one();
										return !empty($Production['dft_2']) ? $Production['dft_2'] : '-';
									},
								],
								[
									'attribute' => 'weld_number',
									'label' => 'DFT 3',
									'filter' => false,
									'value' => function ($model) {
										// $Production = \app\models\Production::find()->select(['dft_3'])->where([
										// 	'AND',
										// 	['weld_number' => $model->weld_number],
										// 	['kp' => $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->one();
										$Production = \app\models\Production::find()->select(['dft_3'])->where(['kp' => $model->kp,'weld_number'=>$model->weld_number])->active()->asArray()->one();
										return !empty($Production['dft_3']) ? $Production['dft_3'] : '-';
									},
								],
								[
									'attribute' => 'weld_number',
									'label' => 'DFT 4',
									'filter' => false,
									'value' => function ($model) {
										// $Production = \app\models\Production::find()->select(['dft_4'])->where([
										// 	'AND',
										// 	['weld_number' => $model->weld_number],
										// 	['kp' => $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->one();
										$Production = \app\models\Production::find()->select(['dft_4'])->where(['kp' => $model->kp,'weld_number'=>$model->weld_number])->active()->asArray()->one();
										return !empty($Production['dft_4']) ? $Production['dft_4'] : '-';
									},
								],
								[
									'attribute' => 'weld_number',
									'label' => 'DFT 5',
									'filter' => false,
									'value' => function ($model) {
										// $Production = \app\models\Production::find()->select(['dft_5'])->where([
										// 	'AND',
										// 	['weld_number' => $model->weld_number],
										// 	['kp' => $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->one();
										$Production = \app\models\Production::find()->select(['dft_5'])->where(['kp' => $model->kp,'weld_number'=>$model->weld_number])->active()->asArray()->one();
										return !empty($Production['dft_5']) ? $Production['dft_5'] : '-';
									},
								],
								[
									'attribute' => 'weld_number',
									'label' => 'DFT 6',
									'filter' => false,
									'value' => function ($model) {
										// $Production = \app\models\Production::find()->select(['dft_6'])->where([
										// 	'AND',
										// 	['weld_number' => $model->weld_number],
										// 	['kp' => $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->one();
										$Production = \app\models\Production::find()->select(['dft_6'])->where(['kp' => $model->kp,'weld_number'=>$model->weld_number])->active()->asArray()->one();
										return !empty($Production['dft_6']) ? $Production['dft_6'] : '-';
									},
								],
								[
									'attribute' => 'weld_number',
									'label' => 'Check Points',
									'filter' => false,
									'value' => function ($model) {
										// $Production = \app\models\Production::find()->select(['checkpoint'])->where([
										// 	'AND',
										// 	['weld_number' => $model->weld_number],
										// 	['kp' => $model->kp],
										// 	['>', 'created_at', $model->created_at]
										// ])->active()->asArray()->one();
										$Production = \app\models\Production::find()->select(['checkpoint'])->where(['kp' => $model->kp,'weld_number'=>$model->weld_number])->active()->asArray()->one();
										return !empty($Production['checkpoint']) ? $Production['checkpoint'] : '-';
									},
								],
								[
									'attribute' => 'comment',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => false,
									'value' => function ($model) {
										return !empty($model->comment) ? $model->comment : '-';
									}
								],
								[
									'attribute' => 'created_by',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => Yii::$app->general->employeeList(""),
									'value' => function ($model) {
										$List = Yii::$app->general->employeeList("");
										return !empty($List[$model->created_by]) ? $List[$model->created_by] : "";
									},
								],
								[
									'attribute' => 'signed_off',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => ['Yes' => 'Yes', 'No' => 'No']
								],
								[
									'attribute' => 'qa_manager',
									'headerOptions' => ['style' => 'width:10%'],
									'filter' => Yii::$app->general->employeeList(""),
									'value' => function ($model) {
										$List = Yii::$app->general->employeeList("");
										return !empty($List[$model->qa_manager]) ? $List[$model->qa_manager] : "";
									},
								],
							],
						]); ?>
					<!-- </div> -->
				</div>
			</div>
		</div>
	</div>
</section>
<?php Pjax::end(); ?>