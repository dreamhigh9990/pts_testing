<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<section id="login" class="login-pedding">
    <div class="container-fluid">
        <div class="row full-height-vh">
            <div class="col-12 d-flex align-items-center justify-content-center">
                <div class="card  width-400">
                    <div class="card-img overlap text-center">
                       <?= Html::img("@web/images/site/logo.jpg",['class'=>'mt-5 mb-1','alt'=>'element 06','width'=>'190']);?>
                    </div>
                    <div class="card-body">
                        <div class="card-block">
                            <h6 class="text-center"><b>Please enter your login credentials below</b></h6>
                             <?php $form = ActiveForm::begin(['id' => 'login-form','options'=>['autocomplete'=>'off']]); ?>
                             	  <div class="form-group">
                                    <div class="col-md-12">
										<?= $form->field($model, 'username')->textInput(['autofocus' => true
										,'placeholder'=>"Username"])->label(false); ?>
                					</div>
                                  </div>
                                  <div class="form-group">
                                    <div class="col-md-12">
                               		    <?= $form->field($model, 'password')->passwordInput([
										'placeholder'=>"Password"
										])->label(false); ?>
                					</div>
                                  </div>
                                  <div class="form-group">
                                    <div class="col-md-12">
										<?= Html::submitButton('Login',
                                         ['class' => 'btn btn-pink btn-block btn-success', 
										 'name' => 'login-button']);
                                         ?>
                                     </div>
                                   </div>
           				 <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                   <!-- <div class="card-footer">
                        <div class="float-left"><a>Recover Password</a></div>
                    </div>-->
                </div>
            </div>
        </div>
    </div>
</section>

