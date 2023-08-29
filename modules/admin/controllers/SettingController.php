<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Setting;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
class SettingController extends Controller
{
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [                    
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    public function actionCreate()
    {
        $model = Setting::find()->where(['id'=>1])->one();

        if (Yii::$app->request->isPost) {
            $model->value = UploadedFile::getInstance($model, 'value');

            if ($model->value && $model->validate() && $model->save()) {                
                $model->value->saveAs('images/site/' . $model->value->baseName . '.' . $model->value->extension);
                $res['status']  = true;
                $res['message'] = 'success';
            }else{
                $res['status']  = false;
                $res['message'] = $model->errors;
            }
            echo json_encode($res);die;
        }


        return $this->render('_form', [
            'model' => $model,
        ]);
    }
}
