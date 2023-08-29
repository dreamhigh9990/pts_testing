<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Project;
use app\models\ProjectSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * ProjectController implements the CRUD actions for ProjectValue model.
 */
class ProjectController extends Controller
{
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [                    
                    // allow authenticated users
                    [
                        'allow' => true,
                        'actions' => ['create','delete-multiple'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }

    public function actionCreate($EditId = "") {
        $model = new Project();
        if(!empty($EditId)){
            $model = $this->findModel($EditId);            
        }
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate() && $model->save(false)){  
                $User = \app\models\User::find()->where(['id'=>Yii::$app->user->identity->id])->one();  
                if(!empty($User)){
                    $User->project_id = $model->id;
                    $User->save();
                }           
				echo json_encode(array('status' => true,'message' => 'Your data has been saved.'));	die;
            }else{
				echo json_encode(array('status' => false,'message' => $model->errors));die;
            }	
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    protected function findModel($id) {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionDeleteMultiple(){
        $POST = Yii::$app->getRequest()->getBodyParams();        
        if(!empty($POST['deleteId'])){
            $tableArray =  [
                'admin_line_list' => '\app\models\Line',
                'admin_landowner' => '\app\models\Landowner',
                'pipe' => '\app\models\Pipe',
                'pipe_reception' => '\app\models\Reception',
                'pipe_cleargrade' => '\app\models\Cleargrade',
                'pipe_stringing' => '\app\models\Stringing',
                'pipe_transfer' => '\app\models\PipeTransfer',
                'pipe_bending' => '\app\models\Bending',
                'pipe_cuting' => '\app\models\Cutting',
                'welding' => '\app\models\Welding',
                'welding_parameter_check' => '\app\models\Parameter',
                'welding_ndt' => '\app\models\Ndt',
                'welding_repair' => '\app\models\Weldingrepair',
                'welding_coating_production' => '\app\models\Production',
                'welding_coating_repair' => '\app\models\Coatingrepair',
                'civil_backfilling' => '\app\models\Backfilling',
                'civil_lowering' => '\app\models\Lowering',
                'civil_reinstatement' => '\app\models\Reinstatement',
                'civil_trenching' => '\app\models\Trenching',
                'com_cathodic_protection' => '\app\models\Cathodicprotection',
                'com_clean_gauge' => '\app\models\Cleangauge',
                'com_hydrotesting' => '\app\models\Hydrotesting',
                'com_surveying' => '\app\models\Surveying',
                'cabling_drum' => '\app\models\Cable',
                'cabling_splicing' => '\app\models\CabSplicing',
                'cabling_stringing' => '\app\models\CabStringing',
            ];

            foreach ($POST['deleteId'] as $key => $value) {
                $projectId = $value;
                $UserList = \app\models\User::find()->where(['project_id' => $projectId])->asArray()->all();
                if(!empty($UserList)){
                    \app\models\User::updateAll(['project_id' => 0],['IN','id',ArrayHelper::map($UserList,'id','id')]);
                }
                $projectData = \app\models\TaxonomyValue::find()->where(['id' => $projectId])->one();
                if(!empty($projectData)){

                    $deleteItem             = new \app\models\DeletedItem();
                    $deleteItem->table_name = 'taxonomy_value';
                    $deleteItem->table_id   = $projectData->id;
                    $deleteItem->save(false);

                    $projectData->delete();

                    foreach ($tableArray as $tblKey => $tblValue) {
                        $modelList = $tblValue::find()->select('id')->where(['project_id' => $projectId])->asArray()->all();
                        if(!empty($modelList)){                         
                            foreach($modelList as $itmKey => $itmValue){
                                $deleteItem = new \app\models\DeletedItem();
                                $deleteItem->table_name = $tblKey;
                                $deleteItem->table_id = $itmValue['id'];
                                $deleteItem->save();
                            }
                            $tblValue::deleteAll('project_id = :project_id', [':project_id' => $projectId]);
                        }
                    }
                }
            }
            echo json_encode(array('status'=>true,'message'=>'Selected items has been deleted.'));
		}else{
            echo json_encode(array('status'=>false,'message'=>'Please select item to delete.'));
		}
    }
}
