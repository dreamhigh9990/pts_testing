<?php

namespace app\modules\vehicle\controllers;

use Yii;
use app\models\VehicleSchedule;
use app\models\VehicleScheduleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ScheduleController implements the CRUD actions for VehicleSchedule model.
 */
class ScheduleController extends Controller
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
                        'actions' => ['index', 'create', 'view', 'copy', 'update', 'delete', 'unique-part-barcode', 'unique-vehicle-number', 'get-part'],
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
     * Lists all VehicleSchedule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VehicleScheduleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single VehicleSchedule model.
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
     * Creates a new VehicleSchedule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($EditId = 0)
    {
        $model = new VehicleSchedule();
        if(!empty($EditId)){
            $model = Yii::$app->general->getModelData('\app\models\VehicleSchedule', $EditId);
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']); 
            }
        }

        $model = Yii::$app->general->reportNo($model, 'VSC');
        if ($model->load(Yii::$app->request->post())) {            
            if(!empty($_POST['VehicleSchedule']['part_id'])){
                $partListArray = [];
                foreach($_POST['VehicleSchedule']['part_id'] as $key => $val){
                    $pArray = [
                        'part' => $val,
                        // 'barcode' => $_POST['VehicleSchedule']['barcode'][$key]
                    ];
                    array_push($partListArray, $pArray);
                }
                $model->part_list = json_encode($partListArray);
                if($model->save(false)){
                    echo json_encode(array('status' => true, 'modelData' => 'Your data has been saved.'));die;
                } else {
                    echo json_encode(array('status' => false, 'message' => $model->errors));die;
                }                
            } else {
                echo json_encode(array('status' => false, 'message' => 'Parts not select for this vehicle.'));die;
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Copy a VehicleSchedule model to create new.
     * @return mixed
     */
    public function actionCopy($CopyId)
    {
        if(!empty($CopyId)){
            $model = Yii::$app->general->getModelData('\app\models\VehicleSchedule', $CopyId);
            if(isset($model['status']) && $model['status'] == false){
                return $this->redirect(['create']); 
            }
        } else {
            return $this->redirect(['create']);
        }
        
        return $this->render('create', [
            'model' => $model,
            'clone' => true
        ]);
    }

    /**
     * Updates an existing VehicleSchedule model.
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
     * Deletes an existing VehicleSchedule model.
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
     * Finds the VehicleSchedule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VehicleSchedule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VehicleSchedule::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /********** check unique barcode for vehicle part ************/
    public function actionUniquePartBarcode(){
        if(!empty($_POST['code'])){
            $barcode = $_POST['code'];
            $part = $_POST['part'];
            $getPartList = \app\models\VehicleSchedule::find()->active()->asArray()->all();
            if(!empty($getPartList)){
                foreach($getPartList as $list){
                    $jsnDecode = json_decode($list['part_list'], true);
                    if(!empty($jsnDecode)){
                        foreach($jsnDecode as $key => $val){
                            if($part != $key['part'] && $barcode === $val['barcode']){
                                $res['status'] = false;
                                $res['message'] = 'Barcode has been already registered for another part.';
                                echo json_encode($res);
                                die;
                                break;
                            } else {
                                $res['status'] = true;
                            }
                        }
                    }
                }
            } else {
                $res['status'] = true;
            }
        } else {
            $res['status'] = false;
            $res['message'] = 'Barcode not found.';
        }

        echo json_encode($res);
        die;
    }

    /********** check unique vehicle number ************/
    public function actionUniqueVehicleNumber(){
        if(!empty($_POST['number'])){
            $number = $_POST['number'];
            $vehicle = $_POST['vehicle'];
            $getList = \app\models\VehicleSchedule::find()->where(['AND',['=','vehicle_number', $number],['!=', 'id', $vehicle]])->active()->asArray()->all();
            if(!empty($getList)){
                $res['status'] = false;
                $res['message'] = 'Vehicle number has been already registered.';
            } else {
                $res['status'] = true;
            }
        } else {
            $res['status'] = false;
            $res['message'] = 'Vehicle number is missing.';
        }

        echo json_encode($res);
        die;
    }

    /***************** get clone part ******************/
    public function actionGetPart(){
        $getPartDropList = Yii::$app->general->TaxonomyDrop(30, true);
        $html = '<div class="clearfix part-container">
            <div class="col-md-9 v-container">
                <div class="form-group field-vehicleschedule-part_id">
                    <div class="col-md-12 col-sm-12 clearfix p-0">
                        <select id="vehicleschedule-part_id" class="form-control vehicle-part" name="VehicleSchedule[part_id][]">';
                            if(!empty($getPartDropList)){
                                $html .= '<option value="0">Please Select</option>';
                                foreach($getPartDropList as $key => $val){
                                    $html .= '<option value="'.$key.'">'.$val.'</option>';
                                }
                            }
                    $html .= '</select>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-sm btn-raised btn-outline-danger pull-right btn-remove-part-schedule"><i class="fa fa-trash-o"></i></button>
            </div>
        </div>';
        $res['status'] = true;
        $res['html'] = $html;

        echo json_encode($res);
        die;
    }
}
