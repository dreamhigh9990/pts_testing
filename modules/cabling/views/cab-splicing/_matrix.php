
<?php 
use yii\helpers\Html;
if ($model->isNewRecord || empty($model->colour)){
    $color = ['Blue','Orange','Green','Brown','Grey','White','Red','Black'];
    $sub = [
            'Blue'=>[
                'Input'=>"",
                'Output'=>"",
                'Calibration'=>"",
                "Loss"=>"",
                "Remarks"=>"",
            ],
            'Orange'=>[
                'Input'=>"",
                'Output'=>"",
                'Calibration'=>"",
                "Loss"=>"",
                "Remarks"=>"",
            ],
            'Green'=>[
                'Input'=>"",
                'Output'=>"",
                'Calibration'=>"",
                "Loss"=>"",
                "Remarks"=>"",
            ],
            'Brown'=>[
                'Input'=>"",
                'Output'=>"",
                'Calibration'=>"",
                "Loss"=>"",
                "Remarks"=>"",
            ],
            'Grey'=>[
                'Input'=>"",
                'Output'=>"",
                'Calibration'=>"",
                "Loss"=>"",
                "Remarks"=>"",
            ],
            'White'=>[
                'Input'=>"",
                'Output'=>"",
                'Calibration'=>"",
                "Loss"=>"",
                "Remarks"=>"",
            ],
            'Red'=>[
                'Input'=>"",
                'Output'=>"",
                'Calibration'=>"",
                "Loss"=>"",
                "Remarks"=>"",
            ],
            'Black'=>[
                'Input'=>"",
                'Output'=>"",
                'Calibration'=>"",
                "Loss"=>"",
                "Remarks"=>"",
            ],
            'Yellow'=>[
                'Input'=>"",
                'Output'=>"",
                'Calibration'=>"",
                "Loss"=>"",
                "Remarks"=>"",
            ],
            'Violet'=>[
                'Input'=>"",
                'Output'=>"",
                'Calibration'=>"",
                "Loss"=>"",
                "Remarks"=>"",
            ],
            'Pink'=>[
                'Input'=>"",
                'Output'=>"",
                'Calibration'=>"",
                "Loss"=>"",
                "Remarks"=>"",
            ],
            'Aqua'=>[
                'Input'=>"",
                'Output'=>"",
                'Calibration'=>"",
                "Loss"=>"",
                "Remarks"=>"",
            ]
    ];
    foreach($color as $i){
        $ColourArray[$i] = $sub;
    }
} else{
    $ColourArray = $model->colour;
}         
if(!empty($ColourArray)){                 
foreach($ColourArray as $colour => $items){
    $colorMainLabel = Yii::$app->trans->getTrans($colour);
?>
 <div class="col-md-3 col-sm-6 col-xs-12 clearfix"> 
    <table class="table table-responsive matrix-table">
        <thead class="thead-default">
            <tr style="background: <?= $colour;?>;color: #<?= $colour=="White"?"000":"fff"?>;    border: 1px solid;">
                <td colspan="6" >
                    <h3><?= $colorMainLabel; ?></h3>
                </td>
            </tr>
            <tr class="">                                    
                <th><?= Yii::$app->trans->getTrans('Fiber'); ?></th>
                <th><?= Yii::$app->trans->getTrans('Input'); ?></th>
                <th><?= Yii::$app->trans->getTrans('Output'); ?></th>
                <th><?= Yii::$app->trans->getTrans('Calibration'); ?></th>
                <th><?= Yii::$app->trans->getTrans('Loss'); ?></th>
                <th><?= Yii::$app->trans->getTrans('Remarks'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($items as $color => $items){ 
                $dis = Yii::$app->general->isAllowed() ? "disabled='disabled'" : "";
                $colorSubLabel = Yii::$app->trans->getTrans($color);
            ?>    
                <tr>
                    <td><?= $colorSubLabel;?></td>
                    <td><input type="text" class="matrix-input" name="CabSplicing[colour][<?=$colour;?>][<?=$color;?>][Input]"  value="<?=$ColourArray[$colour][$color]['Input'];?>" <?= $dis ?> ></td>
                    <td><input type="text" class="matrix-input" name="CabSplicing[colour][<?=$colour;?>][<?=$color;?>][Output]" value="<?=$ColourArray[$colour][$color]['Output'];?>" <?= $dis ?>></td>
                    <td><input type="text" class="matrix-input" name="CabSplicing[colour][<?=$colour;?>][<?=$color;?>][Calibration]"value="<?=$ColourArray[$colour][$color]['Calibration'];?>" <?= $dis ?>></td>
                    <td><input type="text" class="matrix-input" name="CabSplicing[colour][<?=$colour;?>][<?=$color;?>][Loss]" value="<?=$ColourArray[$colour][$color]['Loss'];?>" <?= $dis ?>></td>
                    <td><input type="text" class="matrix-input" name="CabSplicing[colour][<?=$colour;?>][<?=$color;?>][Remarks]"value="<?=$ColourArray[$colour][$color]['Remarks'];?>" <?= $dis ?>></td>
                </tr>
            <?php } ?>
        </tbody>
   </table> 
</div>
 <?php  }} ?>