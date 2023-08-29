<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Employee;
use app\models\EmployeeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * EmployeeController implements the CRUD actions for Employee model.
 */
class EmployeeController extends Controller
{
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [                    
                    // allow authenticated users
                    [
                        'allow' => true,
                        'actions' => ['create','changepassword','kp-range','view','delete'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate($EditId="")
    {
        $model = new Employee();
        if(!empty($EditId)){
            $model 			= \app\models\Employee::find()->where(['id'=>$EditId])->one();
            if(empty($model)){
		    	return $this->redirect(['create']); 
            }
        }
		if ($model->load(Yii::$app->request->post())) {
			if($model->validate() && $model->save()){
				return json_encode(array('status'=>true,'message'=>'User data has been save successfully'));die;
			}else{
             	echo json_encode(array('status'=>false,'message'=>$model->errors));die;
        	}
		}
        return $this->render('create', [
            'model' => $model,
        ]);
    }
	public function actionChangepassword($EditId){
        
		$model = new Employee;
		if($model->load(Yii::$app->request->post())) {                  
			$password_hash  =  Yii::$app->security->generatePasswordHash($model->password_hash);
            $model 			=  Employee::findOne($EditId);
			if(!empty($model)){
				$model->password_hash = $password_hash ;
				if($model->validate() && $model->save(false)){
					echo json_encode(array('status'=>true,'message'=>'Password changed successfully'));die;
				}else{
					echo json_encode(array('status'=>false,'message'=>$model->errors));die;
				}
			}else{
				echo json_encode(array('status'=>false,'message'=>'User Not Found'));die;
			}
		}else{
            echo json_encode(array('status'=>false,'message'=>'Please enter new password'));die;
        }
	}
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['create']);
    }
    protected function findModel($id)
    {
        if (($model = Employee::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
