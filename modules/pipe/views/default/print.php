<?php
use Da\QrCode\QrCode;
use yii\helpers\Html;
?>
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel16"><?= Yii::$app->trans->getTrans('Print Selected Items'); ?></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body" id="print-body">
            <div class="row col-md-12 table-responsive long">
            
                <?php if($model == "\app\models\PipeSearch") { ?>
                <table class="table table-bordered">
                    <thead class="thead-default">
                        <tr>
                            <th><?= Yii::$app->trans->getTrans('Barcode'); ?></th>
                            <th><?= Yii::$app->trans->getTrans('Pipe Number'); ?></th>
                            <th><?= Yii::$app->trans->getTrans('Wall Thickness'); ?></th>
                            <th><?= Yii::$app->trans->getTrans('OD (mm)'); ?></th>
                            <th><?= Yii::$app->trans->getTrans('Length'); ?></th>
                            <th><?= Yii::$app->trans->getTrans('Yield Strength'); ?></th>
                            <th><?= Yii::$app->trans->getTrans('Heat Number'); ?></th>
                            <th><?= Yii::$app->trans->getTrans('Pup'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach(array_reverse($Data) as $item){
                            $qrCode = (new QrCode($item['pipe_number']))
                            ->setSize(150)
                            ->setMargin(5)
                            ->useForegroundColor(51, 153, 255);
                            $img = $qrCode->writeDataUri();
                        ?>
                        <tr>
                            <th><img src="<?=$img;?>" alt="Maitri" width="50" class="gradient-red-pink"></th>
                            <td><?= Html::encode($item['pipe_number']);?></td>
                            <td><?= Html::encode($item['wall_thikness']);?></td>
                            <td><?= Html::encode($item['od']);?></td>
                            <td><?= Html::encode($item['length']);?></td>
                            <td><?= Html::encode($item['yeild_strength']);?></td>
                            <td><?= Html::encode($item['heat_number']);?></td>
                            <td><?= $item['pups'] == 1 ? Html::encode('Yes') : Html::encode('No');?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php }else{ ?>
                <table class="table table-bordered">
                    <thead class="thead-default">
                        <tr>
                            <?php
                            $ob = new $model;
                            $PrintLabels = $ob->print_attributes();
                            foreach($PrintLabels as $k=>$label){
                            ?>                
                                <th><?= $label; ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sumOfWeight = 0;
                        $sumOfAngle = 0;
                        $sumOfLengthCutting = 0;
                        $sumOfLength = 0;
                        foreach($Data as $item){
                        ?>
                            <tr>
                                <?php
                                foreach($PrintLabels as $k=>$label){
                                    if($k == "pipe_weight"){
                                        $Pipe = \app\models\Pipe::find()->where(['pipe_number' => $item['pipe_number']])->active()->asArray()->one();
                                        $item[$k] = !empty($Pipe['weight']) ? $Pipe['weight'] : 0;
                                        $sumOfWeight = $sumOfWeight + $item[$k];
                                    }
                                    
                                    if($k == "angle"){
                                        $sumOfAngle = $sumOfAngle + $item[$k];
                                    }
                                    
                                    if($k == "length_2"){
                                        $sumOfLengthCutting = $sumOfLengthCutting + $item[$k];
                                    }

                                    if($k == "pipe_length"){
                                        $Pipe = \app\models\Pipe::find()->where(['pipe_number' => $item['pipe_number']])->active()->asArray()->one();
                                        $item[$k] = !empty($Pipe['length']) ? $Pipe['length'] : 0;
                                        $sumOfLength = $sumOfLength + $item[$k];
                                    }

                                    if($k == "defects" && $model == '\app\models\ReceptionSearch'){
                                        $Pipe = \app\models\Pipe::find()->where(['pipe_number' => $item['pipe_number']])->active()->asArray()->one();
                                        $defects = '';
                                        if(!empty($Pipe['defects'])){
                                            $defectJsnDecode = json_decode($Pipe['defects'], true);
                                            if(!empty($defectJsnDecode)){
                                                $defects = implode(', ', $defectJsnDecode);
                                            }
                                        }
                                        $item[$k] = $defects;
                                    }

                                    if($k == "vehicle_id"){
                                        $vehicleDetails = \app\models\VehicleSchedule::find()->where(['id' => $item[$k]])->active()->asArray()->one();
                                        if(!empty($vehicleDetails)){
                                            $item[$k] = $vehicleDetails['vehicle_number'];
                                        } else {
                                            $item[$k] = '-';
                                        }
                                    }

                                    if($k == 'wall_thickness'){
                                        $pipeDetails = \app\models\Pipe::find()->select('wall_thikness')->where(['LIKE', 'pipe_number', $item['pipe_number']])->active()->asArray()->one();
                                        $item[$k] = !empty($pipeDetails) ? $pipeDetails['wall_thikness'] : '-';
                                    }

                                    if($k == "weld_type" && $model != '\app\models\WeldingSearch'){
                                        if($model == '\app\models\NdtSearch' || $model == '\app\models\WeldingrepairSearch'){
                                            $weldList = \app\models\Welding::find()->select('weld_type')->where(['AND',['=', 'weld_number', $item['weld_number']],['=','kp', $item['kp']]])->active()->asArray()->one();
                                        } else if($model == '\app\models\ProductionSearch'){
                                            $weldList = \app\models\Ndt::find()->select(['welding.weld_type'])->leftJoin('welding','welding_ndt.weld_number=welding.weld_number AND welding.project_id=welding_ndt.project_id AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)->where(['AND',['=', 'welding_ndt.weld_number', $item['weld_number']],['=','welding_ndt.kp', $item['kp']],['OR',['=','outcome', 'Accepted'],['=','outcome', 'Repaired']]])->active()->asArray()->one();
                                        } else if($model == '\app\models\CoatingrepairSearch') {
                                            $weldList = \app\models\Production::find()->select(['welding.weld_type'])->leftJoin('welding','welding_coating_production.weld_number=welding.weld_number AND welding.project_id=welding_coating_production.project_id AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)->where(['AND',['=', 'welding_coating_production.weld_number', $item['weld_number']],['=','welding_coating_production.kp', $item['kp']]])->active()->asArray()->one();
                                        }
                                        
                                        $item[$k] = !empty($weldList) ? $weldList['weld_type'] : '-';
                                    }

                                    if($k == "weld_sub_type" && $model != '\app\models\WeldingrepairSearch' && $model != '\app\models\WeldingSearch'){
                                        if($model == '\app\models\NdtSearch'){
                                            $weldList = \app\models\Welding::find()->select('weld_sub_type')->where(['AND',['=', 'weld_number', $item['weld_number']],['=','kp', $item['kp']]])->active()->asArray()->one();
                                        } else if($model == '\app\models\ProductionSearch'){
                                            $weldList = \app\models\Ndt::find()->select(['welding.weld_sub_type'])->leftJoin('welding','welding_ndt.weld_number=welding.weld_number AND welding.project_id=welding_ndt.project_id AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)->where(['AND',['=', 'welding_ndt.weld_number', $item['weld_number']],['=','welding_ndt.kp', $item['kp']],['OR',['=','outcome', 'Accepted'],['=','outcome', 'Repaired']]])->active()->asArray()->one();
                                        } else if($model == '\app\models\CoatingrepairSearch') {
                                            $weldList = \app\models\Production::find()->select(['welding.weld_sub_type'])->leftJoin('welding','welding_coating_production.weld_number=welding.weld_number AND welding.project_id=welding_coating_production.project_id AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)->where(['AND',['=', 'welding_coating_production.weld_number', $item['weld_number']],['=','welding_coating_production.kp', $item['kp']]])->active()->asArray()->one();
                                        }

                                        $item[$k] = !empty($weldList) ? $weldList['weld_sub_type'] : '-';
                                    }

                                    if($k == "WPS" && $model == '\app\models\WeldingSearch'){
                                        $getValue = \app\models\TaxonomyValue::find()->where(['id' => $item[$k]])->asArray()->one();
                                        $item[$k] = !empty($getValue) ? $getValue['value'] : $item[$k];
                                    }

                                    if($k == "created_by" && ($model == '\app\models\CleargradeSearch' || $model == '\app\models\TrenchingSearch' || $model == '\app\models\LoweringSearch' || $model == '\app\models\BackfillingSearch' || $model == '\app\models\ReinstatementSearch' || $model == '\app\models\SpecialCrossingsSearch')){
                                        $List = Yii::$app->general->employeeList("");
                                        $item[$k] = !empty($List[$item['created_by']]) ? $List[$item['created_by']] : "";
                                    }

                                ?>
                                    <td><?=Html::encode($item[$k]);?></td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                        <?php if($model == '\app\models\ReceptionSearch'){ ?>
                        <tr>
                            <th colspan="1" ><b><?= Yii::$app->trans->getTrans('Total Weight'); ?></b></th>
                            <th><?= $sumOfWeight; ?></th>
                        </tr>
                        <?php } else if($model == '\app\models\BendingSearch'){ ?>
                        <tr>
                            <th colspan="1" ><b><?= Yii::$app->trans->getTrans('Total Angle'); ?></b></th>
                            <th><?= $sumOfAngle; ?></th>
                        </tr>
                        <?php } else if($model == '\app\models\NdtSearch' || $model == '\app\models\ProductionSearch' || $model == '\app\models\CoatingrepairSearch' || $model == '\app\models\WeldingSearch'){ ?>
                        <tr>
                            <th colspan="1" ><b><?= Yii::$app->trans->getTrans('Total Records'); ?></b></th>
                            <th><?= count($Data); ?></th>
                        </tr>
                        <?php } else if($model == '\app\models\CuttingSearch'){ ?>
                        <tr>
                            <th colspan="1" ><b><?= Yii::$app->trans->getTrans('Total Length'); ?></b></th>
                            <th><?= $sumOfLengthCutting; ?></th>
                        </tr>
                        <?php } else if($model == '\app\models\StringingSearch'){ ?>
                        <tr>
                            <th colspan="1" ><b><?= Yii::$app->trans->getTrans('Total Length'); ?></b></th>
                            <th><?= $sumOfLength; ?></th>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } ?>
            </div>
        </div>
    
        <div class="modal-footer">
            <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal"><?= Yii::$app->trans->getTrans('Close'); ?></button>
            <button type="button" class="btn btn-outline-primary" onClick="printDiv();"><?= Yii::$app->trans->getTrans('Print'); ?></button>
        </div>
    </div>
</div>

