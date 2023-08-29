<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
?>
<!-- <div class="table-responsive"> -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        "filterSelector" => "select[name='per-page']",
        'summary' => Yii::$app->general->getSummaryText(array('start' => "{begin}", 'end' => "{end}", 'total' => "{totalCount}")),
        'layout' => '<div class="row mb-1"><div class="col-md-6"><div class="summary">{summary}</div></div><div class="col-md-6 right">'.$perpage.'</div></div>'."\n".'<div class="table-responsive long">{items}</div>'."\n".'{pager}',
        'emptyText' => Yii::$app->trans->getTrans('No results found.'),
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        $url = Url::to(['/welding/production/create', 'EditId' => $model->id]);
                        if(!empty($_GET)){
                            $urlParams = Yii::$app->general->getFilters();
                            $urlParams[] = '/welding/production/create';
                            $urlParams['EditId'] = $model->id;
                            $url = Url::to($urlParams);
                        }
                        return $this->render('//partials/_clientView', ['url'=>$url, 'created_by' => $model->created_by]);
                        
                    },
                ],
            ],
            [
                'attribute' => 'date',
                'format' => 'raw',
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'name' => 'ProductionSearch[date]',
                    'convertFormat'=>true,
                    'value' => $searchModel->date,
                    'pluginOptions'=>[
                        'locale'=>[
                            'format'=>'Y-m-d',
                            'separator'=>' / ',
                        ],
                    ]
                ]) 
            ],
            'report_number',
            'kp',
            'weld_number',
            [
                'attribute' => 'kp',
                'label' => Yii::$app->trans->getTrans('Weld Type'),
                'filter' => false,
                'value' => function($model){
                    $weldData = \app\models\Welding::find()->select(['weld_type'])->where(['AND',['=', 'weld_number', $model->weld_number],['=','kp', $model->kp],['=','has_been_cut_out','No']])->active()->asArray()->one();
                    return !empty($weldData) ? $weldData['weld_type'] : '-';
                }
            ],
            [
                'attribute' => 'kp',
                'label' => Yii::$app->trans->getTrans('Weld Sub Type'),
                'filter' => false,
                'value' => function($model){
                    $weldData = \app\models\Welding::find()->select(['weld_sub_type'])->where(['AND',['=', 'weld_number', $model->weld_number],['=','kp', $model->kp],['=','has_been_cut_out','No']])->active()->asArray()->one();
                    return !empty($weldData) ? $weldData['weld_sub_type'] : '-';
                }
            ],
            [
                'attribute' => 'dft',
                'filter' => true,
                'value' => function($model){
                    return !empty($model->dft) ? $model->dft : 0;
                }
            ],
            [
                'attribute' => 'dft_2',
                'filter' => true,
                'value' => function($model){
                    return !empty($model->dft_2) ? $model->dft_2 : 0;
                }
            ],
            [
                'attribute' => 'dft_3',
                'filter' => true,
                'value' => function($model){
                    return !empty($model->dft_3) ? $model->dft_3 : 0;
                }
            ],
            [
                'attribute' => 'dft_4',
                'filter' => true,
                'value' => function($model){
                    return !empty($model->dft_4) ? $model->dft_4 : 0;
                }
            ],
            [
                'attribute' => 'dft_5',
                'filter' => true,
                'value' => function($model){
                    return !empty($model->dft_5) ? $model->dft_5 : 0;
                }
            ],
            [
                'attribute' => 'dft_6',
                'filter' => true,
                'value' => function($model){
                    return !empty($model->dft_6) ? $model->dft_6 : 0;
                }
            ],
            [
                'attribute' => 'outcome',
                'filter' => ['Accepted'=>'Accepted', 'Rejected'=>'Rejected']
            ],
            [
                'attribute' => 'created_by',
                'label' => Yii::$app->trans->getTrans('User'),
                'filter' => Yii::$app->general->employeeList(""),
                'value' => function ($model) {
                    $List = Yii::$app->general->employeeList("");	
                    return !empty($List[$model->created_by]) ? $List[$model->created_by] : "";
                },
            ],            
            [
                'attribute' => 'qa_manager',
                'filter' => Yii::$app->general->employeeList(""),
                'value' => function ($model) {
                    $List = Yii::$app->general->employeeList("");		
                    return !empty($List[$model->qa_manager])?$List[$model->qa_manager]:"";
                },
            ],
            [
                'attribute' => 'signed_off',
                'filter' => ['Yes'=>'Yes','No'=>'No']
            ],
        ],
    ]); 
    ?>
<!-- </div> -->