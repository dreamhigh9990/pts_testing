<?php
namespace app\modules\catalogue\controllers;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;

use app\modules\catalogue\models\Taxonomy;
use app\modules\catalogue\models\TaxonomyValue;
use app\modules\catalogue\models\TaxonomyBinding;

class CatalogueController extends ActiveController
{
	
	public $modelClass = 'app\modules\catalogue\models\TaxonomyValue';
	public function actions(){
		$actions = parent::actions();
		$actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
		return $actions;
	}
	public function prepareDataProvider() {
		$searchModel = new CatalogueTruckSeacrh();    
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
	public function actionTaxanomyList(){
		Yii::$app->permission->checkAccess();		
		$TaxonomyList =Taxonomy::find()->asArray()->all();
		$Taxarray = [];
		if(!empty($TaxonomyList)){
			foreach($TaxonomyList as $ele){
				array_push($Taxarray,array('id'=>$ele['id'],'name'=>$ele['name'],'child_value'=>$ele['child_value']));
			}
		}else{
			throw new \yii\web\NotFoundHttpException('There are no taxonomy exist');
		}
		return $Taxarray;
	}
	public function actionForChildList($child_taxonomy_id){
		$TaxonomyValueList = TaxonomyValue::find()->where(['taxonomy_id'=>$child_taxonomy_id])->active()->asArray()->all();
		$Taxarray = [];
		if(!empty($TaxonomyValueList)){
			foreach($TaxonomyValueList as $ele){
				array_push($Taxarray,array('id'=>$ele['id'],'value'=>htmlentities($ele['value'])));
			}
		}
		return $Taxarray;
	}
	private function valusOfTaxonomy($taxonomy_id){
		$TaxonomyValueList = TaxonomyValue::find()->where(['taxonomy_id'=>$taxonomy_id])->active()->asArray()->all();
		$Taxarray = [];
		if(!empty($TaxonomyValueList)){
			foreach($TaxonomyValueList as $ele){
				array_push($Taxarray,array('id'=>$ele['id'],'name'=>htmlentities($ele['value'])));
			}
		}
		return $Taxarray;
	}
	public function actionTaxanomyValues($TaxonomyName){		
		$Taxonomy = Taxonomy::find()->where(['name'=>$TaxonomyName])->asArray()->one();
		$Taxarray =array();
		if(!empty($Taxonomy)){
			return $this->valusOfTaxonomy($Taxonomy['id']);
		}
		return $Taxarray;
	}	
	public function actionTaxanomyValueList($taxonomy_id){
		return $this->valusOfTaxonomy($taxonomy_id);
	}
	public function actionTaxanomyValueOfValueList($taxonomy_id){
		$TaxonomyBinding = TaxonomyBinding::find()->select(['taxonomy_value.value','taxonomy_value.id'])->where(['taxonomy_value_value.parent_id'=>$taxonomy_id])
			->rightJoin('taxonomy_value','taxonomy_value.id = taxonomy_value_value.child_id')
			->asArray()->all();
		$Taxarray = [];
		if(!empty($TaxonomyBinding)){
			foreach($TaxonomyBinding as $ele){
				array_push($Taxarray,array('id'=>$ele['id'],'name'=>htmlentities($ele['value'])));
			}
		}
		return $Taxarray;
	}
}
