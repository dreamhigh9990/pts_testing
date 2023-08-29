<?php

namespace app\modules\api\controllers;

use Yii;

use yii\rest\ActiveController;

use app\models\Employee;

use app\models\LoginForm; 
use app\models\ResetPasswordForm; 
use app\models\PasswordResetRequestForm; 
use app\models\User; 
use app\models\UserToken;
class SiteController extends ActiveController
{

    public $modelClass = '';
    
    public function actionLogin()
    {
        $model = new \app\models\LoginForm();
        if ($model->load(Yii::$app->getRequest()->getBodyParams(),'') && $model->validate()) {
			 $POSTDATA = Yii::$app->getRequest()->getBodyParams();
			 
			 if($model->login()){				
				  $user = \app\models\User::findOne([
            		'status' => 10,
            		'id' => Yii::$app->user->identity->id,
				  ]);
				  

				  if($POSTDATA['platform'] == "ios" && Yii::$app->user->identity['type']!="Safety"){
						return ['status'=>false,'message'=>'Only Safety users can access this app.'];
				  }

				  $UserToken 			        =   new \app\models\UserToken;
				  $UserToken->user_id           =   Yii::$app->user->identity->id;
				  $UserToken->access_token      =   Yii::$app->security->generateRandomString() . '_' . time();
                  $UserToken->created_at        =   time();
                  $UserToken->platform          =   $POSTDATA['platform']; 
				  $UserToken->device_id         =   $POSTDATA['device_id']; 
				  
				  if (!$UserToken->save(false)) {
						 throw new \yii\web\NotFoundHttpException(json_encode($UserToken->errors));
				  }else{					 
				    return array(
							'username'=>$user->username,
			 				'fullname'=>$user->fullname,
							'access_token'=>$UserToken->access_token,
							'email'=>$user->email,
							'project_id'=>$user->project_id,
							'id'=>$user->id,
							'type'=>$user->type,
							'phone'=>$user->phone,
							'lang' => $user->lang
					);
                  }				 
			 }else{
		  		 return ['status'=>false,'message'=>'Invalid username or password.'];
			 }
        }else{
			return ['status'=>false,'message'=>$model->errors];
				
		}
		return $model;
    }
	public function actionForgetPassword()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->getRequest()->getBodyParams(),'')) {
            if ($model->sendEmail()) {
				return array('status'=>true,'message'=>'Check your email for further instructions.');
            } else {
                 throw new \yii\web\NotFoundHttpException('Sorry, we are unable to reset password for the provided email address.');
            }
        }else{
			throw new \yii\web\NotFoundHttpException('Please enter your email');
		}
    }

}

