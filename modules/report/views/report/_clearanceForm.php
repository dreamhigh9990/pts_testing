
<?php
$Project = Yii::$app->general->getTaxomonyData(Yii::$app->user->identity->project_id);


?>
<div class="card">
	<div class="card-header">
		<h4 class="card-title" id="basic-layout-form">
            <?= Yii::$app->trans->getTrans('Generated Clearance Report'); ?>
            <button type="button" class="btn btn-warning pull-right" onclick="printDiv();"><?= Yii::$app->trans->getTrans('Print'); ?></button></h4>
        </h4>
    </div>
    <div class="card-body">
        <div class="table-Approval-main">
            <div class="container">
                <div class="table-appro" id="print-body">
                    <div class="logo-appro">
                        <img class="appro-logo img-logo img-responsive" src="<?= Yii::$app->general->logo()?>" alt="some">
                    </div>
                    <div class="appro-title">
                        <h1><?= Yii::$app->trans->getTrans('Approval to Lower Pipe Strings'); ?></h1>
                    </div>
                    <div class="br-div-tb"></div>
                    <div class="selectiondetail-tabel">
                        <div class="detail-tabel-title"><?= Yii::$app->trans->getTrans('Pipeline Section Details'); ?></div>
                        <div class="row pro-loc-main">
                            <div class="project-name">
                                <label for="name"><?= Yii::$app->trans->getTrans('Project'); ?> - <code class="pull-right"><?= !empty($Project['value'])?$Project['value']:"";?></code></label>
                            </div>
                        </div>
                        <div class="selectiondetailtable">
                            <table class="table table-selectiondetail">
                                <thead>
                                    <tr>
                                        <th><?= Yii::$app->trans->getTrans('Start Section'); ?></th>
                                        <th><?= Yii::$app->trans->getTrans('End Section'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?= Yii::$app->trans->getTrans('Start Pipe Number'); ?>:<code class="pull-right"><?= isset($data['startPipe'])?$data['startPipe']:"";?></code></td>
                                        <td><?= Yii::$app->trans->getTrans('End Pipe Number'); ?>:<code class="pull-right"><?= isset($data['endPipe'])?$data['endPipe']:"";?></code></td>
                                    
                                    <tr>
                                        <td><?= Yii::$app->trans->getTrans('Start KP'); ?>:<code class="pull-right"><?= isset($data['startKp'])?$data['startKp']:"";?></code></td>
                                        <td><?= Yii::$app->trans->getTrans('End KP'); ?>:<code class="pull-right"><?= isset($data['endKp'])?$data['endKp']:"";?></code></td>
                                    </tr>
                                    <tr>
                                        <td><?= Yii::$app->trans->getTrans('Start Weld Number'); ?>:<code class="pull-right"><?= isset($data['startWeld'])?$data['startWeld']:"";?></code></td>
                                        <td><?= Yii::$app->trans->getTrans('End Weld Number'); ?>:<code class="pull-right"><?= isset($data['endWeld'])?$data['endWeld']:"";?></code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="selectiondetail-tabel">
                        <div class="performed-tabel-title"><?= Yii::$app->trans->getTrans('QA QC Checks to be performed'); ?>:</div>
                        <div class="selectiondetailtable">
                            <table class="table table-selectiondetail performed">
                                <thead>
                                    <tr>
                                        <th><?= Yii::$app->trans->getTrans('Check'); ?></th>
                                        <th><?= Yii::$app->trans->getTrans('Check Accepted'); ?></th>
                                        <th><?= Yii::$app->trans->getTrans('By'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?= Yii::$app->trans->getTrans('All Weld Data Received'); ?></td>
                                        <td><?= !empty($data['weldCheck'])?$data['weldCheck']:"";?></td>
                                        <td><?php echo Yii::$app->user->identity->username;?></td>
                                    </tr>
                                    <tr>
                                        <td><?= Yii::$app->trans->getTrans('All NDT Data received'); ?></td>
                                        <td><?= !empty($data['ndtCheck'])?$data['ndtCheck']:"";?></td>
                                        <td><?php echo Yii::$app->user->identity->username;?></td>
                                    </tr>
                                    <tr>
                                        <td><?= Yii::$app->trans->getTrans('No outstanding repairs'); ?></td>
                                        <td><?= !empty($data['repairCheck'])?$data['repairCheck']:"";?></td>
                                        <td><?php echo Yii::$app->user->identity->username;?></td>
                                    </tr>
                                    <tr>
                                        <td><?= Yii::$app->trans->getTrans('Coating Performed'); ?></td>
                                        <td><?= !empty($data['coatingCheck'])?$data['coatingCheck']:"";?></td>
                                        <td><?php echo Yii::$app->user->identity->username;?></td>
                                    </tr>
                                    <tr>
                                        <td><?= Yii::$app->trans->getTrans('Coating Accepted'); ?></td>
                                        <td><?= !empty($data['coatingAccepted'])?$data['coatingAccepted']:"";?></td>
                                        <td><?php echo Yii::$app->user->identity->username;?></td>
                                    </tr>
                                    <tr>
                                        <td><?= Yii::$app->trans->getTrans('Anomaly Report Checked and no items outstanding'); ?></td>
                                        <td><?= !empty($data['anomalyCheck'])?$data['anomalyCheck']:"";?></td>
                                        <td><?php echo Yii::$app->user->identity->username;?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="performed-tabel-text">
                            <?= Yii::$app->trans->getTrans('Checks listed above are for mainline welds, tie-ins will be cleared on a case by case basis, joint to be signed off for welding visual, NDT and Coating prior to backfill'); ?>
                        </div>
                    </div>
                    <div class="remark-div"><?= Yii::$app->trans->getTrans('Remarks'); ?>:</div>
                    <div class="all-br">
                        <div class="br-line"> </div>
                        <div class="br-line"> </div>
                        <div class="br-line"> </div>
                        <div class="br-line"> </div>
                        <div class="br-line"> </div>
                        <div class="br-line"> </div>
                    </div>
                    <div class="detail-tabel-title add-br"><?= Yii::$app->trans->getTrans('Authorization to Proceed'); ?></div>
                    <div class="Authorisationto-table-div">
                        <table class="table table-selectiondetail Authorisationto">
                            <tbody>
                                <tr>
                                    <td><?= Yii::$app->trans->getTrans('QA'); ?></td>
                                    <td><?= Yii::$app->trans->getTrans('Name'); ?>:</td>
                                    <td><?= Yii::$app->trans->getTrans('Sign'); ?>:</td>
                                    <td><?= Yii::$app->trans->getTrans('Date'); ?>:</td>
                                </tr>
                                <tr>
                                    <td><?= Yii::$app->trans->getTrans('Construction Manager'); ?></td>
                                    <td><?= Yii::$app->trans->getTrans('Name'); ?>:</td>
                                    <td><?= Yii::$app->trans->getTrans('Sign'); ?>:</td>
                                    <td><?= Yii::$app->trans->getTrans('Date'); ?>:</td>
                                </tr>
                                <tr>
                                    <td><?= Yii::$app->trans->getTrans('Witness'); ?></td>
                                    <td><?= Yii::$app->trans->getTrans('Name'); ?>:</td>
                                    <td><?= Yii::$app->trans->getTrans('Sign'); ?>:</td>
                                    <td><?= Yii::$app->trans->getTrans('Date'); ?>:</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>