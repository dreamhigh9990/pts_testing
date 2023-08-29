<?php

namespace app\modules\welding\controllers;

use Yii;
use app\models\Coatingrepair;
use app\models\CoatingrepairSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CoatingrepairController implements the CRUD actions for Coatingrepair model.
 */
class CoatingrepairController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [                    
                    // allow authenticated users
                    [
                        'allow' => true,
                        'actions' => ['create', 'get-pipe-defects', 'check-valid-weld', 'check-valid-pipe'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }

    /**
     * Creates a new Coatingrepair model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($EditId = "") {
        //################ Add #####################
        $model = new Coatingrepair();
        //################ Edit #####################
        if(!empty($EditId)){
            $model = Yii::$app->general->getModelData('\app\models\Coatingrepair',$EditId);   
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']); 
            }
            if(!Yii::$app->general->hasEditAccess($model->created_by)){
                return $this->redirect(['create']);
            }
            $model->checkpoint = !empty($model->checkpoint)?json_decode($model->checkpoint,true):[];
        }

        if(!empty($_GET['download'])){
            $searchModel = new \app\models\CoatingrepairSearch();
            $searchModel->download = 1;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }

        //################ Save  #####################
        $model = Yii::$app->general->reportNo($model,'COR');
        if ($model->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post();
            if(isset($postData['Coatingrepair']['checkpoint']) && $postData['Coatingrepair']['checkpoint'] == ''){
                $model->checkpoint = '';
            } else {
                $model->checkpoint = $postData['Coatingrepair']['checkpoint'];
            }
            // $model = Yii::$app->anomaly->welding_coatingrepair_anomaly($model,'\app\models\Coatingrepair'); //as per client says anomaly section has been turn off
            if($model->validate() && $model->save()){
                $data = Yii::$app->general->UploadImg($model->id,'Coatingrepair');
                if(!empty($EditId)){                
                    $Data = '';
                }else{
                    $Data = Coatingrepair::find()->where(['id'=>$model->id])->asArray()->one();
                }
                echo json_encode(array('status'=>true,'modelData'=>$Data) );die;
            }else{
                echo json_encode(array('status'=>false,'message'=>$model->errors));die;
            }   
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionGetPipeDefects(){
        if(!empty($_POST['number'])){
            $pipeNumber = $_POST['number'];
            $getPipeDefects = \app\models\Pipe::find()->select(['defects'])->where(['pipe_number' => $pipeNumber])->active()->asArray()->one();
            $defects = [];
            if(!empty($getPipeDefects)){
                $defects = json_decode($getPipeDefects['defects'], true);
            }

            $res = [
                'status' => true,
                'data' => $defects
            ];
        } else {
            $res = [
                'status' => false,
            ];
        }

        echo json_encode($res);
        die;
    }

    function actionCheckValidWeld(){
        if(!empty($_POST['weld']) && !empty($_POST['kp'])){
            $weld = $_POST['weld'];
            $kp = $_POST['kp'];

            $getWeldingData = \app\models\Welding::find()->select(['id'])->where(['weld_number' => $weld, 'kp' => $kp])->active()->asArray()->one();
            $res = [];
            if(!empty($getWeldingData)){
                $res['status'] = true;
            } else {
                $res['status'] = false;
            }

            echo json_encode($res);
            die;
        }
    }

    function actionCheckValidPipe(){
        if(!empty($_POST['number'])){
            $number = $_POST['number'];

            $getPipeData = \app\models\Pipe::find()->select(['id'])->where(['pipe_number' => $number])->active()->asArray()->one();
            $res = [];
            if(!empty($getPipeData)){
                $res['status'] = true;
            } else {
                $res['status'] = false;
            }

            echo json_encode($res);
            die;
        }
    }
}
