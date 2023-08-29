<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<ul class="list-group">
    <?php
    $Classes = [
        ['model' => '\app\models\Reception', 'url' => '/pipe/reception/create', 'des' => Yii::$app->trans->getTrans('Receipted').'?'],
        ['model' => '\app\models\Stringing', 'url' => '/pipe/stringing/create', 'des' => Yii::$app->trans->getTrans('Strung').'?'],
        ['model' => '\app\models\Cutting', 'url' => '/pipe/cutting/create', 'des' => Yii::$app->trans->getTrans('Cut').'?'],
        ['model' => '\app\models\Bending', 'url' => '/pipe/bending/create', 'des' => Yii::$app->trans->getTrans('Bent').'?']
    ];
    foreach($Classes as  $ClassName){
        if ($ClassName['model']=="\app\models\Cutting") {
            $PipeNumber =  explode("/",$model->pipe_number);
            $PipeNumber =  $PipeNumber[0];
        }else{
            $PipeNumber =  $model->pipe_number;
        }

        $Loaded = $ClassName['model']::find()->where(['pipe_number'=>$PipeNumber])->active()->one();
        if($Loaded){
    ?>
        <li class="list-group-item">
            <b class="float-right">Yes</b> <?php echo $ClassName['des'];?> 
        </li>
        <li class="list-group-item mb-3">
            <?php if($ClassName['model']=="\app\models\Bending"){ ?>
                <div class="row">
                    <div class="col-6">
                        <?= Yii::$app->trans->getTrans('Report Number'); ?>
                    </div>
                    <div class="col-6">
                        <?php
                        $bedtList = $ClassName['model']::find()->where(['pipe_number'=>$PipeNumber])->active()->all();
                        foreach ($bedtList as $key => $value) {
                            echo Html::a($value->report_number,[$ClassName['url'],'EditId'=>$value->id],['class'=>"card-link float-right"]);
                        }
                        ?>
                    </div>
                </div>
            <?php } else { ?>
                <?= Yii::$app->trans->getTrans('Report Number'); ?>
                <?= Html::a($Loaded->report_number,[$ClassName['url'],'EditId'=>$Loaded->id],['class'=>"card-link float-right"]);?>
            <?php } ?>
        </li>
    <?php } else { ?>
        <li class="list-group-item">
            <b class="float-right">No</b> <?php echo $ClassName['des'];?> 
        </li>
    <?php
        }
    }
    $CurrentPipeWeld = \app\models\Welding::find()->where(['pipe_number'=>$model->pipe_number])->active()->one();
    $PrevPipe = \app\models\Welding::find()->where(['next_pipe'=>$model->pipe_number])->active()->one();
?>
<li class="list-group-item">
    <b class="float-right">
        <?php
        if(!empty($PrevPipe) && !empty($CurrentPipeWeld)){
            echo'Both';
        }
        ?>
        <?php
        if(empty($PrevPipe) && empty($CurrentPipeWeld)){
            echo'No';
        }else if(!empty($PrevPipe) && empty($CurrentPipeWeld)){
            echo'Single Side';
        }else if(empty($PrevPipe) && !empty($CurrentPipeWeld)){
            echo'Single Side';
        }
        ?>
    </b> <?= Yii::$app->trans->getTrans('Welded').'?'; ?>
