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
                        $url = Url::to(['/welding/coatingrepair/create', 'EditId' => $model->id]);
                        if(!empty($_GET)){
                            $urlParams = Yii::$app->general->getFilters();
                            $urlParams[] = '/welding/coatingrepair/create';
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
                    'name' => 'CoatingrepairSearch[date]',
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
                    $weldList = \app\models\Production::find()->select(['welding.weld_type'])->leftJoin('welding','welding_coating_production.weld_number=welding.weld_number AND welding.project_id=welding_coating_production.project_id AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)->where(['AND',['=', 'welding_coating_production.weld_number', $model->weld_number],['=','welding_coating_production.kp', $model->kp]])->active()->asArray()->one();

                    return !empty($weldList) ? $weldList['weld_type'] : '-';
                }
            ],
            [
                'attribute' => 'kp',
                'label' => Yii::$app->trans->getTrans('Weld Sub Type'),
                'filter' => false,
                'value' => function($model){
                    $weldList = \app\models\Production::find()->select(['welding.weld_sub_type'])->leftJoin('welding','welding_coating_production.weld_number=welding.weld_number AND welding.project_id=welding_coating_production.project_id AND welding.is_active=1 AND welding.is_deleted=0 AND welding.project_id='.Yii::$app->user->identity->project_id)->where(['AND',['=', 'welding_coating_production.weld_number', $model->weld_number],['=','welding_coating_production.kp', $model->kp]])->active()->asArray()->one();

                    return !empty($weldList) ? $weldList['weld_sub_type'] : '-';
                }
            ],
            'pipe_number',
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
                'filter' =>Yii::$app->general->employeeList(""),
                'value' => function ($model) {
                    $List = Yii::$app->general->employeeList("");		
                    return !empty($List[$model->qa_manager])?$List[$model->qa_manager]:"";
                },
            ],
            [
                'attribute' => 'signed_off',
                'filter' =>['Yes'=>'Yes','No'=>'No']
            ],
        ],
    ]); 
    ?>
<!-- </div> -->