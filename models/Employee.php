<?php



namespace app\models;



use Yii;



/**

 * This is the model class for table "user".

 *

 * @property int $id

 * @property string $username
  * @property string $fullname

 * @property string $auth_key

 * @property string $password_hash

 * @property string $password_reset_token

 * @property string $type

 * @property string $email

 * @property int $status

 * @property int $created_at

 * @property int $updated_at

 */

class Employee extends \yii\db\ActiveRecord

{
    /**

     * @inheritdoc

     */

    public static function tableName()

    {

        return 'user';

    }

	public function fields()
	{
		$fields = parent::fields();
	
		// remove fields that contain sensitive information
		unset($fields['password_hash'], $fields['password_hash'], $fields['password_reset_token']);
	
		return $fields;
	}

    /**

     * @inheritdoc

     */

    public function rules()

    {

        return [
            [['username', 'fullname', 'password_hash','phone', 'type', 'email'], 'required'],
			[['email'], 'email'],
            [['type'], 'string'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash'], 'string', 'min' => 6],
            [['username'], 'unique', 'filter' => ['<>', 'is_deleted', 1]],
            [['email'], 'unique', 'filter' => ['<>', 'is_deleted', 1]],
            [['password_reset_token'], 'unique'],
            ['project_id', 'required', 'when' => function ($model) {
                if(!empty(Yii::$app->user->identity->type) && Yii::$app->user->identity->type == 'QA Manager'){
                    return false;
                } else {
                    return true;
                }
            }],
        ];

    }



    /**

     * @inheritdoc

     */

    public function attributeLabels()

    {

        return [
            'id' => 'ID',
            'username' => Yii::$app->trans->getTrans('Username'),
            'fullname' => Yii::$app->trans->getTrans('Fullname'),
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password',
            'password_reset_token' => 'Password Reset Token',
            'type' => Yii::$app->trans->getTrans('Type'),
            'email' => Yii::$app->trans->getTrans('Email'),
            'phone' => Yii::$app->trans->getTrans('Phone'),
            'status' => Yii::$app->trans->getTrans('Status'),
            'project_id' => Yii::$app->trans->getTrans('Project ID'),
            'created_at' => Yii::$app->trans->getTrans('Created at'),
            'updated_at' => Yii::$app->trans->getTrans('Updated At'),
        ];

    }
	public function beforeSave($insert){
		if (parent::beforeSave($insert)) {
			if(!empty(Yii::$app->user->identity->auth_key)){
				if ($this->isNewRecord){
					$this->password_hash =  Yii::$app->security->generatePasswordHash($this->password_hash);
					$this->created_at = time();
					$this->updated_at = time();
                    $this->auth_key = Yii::$app->security->generateRandomString();
                    
                    if(!empty(Yii::$app->user->identity->type) && Yii::$app->user->identity->type == 'QA Manager'){
                        $projectId = !empty(Yii::$app->user->identity->project_id) ? Yii::$app->user->identity->project_id : 0;
                        $this->project_id = $projectId;
                    }
				}else{
					$this->updated_at = time();
				}
				return true;
			}else{				
				throw new \yii\web\NotFoundHttpException('Invalid or missing access token');
			}
		}else{
			return false;
		}
    }
    
    public static function find()
    {
        return new EmployeeQuery(get_called_class());
    }

}

