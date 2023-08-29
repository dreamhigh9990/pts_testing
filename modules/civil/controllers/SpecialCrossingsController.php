<?php

namespace app\modules\civil\controllers;

use Yii;
use app\models\SpecialCrossings;
use app\models\SpecialCrossingsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SpecialCrossingsController implements the CRUD actions for SpecialCrossings model.
 */
class SpecialCrossingsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [                    
                    // allow authenticated users
                    [
                        'allow' => true,
                        'actions' => ['create','delete'],
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * Lists all SpecialCrossings models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SpecialCrossingsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new SpecialCrossings model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($EditId = "")
    {
        //################ Add #####################
        $model = new SpecialCrossings();
        
        //################ Edit #####################
        if(!empty($EditId)){
            $model = Yii::$app->general->getModelData('\app\models\SpecialCrossings', $EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']);
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
        }

        //################ Save  #####################
        $model = Yii::$app->general->reportNo($model, 'SPC');
        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post();
            if(isset($postData['SpecialCrossings']['check_points']) && $postData['SpecialCrossings']['check_points'] == ''){
                $model->check_points = '';
            } else {
                $model->check_points = $postData['SpecialCrossings']['check_points'];
            }

            if($model->validate() && $model->save()){
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                    $Data = SpecialCrossings::find()->where(['id'=>$model->id])->asArray()->one(); 
                }              
                echo json_encode(array('status' => true, 'modelData' => $Data));die;
            }else{
                echo json_encode(array('status' => false, 'message' => $model->errors));die;
            } 
        }

        //################ Render to view #####################
        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
