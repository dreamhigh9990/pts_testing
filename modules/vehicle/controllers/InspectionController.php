<?php

namespace app\modules\vehicle\controllers;

use Yii;
use app\models\VehicleInspection;
use app\models\VehicleInspectionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InspectionController implements the CRUD actions for VehicleInspection model.
 */
class InspectionController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [ 
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'view', 'update', 'delete', 'unique-part-barcode', 'unique-vehicle-number'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all VehicleInspection models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VehicleInspectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single VehicleInspection model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new VehicleInspection model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($EditId = 0)
    {
        $model = new VehicleInspection();
        if(!empty($EditId)){
            $model = Yii::$app->general->getModelData('\app\models\VehicleInspection', $EditId);            
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']); 
            }
            $getVehicleNumber = \app\models\VehicleSchedule::find()->select('vehicle_number')->where(['id' => $model->vehicle_id])->active()->asArray()->one();
            if(!empty($getVehicleNumber)){
                $model->vehicle_id = $getVehicleNumber['vehicle_number'];
            }
        }

        $model = Yii::$app->general->reportNo($model, 'VIR');
        if ($model->load(Yii::$app->request->post())) {
            $getVehicleId = \app\models\VehicleSchedule::find()->select('id')->where(['vehicle_number' => $model->vehicle_id])->active()->asArray()->one();
            if(!empty($getVehicleId)){
                $model->vehicle_id = $getVehicleId['id'];
                $model->save(false);

                // save part inspection details
                if(!empty($_POST['MapPartVehicleInspection'])){
                    $list = $_POST['MapPartVehicleInspection'];
                    if(!empty($EditId)){
                        foreach($list as $key => $val){
                            $getMapDetails = \app\models\MapPartVehicleInspection::find()->where(['id' => $val['map_id']])->one();
                            if(!empty($getMapDetails)){
                                $getMapDetails->status = $val['status'];
                                $getMapDetails->defect_comments = $val['defect_comment'];
                                $getMapDetails->save(false);
                            }
                        }
                    } else {
                        foreach($list as $key => $val){
                            if(!empty($val)){
                                foreach($val as $k => $v){
                                    $newInspectionMap = new \app\models\MapPartVehicleInspection();
                                    $newInspectionMap->date = $model->date;
                                    $newInspectionMap->inspection_id = $model->id;
                                    $newInspectionMap->part_id = $key;
                                    $newInspectionMap->que_id = $k;
                                    $newInspectionMap->status = $v['status'];
                                    $newInspectionMap->defect_comments = $v['defect_comment'];
                                    $newInspectionMap->save(false);
                                }
                            }
                        }
                    }
                    echo json_encode(array('status' => true, 'modelData' => 'Your data has been saved.'));die;
                } else {
                    echo json_encode(array('status' => false, 'message' => 'Something went wrong. Vehicle part list is missing.'));die;
                }
            } else {
                echo json_encode(array('status' => false, 'message' => 'Vehicle not found.'));die;
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing VehicleInspection model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing VehicleInspection model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the VehicleInspection model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VehicleInspection the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VehicleInspection::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
