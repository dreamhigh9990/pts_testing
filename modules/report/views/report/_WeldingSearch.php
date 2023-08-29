<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use yii\data\ActiveDataProvider;
use yii\widgets\Pjax;

$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
$this->title = "Open End Summary";
$Pipes = \app\models\Welding::find()->select(['next_pipe','pipe_number'])->where(['has_been_cut_out' => 'No'])->active()->asArray()->all();
$pipe_number = ArrayHelper::map($Pipes,'pipe_number','pipe_number');
$next_pipe = ArrayHelper::map($Pipes,'next_pipe','next_pipe');

$OpenEndPipeList = array_merge(array_diff($pipe_number,$next_pipe),array_diff($next_pipe,$pipe_number));

$query = \app\models\Welding::find()->where(['OR',['IN','pipe_number',$OpenEndPipeList],['IN','next_pipe',$OpenEndPipeList]])->andWhere(['has_been_cut_out' => 'No'])->active();
$dataProvider = new ActiveDataProvider([
    'query' => $query,
    'pagination' => [
        'pageSize' => !empty($_GET['per-page']) ? $_GET['per-page'] : 10,
    ],
    'sort' => [],
]);
if(!empty($_GET['download'])){
    Yii::$app->general->globalDownload($query);
 }
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<!-- <div class="col-md-12"> -->
    <section id="basic-tabs-components">   
        <div class="row match-height">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><?= Yii::$app->trans->getTrans('Open End Summary'); ?>
                        <?= Yii::$app->general->generateReport();?>
                        </h4>
                    </div>            
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php    
                                echo GridView::widget([
                                    'dataProvider' => $dataProvider,
                                    'filterModel' => '',
                                    "filterSelector" => "select[name='per-page']",
                                    "layout" => "<div class='row mb-1'></div>\n{items}\n{pager}",
                                    'summary' => Yii::$app->general->getSummaryText(array('start' => "{begin}", 'end' => "{end}", 'total' => "{totalCount}")),
                                    'layout' => '<div class="row mb-1"><div class="col-md-6"><div class="summary">{summary}</div></div><div class="col-md-6 right">'.$perpage.'</div></div>'."\n".'<div class="table-responsive long">{items}</div>'."\n".'{pager}',
                                    'emptyText' => Yii::$app->trans->getTrans('No results found.'),
                                    'columns' => [
                                        'date',
                                        [
                                            'attribute' => 'report_number',
                                            'filter' => true,
                                            'format' => 'raw',
                                            'value' => function ($model) {
                                                return Html::a($model->report_number,['/welding/welding/create','EditId'=>$model->id],['class'=>"card-link",'data-pjax'=>0,'target'=>'_blank']);
                                            },
                                        ],
                                        'line_type',
                                        'kp',
                                        'weld_number',
                                        [
                                            'attribute' => 'pipe_number',
                                            'format'=>'raw',                                           
                                            'value' => function ($model) {
                                                $nextWeldDetails = \app\models\Welding::find()->where(['pipe_number' => $model->next_pipe])->active()->asArray()->one();
                                                if(!empty($nextWeldDetails)){
                                                    return '<b>'.$model->pipe_number.'</b>';
                                                } else {
                                                    return $model->pipe_number;
                                                }
                                            },
                                        ],
                                        [
                                            'attribute' => 'next_pipe',  
                                            'format'=>'raw',                                         
                                            'value' => function ($model) {
                                                $nextWeldDetails = \app\models\Welding::find()->where(['pipe_number' => $model->next_pipe])->active()->asArray()->one();
                                                if(empty($nextWeldDetails)){
                                                    return '<b>'.$model->next_pipe.'</b>';
                                                } else {
                                                    return $model->next_pipe;
                                                }
                                            },
                                        ],
                                    ],
                                ]); 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<!-- </div> -->
<?php Pjax::end(); ?>