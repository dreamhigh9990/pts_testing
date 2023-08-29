<?php
namespace app\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;

use app\models\Pipe;
use app\models\PipeSearch;
use app\models\CsvImport;
use yii\web\UploadedFile;
use app\models\Taxonomy;
use app\models\TaxonomyValue;
class PipeController extends ActiveController
{

	public $modelClass = 'app\models\Pipe';
	public function actions(){
		$actions = parent::actions();
		 unset($actions['delete'],$actions['view']);
		 $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
		return $actions;
	}
	public function prepareDataProvider() {
		$searchModel = new PipeSearch();    
		return $searchModel->search(\Yii::$app->request->queryParams);
	}	
	public function behaviors(){
	    $behaviors = parent::behaviors();
		$behaviors['authenticator'] = [
			'class' => CompositeAuth::className(),
			'authMethods' => [				
				QueryParamAuth::className(),
			],
		];
		return $behaviors;
	}
	public function checkAccess($action, $model = null, $params = []){
		Yii::$app->permission->checkAccess();
	}
	public function actionSimilar($pipe_number,$project_id){
			return Pipe::find()->where(['AND',['LIKE','pipe_number',$pipe_number],['project_id'=>$project_id]])->asArray()->all();			
	}
	public function actionView($pipe_number){
		 Yii::$app->permission->checkAccess();
		 $model = Pipe::find()->with(['bendings','cutings','stringings','receptions','transfers'])->where(['pipe_number'=>$pipe_number])->asArray()->one();
		 if(!empty($model)){
			 return $model;
		 }else{
		 	throw new \yii\web\NotFoundHttpException('Invalid pipe number.');
		 }
	}	
	public function actionDeleteAll(){		
		Yii::$app->general->delete('app\modules\pipe\models\Pipe');		
		return array('message'=>'Selected items has been deleted.');
	}
	public function actionCsvImport(){	  
        $Data = Yii::$app->general->csvImport();
        if($Data['status']==false){
            throw new \yii\web\NotFoundHttpException(!empty($Data['message'])?$Data['message']:"Something wents wrong.");
        }else{
            return $Data['data'];
        }
    }
}

