<?php
namespace app\modules\pipe\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;
use app\modules\pipe\models\Bending;
use app\modules\pipe\models\BendingSearch;
use app\models\Picture;
class ImageController extends ActiveController
{

	public $modelClass = '';
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
	public function actionDeleteImage($id,$section_id,$section_type){
		Yii::$app->permission->checkAccess();
		$Picture = Picture::find()->where(['id'=>$id,'section_id'=>$section_id,'section_type'=>$section_type])->one();
		if(!empty($Picture)){
				!unlink(Yii::$app->basePath.'/web/images/'.$Picture->image);
				$Picture->delete();
				return array('message'=>'Image has been deleted.');
			
		}else{
			throw new \yii\web\NotFoundHttpException('Image is not exist');
		}
	}
	public function actionUpload(){		
		Yii::$app->permission->checkAccess();
		$Picture = new Picture();
	    $Images  = UploadedFile::getInstances($Picture, 'image');		
		if(!empty($Images)){
			 foreach ($Images as $file) {
				    $filename = time().'-'.$file->name;
					$model = new Picture();
					$model->mime_type = mime_content_type($file->tempName);
				
					$model->load(Yii::$app->getRequest()->getBodyParams(), '');
					$model->image = 	$filename;	
					if ($model->save() === false && !$model->hasErrors()) {
							throw new \yii\web\NotFoundHttpException('Failed to update the object for unknown reason.');
					}else{
						if(!move_uploaded_file($file->tempName, \Yii::$app->basePath.'/web/images/'.$filename)){
							throw new \yii\web\NotFoundHttpException('Failed to upload image.');
						}
					}
            }
			return array('message'=>'image uploaded');
			
		}else{
				throw new \yii\web\NotFoundHttpException('Please select atleast one image.');
		}		
	}
	
}

