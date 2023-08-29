<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sync_failed".
 *
 * @property int $id
 * @property int $user_id
 * @property int $project_id
 * @property string $request
 * @property string $response
 * @property string $error
 * @property string $date
 */
class SyncFailed extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sync_failed';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'project_id'], 'required'],
            [['user_id', 'project_id'], 'integer'],
            [['request', 'response', 'error'], 'string'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'project_id' => 'Project ID',
            'request' => 'Request',
            'response' => 'Response',
            'error' => 'Error',
            'date' => 'Date',
        ];
    }
}
