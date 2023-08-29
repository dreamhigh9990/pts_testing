<?php

namespace app\models;

use yii\db\ActiveRecord;

use yii\web\IdentityInterface;

use Yii;
use app\models\UserToken;
class User extends ActiveRecord implements IdentityInterface
{
	
    public static function tableName()
    {
        return 'user';
    }
    public static function findIdentity($id)
    {
        return static::findOne(['id'=>$id,'is_deleted'=>0]);
    }
    public static function findByUsername($username)
    {

         return static::findOne(['username' => $username,'is_deleted'=>0]);       

    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
		$UserToken	 = 	UserToken::find()->where(['access_token'=>$token])->one();
		if(!empty($UserToken)){
        	return static::findOne(['id' => $UserToken->user_id,'is_deleted'=>0]);
		}else{
			throw new \yii\web\NotFoundHttpException('Invalid access token or User has been deleted.');
		}
    }
    public function getId()
    {
        return $this->id;
    }
    public function getAuthKey()
    {
        return $this->auth_key;
    }
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
	public function validatePassword($password)
    {

       // return $this->password_hash === $password;
		return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
	public static function findByPasswordResetToken($token)
    {
		
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }
	public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }
	public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
}