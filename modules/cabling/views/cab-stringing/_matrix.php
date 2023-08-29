
<?php 
use yii\helpers\Html;
if ($model->isNewRecord || empty($model->colour)){
    $color = ['Blue','Orange','Green','Brown','Grey','White','Red','Black'];
    $sub = [
            'Blue'=>[
                'Attenution_1310'=>"",
                'Attenution_1550'=>"",
                'Status'=>""
            ],
            'Orange'=>[
                'Attenution_1310'=>"",
                'Attenution_1550'=>"",
                'Status'=>""
            ],
            'Green'=>[
                'Attenution_1310'=>"",
                'Attenution_1550'=>"",
                'Status'=>""
            ],
            'Brown'=>[
                'Attenution_1310'=>"",
                'Attenution_1550'=>"",
                'Status'=>""
            ],
            'Grey'=>[
                'Attenution_1310'=>"",
                'Attenution_1550'=>"",
                'Status'=>""
            ],
            'White'=>[
                'Attenution_1310'=>"",
                'Attenution_1550'=>"",
                'Status'=>""
            ],
            'Red'=>[
                'Attenution_1310'=>"",
                'Attenution_1550'=>"",
                'Status'=>""
            ],
            'Black'=>[
                'Attenution_1310'=>"",
                'Attenution_1550'=>"",
                'Status'=>""
            ],
            'Yellow'=>[
                'Attenution_1310'=>"",
                'Attenution_1550'=>"",
                'Status'=>""
            ],
            'Violet'=>[
                'Attenution_1310'=>"",
                'Attenution_1550'=>"",
                'Status'=>""
            ],
            'Pink'=>[
                'Attenution_1310'=>"",
                'Attenution_1550'=>"",
                'Status'=>""
            ],
            'Aqua'=>[
                'Attenution_1310'=>"",
                'Attenution_1550'=>"",
                'Status'=>""
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
                <td colspan="4" >
                    <h3><?= $colorMainLabel;?></h3>
                </td>
            </tr>
            <tr class="">                                    
                <th><?= Yii::$app->trans->getTrans('Fiber'); ?></th>
                <th><?= Yii::$app->trans->getTrans('1310 Attenution'); ?></th>
                <th><?= Yii::$app->trans->getTrans('1550 Attenution'); ?></th>
                <th><?= Yii::$app->trans->getTrans('Status'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $color => $items){ 
                $dis = Yii::$app->general->isAllowed()?"disabled='disabled'":"";
                $disabled = Yii::$app->general->isAllowed()?true:false;
                $colorSubLabel = Yii::$app->trans->getTrans($color);
            ?>    
                <tr>
                    <td><?= $colorSubLabel; ?></td>
                    <td><input type="text" class="matrix-input" name="CabStringing[colour][<?=$colour;?>][<?=$color;?>][Attenution_1310]"  value="<?=$ColourArray[$colour][$color]['Attenution_1310'];?>"  <?= $dis ?> ></td>
                    <td><input type="text" class="matrix-input" name="CabStringing[colour][<?=$colour;?>][<?=$color;?>][Attenution_1550]"  value="<?=$ColourArray[$colour][$color]['Attenution_1550'];?>"  <?= $dis ?> ></td>
                    <td>                                          
                        <?= Html::dropDownList('CabStringing[colour]['.$colour.']['.$color.'][Status]', $ColourArray[$colour][$color]['Status'], ['Accept'=>'Accept','Reject'=>'Reject'],['prompt'=>'Select','disabled'=>$disabled]); ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
   </table> 
</div>
 <?php  }} ?>