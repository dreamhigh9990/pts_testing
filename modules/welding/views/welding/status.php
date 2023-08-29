<?php
use yii\helpers\Html;
?>
<ul class="list-group">
    <?php
    $Classes = ['Parameter Checked'=>'\app\models\Parameter','NDT'=>'\app\models\Ndt','Repaired'=>'\app\models\Weldingrepair','Coated'=>'\app\models\Production','Coating Repaired'=>'\app\models\Coatingrepair'];

    $Classes = [
        ['model' => '\app\models\Parameter', 'url' => '/welding/parameter/create', 'des' => Yii::$app->trans->getTrans('Parameter Checked').'?'],
        ['model' => '\app\models\Ndt', 'url' => '/welding/ndt/create', 'des' => Yii::$app->trans->getTrans('NDT\'d').'?'],
        ['model' => '\app\models\Weldingrepair', 'url' => '/welding/weldingrepair/create', 'des' => Yii::$app->trans->getTrans('Repaired').'?'],
        ['model' => '\app\models\Production', 'url' => '/welding/production/create', 'des' => Yii::$app->trans->getTrans('Coated').'?'],
        ['model' => '\app\models\Coatingrepair', 'url' => '/welding/coatingrepair/create', 'des' => Yii::$app->trans->getTrans('Coating Repaired').'?']
    ];

    foreach($Classes as  $ClassName){
        if($ClassName['model'] == '\app\models\Parameter'){
            $Loaded = $ClassName['model']::find()->where(['weld_number' => $model->weld_number, 'kp' => $model->kp])->active()->orderBy('id DESC')->one();
        } else {
            $Loaded = $ClassName['model']::find()->where(['weld_number' => $model->weld_number, 'kp' => $model->kp, 'main_weld_id' => $model->id])->active()->orderBy('id DESC')->one();
        }
        if($Loaded){
    ?>
            <li class="list-group-item">
                <b class="float-right">Yes</b> <?php echo $ClassName['des'];?>
            </li>
            <li class="list-group-item  <?php  if($ClassName['model'] !="\app\models\Ndt") { ?>mb-3 <?php } ?>" >                          
                <b class="float-right"><?= Html::a($Loaded->report_number,[$ClassName['url'],'EditId'=>$Loaded->id],['class'=>"card-link"]);?></b> <?= Yii::$app->trans->getTrans('Report Number'); ?>
            </li>
            <?php  if($ClassName['model'] =="\app\models\Ndt") { ?>
            <li class="list-group-item mb-3">
                <b class="float-right"> <?php echo $Loaded->outcome;?> </b> <?= Yii::$app->trans->getTrans('Result');?>
            </li>
            <?php } ?>
    <?php
        }else {
    ?>
            <li class="list-group-item">
                <b class="float-right">No</b> <?php echo $ClassName['des'];?>
            </li>
    <?php
        }
    }
    ?>
</ul>