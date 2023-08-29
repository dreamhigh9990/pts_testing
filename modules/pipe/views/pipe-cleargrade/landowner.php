<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php
if(!empty($landownerlist)){
    $i = 1;
    foreach($landownerlist as $list){
?>
    <h4>Landowner <?= $i; ?></h4>
    <ul class="list-group mb-1">
        <li class="list-group-item">
            Landholder<b class="float-right"><?= !empty($list['landholder']) ? $list['landholder'] : '-';?></b>
        </li>
        <li class="list-group-item">
            Site Reference<b class="float-right"><?= !empty($list['site_reference']) ? $list['site_reference'] : '-';?></b> 
        </li>
        <li class="list-group-item">
            Fencing Details<b class="float-right"><?= !empty($list['fencing_details']) ? $list['fencing_details'] : '-';?></b> 
        </li>
        <li class="list-group-item">
            Gate Management<b class="float-right"><?= !empty($list['gate_management']) ? $list['gate_management'] : '-';?></b> 
        </li>
        <li class="list-group-item">
            Stock Impact<b class="float-right"><?= !empty($list['stock_impact']) ? $list['stock_impact'] : '-';?></b> 
        </li>
        <li class="list-group-item">
            Vegetation Impact<b class="float-right"><?= !empty($list['vegetation_impact']) ? $list['vegetation_impact'] : '-';?></b> 
        </li>
        <li class="list-group-item">
            Weed Hygiene<b class="float-right"><?= !empty($list['weed_hygiene']) ? $list['weed_hygiene'] : '-';?></b> 
        </li>

        <li class="list-group-item">
            KP (Start - End) <b class="float-right"><?= (isset($list['from_kp']) ? $list['from_kp'] : 'N/A').' - '.(isset($list['to_kp']) ? $list['to_kp'] : 'N/A');?></b> 
        </li>
    </ul>
<?php
    $i++;
    }
}
?>