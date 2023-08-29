<?php
use yii\helpers\Html;
if(Yii::$app->general->hasEditAccess($created_by)){
    $icon = 'pencil';
    if(Yii::$app->user->identity->type == 'Client') $icon = 'eye-open';
    echo Html::a('<span class="glyphicon glyphicon-'.$icon.'"></span>', $url, ['title' => Yii::t('app', 'lead-update')]);
} else {
    $url = Yii::$app->general->clientViewFilter($url);
    echo Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, ['title' => Yii::t('app', 'lead-update')]);
}