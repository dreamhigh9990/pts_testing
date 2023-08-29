<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

$perpage = \nterms\pagesize\PageSize::widget(['class'=>'form-control']);
?>
<!-- <div class="table-responsive"> -->
    
<?php
echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		    "filterSelector" => "select[name='per-page']",
	     	"layout" => "<div class='row mb-1'><div class='col-md-6'>{summary}</div><div class='col-md-6 right'>{$perpage}</div></div>\n<div class='table-responsive long'>{items}</div>\n{pager}",
        'columns' => [  
            [
                'attribute' => 'heat_number',
                 'label' => "Heat Number",
                'filter' => Yii::$app->general->pipeHeatList(),
            ],       
            'pipe_number',            
            'is_active',
            'why_anomally',
            'wall_thikness',
            'weight',
            'yeild_strength',
            'length',
            'od',
            'coating_type',
            'plate_number',
            [
                'attribute' => 'created_by',
                 'label' => "User",
                'filter' =>Yii::$app->general->employeeList(""),
                'value' => function ($model) {
                            $List = Yii::$app->general->employeeList("");	
                            return !empty($List[$model->created_by])?$List[$model->created_by]:"";
                     },
            ],
            'updated_at:datetime',
           
        ],
]); ?>
<!-- </div> -->