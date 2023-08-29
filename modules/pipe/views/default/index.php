<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = "Dashboard";
?>

<div class="card-body card"> 
    <?php if(Yii::$app->user->identity->type == "Admin"){ ?>
    <div class="col-xl-12 col-lg-12 col-12">
        <section id="minimal-statistics-bg">
            <div class="row">
                <div class="col-12 mt-3 mb-1">
                    <div class="content-header">System Section</div>
                </div>
            </div>
            
            <div class="row">
                
                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/admin/project/create']); ?>">
                        <div class="card bg-primary">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Project</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="ft-folder white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/admin/employee/create']); ?>">
                        <div class="card bg-warning">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>User</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="ft-user white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/admin/taxonomy/create']); ?>">
                        <div class="card bg-success">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Catalogue</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="fa fa-sitemap white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/admin/line/create']); ?>">
                        <div class="card bg-danger">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Line List</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-graph white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/admin/landowner/create']); ?>">
                        <div class="card bg-success">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Landowner</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="ft-users white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </div>
    <?php } ?>
    <div class="col-xl-12 col-lg-12 col-12">
        <section id="minimal-statistics-bg">
            <div class="row">
                <div class="col-12 mt-3 mb-1">
                    <div class="content-header">Pipe Section</div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/pipe/pipe/create']); ?>">
                        <div class="card bg-warning">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Pipe</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-graph white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/pipe/reception/create']); ?>">
                        <div class="card bg-success">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Reception</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-doc white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/pipe/pipe-cleargrade/create']); ?>">
                        <div class="card bg-danger">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Clear & Grade</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-support white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/pipe/stringing/create']); ?>">
                        <div class="card bg-primary">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Stringing</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="ft-minus white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>                
            </div>

            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/pipe/pipe-transfer/create']); ?>">
                        <div class="card bg-danger">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Transfer</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="fa fa-exchange white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/pipe/bending/create']); ?>">
                        <div class="card bg-warning">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Bending</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="fa fa-angle-right white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/pipe/cutting/create']); ?>">
                        <div class="card bg-primary">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Cutting</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="fa fa-cut white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </div>

    <div class="col-xl-12 col-lg-12 col-12">
        <section id="minimal-statistics-bg">
            <div class="row">
                <div class="col-12 mt-3 mb-1">
                    <div class="content-header">Welding Section</div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-danger">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Welding</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-graph white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-warning">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Coating Production</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-graph white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-success">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Coating Repair</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-doc white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-danger">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>NDT</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="fa fa-exchange white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-primary">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Parameter Check</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-support white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-danger">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Repair</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="ft-minus white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </div>

    <div class="col-xl-12 col-lg-12 col-12">
        <section id="minimal-statistics-bg">
            <div class="row">
                <div class="col-12 mt-3 mb-1">
                    <div class="content-header">Civil Section</div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-warning">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Backfilling</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-graph white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-success">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Lowering</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-doc white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-danger">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Reinstatement</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="fa fa-exchange white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-primary">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Trenching</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-support white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </div>

    <div class="col-xl-12 col-lg-12 col-12">
        <section id="minimal-statistics-bg">
            <div class="row">
                <div class="col-12 mt-3 mb-1">
                    <div class="content-header">Pre Commissioning Section</div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-warning">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Cathodic Protection</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-graph white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-success">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Clean & Gauge</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-doc white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-danger">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Hydrotesting</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="fa fa-exchange white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-primary">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>DCVG Surveying</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-support white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </div>

    <div class="col-xl-12 col-lg-12 col-12">
        <section id="minimal-statistics-bg">
            <div class="row">
                <div class="col-12 mt-3 mb-1">
                    <div class="content-header">Cable Section</div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-warning">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Drum</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-graph white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-success">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Lowering</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-doc white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-danger">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Splice</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="fa fa-exchange white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-6 col-12 col-sm-6">
                    <a href="<?php echo Url::to(['/#']); ?>">
                        <div class="card bg-primary">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body white text-left">
                                            <h3>Stringing</h3>
                                        </div>
                                        <div class="media-right align-self-center">
                                            <i class="icon-support white font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </div>
</div>