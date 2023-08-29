<?php
use yii\widgets\ActiveForm;

use kartik\daterange\DateRangePicker;
$this->title = 'Production Report';
?>

<div class="col-md-12">
	<section id="basic-form-layouts">
		<div class="row">
			<div class="col-sm-12">
				<div class="content-header"></div>
			</div>
		</div>
		<div class="row match-height">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title" id="basic-layout-form">
                            <?= Yii::$app->trans->getTrans('Production Report'); ?>
                            <button class="btn btn-raised btn-white btn-min-width mr-1 mb-1 black pull-right" onclick="printDiv();"><?= Yii::$app->trans->getTrans('Print Report'); ?></button>
                        </h4>
					</div>
					<div class="card-body">
						<div class="px-3">
							<div class="form-body">
                                <?php
                                $form = ActiveForm::begin([
                                    'id'=>'production-report',
                                    'options'=>['autocomplete'=>'off'],
                                ]);
                                ?>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <select class="form-control prod-opt-change" name="filterType">
                                                    <option value="all" <?= $filterType == 'all' ? 'selected="selected"' : ""; ?>><?= Yii::$app->trans->getTrans('Overall Report'); ?></option>
                                                    <option value="weekly" <?= $filterType == 'weekly' ? 'selected="selected"' : ""; ?>><?= Yii::$app->trans->getTrans('Weekly Report'); ?></option>
                                                    <option value="daily" <?= $filterType == 'daily' ? 'selected="selected"' : ""; ?>><?= Yii::$app->trans->getTrans('Daily Report'); ?></option>
                                                </select>
                                            </div>
                                            <div class="form-group div-weekly">
                                                <?php
                                                    echo DateRangePicker::widget([
                                                        'name' => 'weekRange',
                                                        'useWithAddon' => true,
                                                        'language' => 'en',
                                                        'options' => [
                                                            'id' => 'weekRange',
                                                            'class' => 'form-control'
                                                        ],
                                                        'pluginOptions' => [
                                                            'locale' => [
                                                                'format' => 'Y-m-d'
                                                            ],
                                                            'separator' => ' / ',
                                                            'opens' => 'left'
                                                        ]
                                                    ]);
                                                ?>
                                            </div>
                                            <div class="form-group div-daily">
                                                <?php
                                                    echo DateRangePicker::widget([
                                                        'name' => 'dailyRange',
                                                        'useWithAddon' => true,
                                                        'language' => 'en',
                                                        'options' => [
                                                            'id' => 'dailyRange',
                                                            'class' => 'form-control'
                                                        ],
                                                        'pluginOptions' => [
                                                            'locale' => [
                                                                'format' => 'Y-m-d'
                                                            ],
                                                            'separator' => ' / ',
                                                            'opens' => 'left'
                                                        ]
                                                    ]);
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                            <button type="submit" class="btn btn-success btn-production-report"><?= Yii::$app->trans->getTrans('Get Report'); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                <?php ActiveForm::end(); ?>
							</div>
						</div>

                        <div class="production-data">
                            <?php
                                echo $this->render('production/_overallproductiondata',[
                                    'filterType'=>$filterType
                                ]);
                            ?>
                        </div>
					</div>
				</div>
			</div>
		</div>	
	</section>
</div>
