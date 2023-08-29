<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;
use Yii;
/**
 * Default controller for the `admin` module
 */


 /* 
 {

    pipe: []
 }
 */
class AnomalyController extends Controller
{
  
    public function behaviors(){
       return [
           'access' => [
               'class' => \yii\filters\AccessControl::className(),
               'rules' => [                   
                   [
                       'allow' => true,
                       'actions' => ['index','active','delete-anomaly-record'],
                       'roles' => ['@'],
                   ],
               ],
           ],
       ];
    }
    public function actionActive($Id,$Model){       
        $ActiveModel = $Model::find()->where(['id'=>$Id])->one();
        if(!empty($ActiveModel)){

            $ActiveModel->is_active   = 1;
            $ActiveModel->is_anomally = 'No';
            $ActiveModel->save(false);

            if(!empty($ActiveModel->pipe_number)){
                $Models = $Model::find()->where(['pipe_number'=>$ActiveModel->pipe_number,'is_anomally'=>'Yes','project_id'=>Yii::$app->user->identity->project_id])->all();
            } else if (!empty($ActiveModel->weld_number)){
                $Models = $Model::find()->where(['weld_number'=>$ActiveModel->weld_number,'is_anomally'=>'Yes','project_id'=>Yii::$app->user->identity->project_id])->all();
            }
            
            if(!empty($Models)){
                foreach($Models as $item){
                    $DeletedItem                = new \app\models\DeletedItem;
                    $DeletedItem->table_name    = $Model::tableName();
                    $DeletedItem->table_id      = $item->id;
                    $DeletedItem->save(false);
                }
            }

            if(!empty($ActiveModel->pipe_number)){
                $Model::deleteAll(['pipe_number'=>$ActiveModel->pipe_number,'is_anomally'=>'Yes','project_id'=>Yii::$app->user->identity->project_id]);
            } else if (!empty($ActiveModel->weld_number)){
                $Model::deleteAll(['weld_number'=>$ActiveModel->weld_number,'is_anomally'=>'Yes','project_id'=>Yii::$app->user->identity->project_id]);
            }
           
            $res = ['status'=>true,'message'=>'Data has been updated'];   
        }else{
            $res = ['status'=>false,'message'=>'Invalid input data'];
        }
        echo json_encode($res);die;
    }

    public function actionDeleteAnomalyRecord($model){
        $postItems = Yii::$app->getRequest()->getBodyParams();
        
        if(!empty($postItems['deleteId'])){
            $allIds = $postItems['deleteId'];
            foreach($allIds as $id){
                $DeletedItem                = new \app\models\DeletedItem;
                $DeletedItem->table_name    = $model::tableName();
                $DeletedItem->table_id      = $id;
                $DeletedItem->save(false);
            }

            $model::deleteAll(['id' => $allIds,'is_anomally'=>'Yes','project_id'=>Yii::$app->user->identity->project_id]);
            
            $res = ['status'=>true, 'message'=>'Data has been deleted'];
        } else {
            $res = ['status'=>false, 'message'=>'Invalid input data'];
        }
        echo json_encode($res);die;
    }


    public function actionIndex($model="PipeSearch")
    {
      
        return $this->render('index',['model'=>$model]);
    }
}