</li>    
<li class="list-group-item">
    <div class="row">
        <?php if(!empty($PrevPipe) && !empty($CurrentPipeWeld)){?>
        <div class="col-md-6">
            <ul class="list-group">
                <li class="list-group-item mb-3">
                    <b class="float-right"><?= $PrevPipe['pipe_number'];?></b> <?= Yii::$app->trans->getTrans('Prev Pipe Number'); ?>
                </li>
                <li class="list-group-item mb-3">
                    <?= Yii::$app->trans->getTrans('Prev Weld Number'); ?>
                    <?= Html::a($PrevPipe['weld_number'],['/welding/welding/create','EditId'=>$PrevPipe['id']],['class'=>"card-link float-right"]);?>
                </li>
                <li class="list-group-item mb-3">
                    <b class="float-right"><?= $PrevPipe['kp'];?></b> <?= Yii::$app->trans->getTrans('KP'); ?>
                </li>
                <li class="list-group-item mb-3">
                    <? Yii::$app->trans->getTrans('Weld Report'); ?>
                    <?= Html::a($PrevPipe['report_number'],['/welding/welding/create','EditId'=>$PrevPipe['id']],['class'=>"card-link float-right"]);?>
                </li>
            </ul>
        </div> 
        <div class="col-md-6">
            <ul class="list-group">
                <li class="list-group-item mb-3">
                    <b class="float-right"><?= $CurrentPipeWeld['next_pipe'];?></b> <?= Yii::$app->trans->getTrans('Next Pipe Number'); ?>
                </li>
                <li class="list-group-item mb-3">
                    <?= Yii::$app->trans->getTrans('Next Weld Number'); ?>
                    <?= Html::a($CurrentPipeWeld['weld_number'],['/welding/welding/create','EditId'=>$CurrentPipeWeld['id']],['class'=>"card-link float-right"]);?>
                </li>
                <li class="list-group-item mb-3">
                    <b class="float-right"><?= $CurrentPipeWeld['kp'];?></b> <?= Yii::$app->trans->getTrans('KP'); ?>
                </li>
                <li class="list-group-item mb-3">
                    <?= Yii::$app->trans->getTrans('Weld Report'); ?>
                    <?= Html::a($CurrentPipeWeld['report_number'],['/welding/welding/create','EditId'=>$CurrentPipeWeld['id']],['class'=>"card-link float-right"]);?>
                </li>
            </ul>
        </div>
        <?php } else if(empty($CurrentPipeWeld) && empty($PrevPipe)){ ?>

        <?php } else if(!empty($CurrentPipeWeld) && empty($PrevPipe)){?>
        <div class="col-md-12">
            <ul class="list-group">
                <li class="list-group-item mb-3">
                    <b class="float-right"><?= $CurrentPipeWeld['next_pipe'];?></b> <?= Yii::$app->trans->getTrans('Next Pipe Number'); ?>
                </li>
                <li class="list-group-item mb-3">
                    <?= Yii::$app->trans->getTrans('Next Weld Number'); ?>
                    <?= Html::a($CurrentPipeWeld['weld_number'],['/welding/welding/create','EditId'=>$CurrentPipeWeld['id']],['class'=>"card-link float-right"]);?>
                </li>
                <li class="list-group-item mb-3">
                    <b class="float-right"><?= $CurrentPipeWeld['kp'];?></b><?= Yii::$app->trans->getTrans('KP'); ?>
                </li>
                <li class="list-group-item mb-3">
                    <?= Yii::$app->trans->getTrans('Weld Report'); ?>
                    <?= Html::a($CurrentPipeWeld['report_number'],['/welding/welding/create','EditId'=>$CurrentPipeWeld['id']],['class'=>"card-link float-right"]);?>
                </li>
            </ul>
        </div>
        <?php }  else if(empty($CurrentPipeWeld) && !empty($PrevPipe)){?>
        <div class="col-md-12">
            <ul class="list-group">
                <li class="list-group-item mb-3">
                    <b class="float-right"><?= $PrevPipe['pipe_number'];?></b><?= Yii::$app->trans->getTrans('Prev Pipe Number'); ?>
                </li>
                <li class="list-group-item mb-3">
                    <?= Yii::$app->trans->getTrans('Prev Weld Number'); ?>
                    <?= Html::a($PrevPipe['weld_number'],['/welding/welding/create','EditId'=>$PrevPipe['id']],['class'=>"card-link float-right"]);?>
                </li>
                <li class="list-group-item mb-3">
                    <b class="float-right"><?= $PrevPipe['kp'];?></b><?= Yii::$app->trans->getTrans('KP'); ?>
                </li>
                <li class="list-group-item mb-3">
                    <?= Yii::$app->trans->getTrans('Weld Report'); ?>
                    <?= Html::a($PrevPipe['report_number'],['/welding/welding/create','EditId'=>$PrevPipe['id']],['class'=>"card-link float-right"]);?>
                </li>
            </ul>
        </div>
        <?php } ?>
    </div>
</li>
</ul>