<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\TaxonomyValue;
$this->title = "Cable";
$model='PipeSearch';
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="col-md-12">
    <section id="basic-tabs-components">   
    <div class="row match-height">
        <div class="col-xl-12 col-lg-12">
        <div class="card">
            <div class="card-header">
            <h4 class="card-title">Report List</h4>
            </div>
            <div class="card-body">
            <div class="card-block">
                
                <ul class="nav nav-tabs ">
                    <li class="<?= $model=="PipeSearch"?"active":"";?>">
                      <?= Html::a('Heat Report',['/admin/anomaly/index','model'=>'PipeSearch'],['class'=>'nav-link success']);?>
                    </li>
                    <li class="<?= $model=="ReceptionSearch"?"active":"";?>">
                        <?= Html::a('Visual Progress',['/admin/anomaly/index','model'=>'ReceptionSearch'],['class'=>'nav-link success']);?>
                    </li>
                    <li class="<?= $model=="PipeTransferSearch"?"active":"";?>">
                        <?= Html::a('Open End Summary',['/admin/anomaly/index','model'=>'PipeTransferSearch'],['class'=>'nav-link success']);?>
                    </li>
                    <li class="<?= $model=="StringingSearch"?"active":"";?>">
                        <?= Html::a('Clearance',['/admin/anomaly/index','model'=>'StringingSearch'],['class'=>'nav-link success']);?>
                    </li>
                    <li class="<?= $model=="BendingSearch"?"active":"";?>">
                        <?= Html::a('Review Summary',['/admin/anomaly/index','model'=>'BendingSearch'],['class'=>'nav-link success']);?>
                    </li>
                    <li class="<?= $model=="CuttingSearch"?"active":"";?>">
                        <?= Html::a('Weld Book',['/admin/anomaly/index','model'=>'CuttingSearch'],['class'=>'nav-link success']);?>
                    </li>
                    <li class="<?= $model=="CuttingSearch"?"active":"";?>">
                        <?= Html::a('Welder Analysis',['/admin/anomaly/index','model'=>'CuttingSearch'],['class'=>'nav-link success']);?>
                    </li>
                    <li class="<?= $model=="CuttingSearch"?"active":"";?>">
                        <?= Html::a('Daily/ Weekly Production',['/admin/anomaly/index','model'=>'CuttingSearch'],['class'=>'nav-link success']);?>
                    </li>
                    <li class="<?= $model=="CuttingSearch"?"active":"";?>">
                        <?= Html::a('Anomalies',['/admin/anomaly/index','model'=>'CuttingSearch'],['class'=>'nav-link success']);?>
                    </li>
                </ul>
                <div class="tab-content px-1 pt-1">
                    <div role="tabpanel" class="tab-pane active" id="Pipe">
                       
                    </div>
                    <div class="tab-pane" id="Reception">
                        <p>Sugar plum tootsie roll biscuit caramels. Liquorice brownie pastry cotton candy oat cake fruitcake jelly chupa chups. Pudding caramels pastry powder cake soufflé wafer caramels. Jelly-o pie cupcake.</p>
                    </div>
                    <div class="tab-pane" id="Transfer">
                        <p> Transfer Sugar plum tootsie roll biscuit caramels. Liquorice brownie pastry cotton candy oat cake fruitcake jelly chupa chups. Pudding caramels pastry powder cake soufflé wafer caramels. Jelly-o pie cupcake.</p>
                    </div>
                    <div class="tab-pane" id="Stringing">
                        <p>Stringing plum tootsie roll biscuit caramels. Liquorice brownie pastry cotton candy oat cake fruitcake jelly chupa chups. Pudding caramels pastry powder cake soufflé wafer caramels. Jelly-o pie cupcake.</p>
                    </div>
                    <div class="tab-pane" id="Bending">
                        <p>Bending  plum tootsie roll biscuit caramels. Liquorice brownie pastry cotton candy oat cake fruitcake jelly chupa chups. Pudding caramels pastry powder cake soufflé wafer caramels. Jelly-o pie cupcake.</p>
                    </div>
                    <div class="tab-pane" id="Cutting">
                        <p>Cutting plum tootsie roll biscuit caramels. Liquorice brownie pastry cotton candy oat cake fruitcake jelly chupa chups. Pudding caramels pastry powder cake soufflé wafer caramels. Jelly-o pie cupcake.</p>
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