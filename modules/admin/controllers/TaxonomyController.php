<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\TaxonomyValue;
use app\models\TaxonomyValueValue;
use app\models\TaxonomyValueSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TaxonomyController implements the CRUD actions for TaxonomyValue model.
 */
class TaxonomyController extends Controller
{
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [                    
                    // allow authenticated users
                    [
                        'allow' => true,
                        'actions' => ['create','delete','taxomonychild','projects','copy-all','unique-part-name'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }
    public function actionCopyAll(){
        if(isset($_POST['TaxonomyValue']['taxonomy_id']) && isset($_POST['TaxonomyValue']['project_id'])){
                if($_POST['TaxonomyValue']['taxonomy_id']==0){
                    $Data = \app\models\TaxonomyValue::find()->where(['project_id'=>$_POST['TaxonomyValue']['project_id'],'is_deleted'=>0])->asArray()->all();
                }else{
                    $Data = \app\models\TaxonomyValue::find()->where([
                        'project_id'=>$_POST['TaxonomyValue']['project_id'],
                        'taxonomy_id'=>$_POST['TaxonomyValue']['taxonomy_id'],
                        'is_deleted'=>0
                    ])->asArray()->all();
                }
                if(!empty( $Data )){
                    foreach($Data as $ele){
                        $model                = new \app\models\TaxonomyValue;
                        $model->taxonomy_id   = $ele['taxonomy_id'];
                        $model->value         = $ele['value'];
                        $model->location_lat  = $ele['location_lat'];
                        $model->location_long = $ele['location_long'];
                        $model->project_id    = Yii::$app->user->identity->project_id;
                        $model->save(false);
                        $Old[$ele['id']]  = $model->id;  
                        $allchild = TaxonomyValueValue::find()->where(['parent_id'=>$ele['id']])->all();
                        if(!empty( $allchild)){
                            foreach( $allchild as $child){
                                $childData            = new \app\models\TaxonomyValueValue;
                                if(isset($Old[$child['parent_id']]) && isset($Old[$child['child_id']])){

                                    $childData->parent_id = $Old[$child['parent_id']];
                                    $childData->child_id  = $Old[$child['child_id']];
                                    $childData->save(false);
                                }
                            }
                        }
                        
                        //for only part questions
                        if($ele['taxonomy_id'] == 30){
                            $getQue = \app\models\MapPartQuestion::find()->where(['part_id' => $ele['id']])->asArray()->all();
                            if(!empty($getQue)){
                                foreach($getQue as $que){
                                    $newQue = new \app\models\MapPartQuestion();
                                    $newQue->part_id = $model->id;
                                    $newQue->question = $que['question'];
                                    $newQue->save(false);
                                }
                            }
                        }
                    }
                }
                echo json_encode(array('status' => true,'message' => 'Data has been copied.'));die;            
        }else{
            echo json_encode(array('status' => false,'message' => 'Project or Catalogue is missing'));die;
        }
    }
    public function actionCreate($EditId = "") {        
        $model = new TaxonomyValue();
        if(!empty($EditId)){
            $model = \app\models\TaxonomyValue::find()->where(['id'=>$EditId])->active()->one();
            if(empty($model)) return $this->redirect(['create']);
            $queList = \app\models\MapPartQuestion::find()->select('id')->where(['part_id' => $EditId])->asArray()->all();
            $alreadyListQue = [];
            foreach($queList as $q){
                $alreadyListQue[] = $q['id'];
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            if($model->taxonomy_id == 30 && empty($_POST['MapPartQuestion']['question'])){
                echo json_encode(array('status' => false,'message' =>'Please ensure all questions have been filled'));
                die;
            }
            if($model->taxonomy_id==12){
                $TaxonomyValue          = \app\models\TaxonomyValue::find()->where(['taxonomy_id'=>12])->one();
                if(!empty( $TaxonomyValue ) && empty($EditId)){
                    echo json_encode(array('status' => false,'message' =>'This Yield Strength Threshold is already exist.'));die;
                }               
            }
            if($model->validate() && $model->save(false)){
                if(!empty($EditId)){
                    $this->deleteChild($model);
                }
                $postVal = Yii::$app->request->post('TaxonomyValue');
                if(!empty($postVal['taxonomyChildId'])){
                    foreach($postVal['taxonomyChildId'] as $childId){
                        $newTax = new TaxonomyValueValue();
                        $newTax->parent_id = $model->id;
                        $newTax->child_id = $childId;
                        $newTax->save(false);
                    }
                }

                //for part list questions
                if($model->taxonomy_id == 30){
                    $partQuestions = $_POST['MapPartQuestion']['question'];
                    if(!empty($EditId)){
                        $partQuestionsIds = $_POST['MapPartQuestion']['questionId'];
                        $diffArray = array_diff($alreadyListQue, $partQuestionsIds);
                        $diffArray = array_values($diffArray);
                        // delete que from db which is removed
                        if(!empty($diffArray)){
                            foreach($diffArray as $k => $v){
                                \app\models\MapPartQuestion::find()->where(['id' => $v])->one()->delete();
                            }
                        }
                        if(!empty($partQuestions)){                            
                            foreach($partQuestions as $key => $que){
                                if(!empty($partQuestionsIds[$key])){
                                    $getDetails = \app\models\MapPartQuestion::find()->where(['id' => $partQuestionsIds[$key]])->one();
                                    if(!empty($getDetails)){
                                        $getDetails->question = $que;
                                        $getDetails->save(false);
                                    }
                                } else {
                                    $newPartQue = new \app\models\MapPartQuestion();
                                    $newPartQue->part_id = $EditId;
                                    $newPartQue->question = $que;
                                    $newPartQue->save(false);
                                }
                            }
                        }
                    } else {                        
                        if(!empty($partQuestions)){
                            foreach($partQuestions as $que){
                                $newPartQue = new \app\models\MapPartQuestion();
                                $newPartQue->part_id = $model->id;
                                $newPartQue->question = $que;
                                $newPartQue->save(false);
                            }
                        }
                    }
                }
				echo json_encode(array('status' => true,'message' => 'Your data has been saved.'));die;
            }else{
                //Yii::$app->session->setFlash('taxonomy_error',json_encode($model->errors));
				echo json_encode(array('status' => false,'message' => $model->errors));
				die;
            }			
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function deleteChild($id){
        $allchild = TaxonomyValueValue::find()->where(['parent_id'=>$id])->all();
        if(!empty($allchild)){
            TaxonomyValueValue::deleteAll(['parent_id' => $id]);
        }
    }
    protected function findModel($id) {
        if (($model = TaxonomyValue::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionTaxomonychild(){
        $result = array('status'=>false,'data'=>array());
        if(!empty($_POST['id'])){
            $data = Yii::$app->general->TaxonomyChild($_POST['id']);
            if(!empty($data)){
                $result = array('status' => true, 'data' => $data);
            }
        }
        echo json_encode($result);
        die;
    }

    public function actionProjects(){
        $result = array('status'=>false,'data'=>'');
        if(!empty($_POST['id'])){
            $data = Yii::$app->general->TaxonomyDrop($_POST['id'],true);
            if(!empty($data)){
                $htmlList = '';
                foreach ($data as $key => $value) {
                    $htmlList .= '<option value="'.$key.'">'.$value.'</option>';
                }
                $result = array('status' => true, 'data' => $htmlList);
            }
        }
        echo json_encode($result);
        die;
    }

    public function actionUniquePartName(){
        if(!empty($_POST['name']) && !empty($_POST['taxo'])){
            $name = $_POST['name'];
            $taxonomy = $_POST['taxo'];
            $editId = !empty($_POST['edit']) ? $_POST['edit'] : 0;
            $getDetails = \app\models\TaxonomyValue::find()->where([
                'AND',
                [
                    '!=',
                    'id',
                    $editId
                ],
                [
                    '=',
                    'value',
                    $name
                ],
                [
                    '=',
                    'taxonomy_id',
                    $taxonomy
                ],
                [
                    '=',
                    'project_id',
                    Yii::$app->user->identity->project_id
                ]
            ])->asArray()->active()->one();

            if(!empty($getDetails)){
                $res['status'] = false;
                $res['message'] = $name.' has been already registered.';
            } else {
                $res['status'] = true;
            }
        } else {
            $res['status'] = true;
        }

        echo json_encode($res);
        die;
    }
}
