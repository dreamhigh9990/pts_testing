<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\TaxonomyValue;
$this->title = "Anomaly";
if(!empty($_GET['model'])){
    $aModel = str_replace('Search','',$_GET['model']);
}
?>
<?php Pjax::begin(['id'=>"idofpjaxcontainer"]); ?>
<div class="col-md-12">
    <section id="basic-tabs-components">
        <div class="row match-height">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                    <?= $this->render('_Tab');?>
                    </div>                    
                    <div class="card-body">
                        <div class="col-12 pl-1">
                            <div class="form-group">
                                <button type="button" url="admin/anomaly/delete-anomaly-record?model=app\models\<?= $aModel; ?>" class="mb-1 btn btn-raised btn-outline-danger btn-min-width delete-multipe"><i class="fa fa-times"></i> <?= Yii::$app->trans->getTrans('Delete selected'); ?></button>
                            </div>
                        </div>
                        <?php
                            $searchModel ='\\app\models\\'.$model;
                            $searchModel = new $searchModel;
                            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                        
                            echo $this->render('_'.$model, [
                                'searchModel' => $searchModel,
                                'dataProvider' => $dataProvider,
                            ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php Pjax::end(); ?>