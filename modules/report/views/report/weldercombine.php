<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = "Welder Combine";
$currentDate = date('Y-m-d');
if(isset($date)){
    $currentDate = $date;
}
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
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="basic-layout-form">MANUAL WELD REPAIR DETAILS <button class="btn btn-raised btn-white btn-min-width mr-1 mb-1 black pull-right" onclick="printDiv();">Print Report</button></h4>
                        </div>
                        <div class="card-body">
                            <div class="px-3">
                                <div class="form-body">
                                    <?php		
                                    $form = ActiveForm::begin([
                                        'id'=>'welder-combine-form',
                                        'options' => [
                                            'autocomplete'=>'off'
                                        ]
                                    ]);
                                    ?>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <input type="text" class="form-control pickadate" name="date" value="<?= $currentDate; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-sm btn-primary btn-welder-combine">Search</button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php ActiveForm::end(); ?>
                                    <div class="tbl-data" id="print-body">
                                        <?php echo $this->render('_weldercombinetable',['data'=>$data,'date'=>$date]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                		
            </div>	
        </section>
    </div>
<?php Pjax::end(); ?>