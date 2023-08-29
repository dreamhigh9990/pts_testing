<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\VehicleSchedule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vehicle-schedule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'report_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'project_id')->textInput() ?>

    <?= $form->field($model, 'location')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sca_unit_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vehicle_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vehicle_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inspection_frequency')->dropDownList([ 'Weekly' => 'Weekly', 'Monthly' => 'Monthly', 'Quarterly' => 'Quarterly', 'Half Yearly' => 'Half Yearly', 'Yearly' => 'Yearly', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'part_list')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'signed_off')->dropDownList([ 'Yes' => 'Yes', 'No' => 'No', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'is_deleted')->textInput() ?>

    <?= $form->field($model, 'is_active')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
