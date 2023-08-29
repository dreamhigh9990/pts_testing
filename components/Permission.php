<?php
namespace app\components;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
class Permission extends Component{

	public function checkAccess(){	
		$action 		= Yii::$app->controller->action->id;
		$controller 	= Yii::$app->controller->id;
		$module			= Yii::$app->controller->module->id; 	
		if($module	== "admin" || $action == "delete"){
			if (Yii::$app->user->identity->type != "Admin"){
				throw new \yii\web\NotFoundHttpException('You are not allowed to perform this action.');				
			}
		}else{
			switch (Yii::$app->controller->id){
				case 'project':
					if ($action == 'update' || $action == 'create'|| $action == 'index'||$action == 'view') {
						if (Yii::$app->user->identity->type != "Admin")
							throw new \yii\web\NotFoundHttpException('You are not allowed to perform this action.');
					}
					
					break;
				case 'pipe':
					if ($action == 'update' || $action == 'create'|| $action == 'index') {
						if (Yii::$app->user->identity->type != "Admin" && Yii::$app->user->identity->type != "Inspector")
							throw new \yii\web\NotFoundHttpException('You are not allowed to perform this action.');
					}
					break;
				case 'truck':
					if (Yii::$app->user->identity->type != "Admin")
							throw new \yii\web\NotFoundHttpException('You are not allowed to perform this action.');
					
					break;
				case 'location':
					if (Yii::$app->user->identity->type != "Admin")
							throw new \yii\web\NotFoundHttpException('You are not allowed to perform this action.');
					
					break;
				case 'defact':
					if (Yii::$app->user->identity->type != "Admin")
							throw new \yii\web\NotFoundHttpException('You are not allowed to perform this action.');
					
					break;
			}
		}
	}

}