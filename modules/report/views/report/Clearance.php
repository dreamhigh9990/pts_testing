<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = "Clearance Report";
?>
    <?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="col-md-12">
	<section id="basic-form-layouts">
		<div class="row">
			<div class="col-sm-12">
				<div class="content-header"></div>
			</div>
		</div>
		<div class="row match-height">
		<div class="col-md-6 col-sm-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title" id="basic-layout-form"><?= Yii::$app->trans->getTrans('Get Clearance report'); ?></h4>
					</div>
					<div class="card-body">
						<div class="px-3">

							<div class="form-body">
								<?php
									$Kplist = ArrayHelper::map(\app\models\Welding::find()->select('kp')->active()->groupBy('kp')->asArray()->all(),'kp','kp');
									$model = new \app\models\ClearanceReport;
									$form = ActiveForm::begin([
										'id'=>'clearance-form',
										'options'=>['autocomplete'=>'off'],
										
									]);?>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<?= $form->field($model, 'from_kp')
													->dropDownList(
														$Kplist,
														['onchange'=>'$.get( "'.Url::toRoute(['/report/default/weld-dropdown']).'", { kp : $(this).val(),type : "from_weld" })
														.done(function(data) {															
															$( "#clearancereport-from_weld").html(data);
														});',
														'prompt' => Yii::$app->trans->getTrans('From KP')]
													);
												?>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<?= $form->field($model, 'to_kp')
													->dropDownList(
														$Kplist,
														['onchange'=>'$.get( "'.Url::toRoute(['/report/default/weld-dropdown']).'", { kp : $(this).val(),type : "to_weld" })
														.done(function(data) {														
															$( "#clearancereport-to_weld").html(data);
														});',
														'prompt' => Yii::$app->trans->getTrans('To KP')]
													);
												?>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6" id="clearancereport-from_weld">
											
												
										</div>
										<div class="col-md-6" id="clearancereport-to_weld">
												
										</div>
									</div>
									<div class="form-actions">
										<button type="submit" class="btn btn-success"><?= Yii::$app->trans->getTrans('Get Report'); ?></button>
									</div>
								<?php ActiveForm::end(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-sm-12" id="clearance">
			</div>		
		</div>	
	</section>
</div>
<?php Pjax::end(); ?>