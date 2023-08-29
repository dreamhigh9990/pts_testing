<?php
namespace app\modules\pipe\controllers;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
class WarningController extends Controller
{
	public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [                    
                    // allow authenticated users
                    [
                        'allow' => true,
                        'actions' => [
                            'pipe-warning',
                            'pipe-reception-warning',
                            'pipe-transfer-warning',
                            'pipe-stringing-warning',
                            'pipe-cleargrade-warning',
                            'pipe-bending-warning',
                            'pipe-cutting-warning',
                            'welding-warning',
                            'welding-parameter-warning',
                            'welding-ndt-warning',
                            'welding-weldrepair-warning',
                            'welding-production-warning',
                            'welding-coatingrepair-warning',
                            'civil-trenching-warning',
                            'civil-lowering-warning',
                            'civil-backfilling-warning',
                            'civil-reinstatement-warning',
                            'precom-cleanguage-warning',
                            'precom-hydrotesting-warning',
                            'cab-stringing-warning',
                            'cab-splicing-warning',
                            'cable-warning'
                        ],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }
    public function actionPipeWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Pipe',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model     = new \app\models\Pipe(); 
        }
        $res       = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->pipe_anomaly($model,true);
        }
        echo json_encode($res);die;
    }
    public function actionPipeReceptionWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Reception',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model     = new \app\models\Reception(); 
        }
        $res       = array('status'=>false);

        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->pipe_reception_anomaly($model,true);
        }
        echo json_encode($res);die;
    }
    public function actionPipeTransferWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\PipeTransfer',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model     = new \app\models\PipeTransfer(); 
        }
        $res       = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->pipe_transfer_anomaly($model,true);
        }
        echo json_encode($res);die;
    }
    public function actionPipeStringingWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Stringing',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model     = new \app\models\Stringing(); 
        }
        $res       = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->pipe_stringing_anomaly($model,true); //as per client says anomaly section has been turn off
        }
        echo json_encode($res);die;
    }

    public function actionPipeCleargradeWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Cleargrade',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model     = new \app\models\Cleargrade(); 
        }
        $res       = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->pipe_cleargrade_anomaly($model,true);
        }
        echo json_encode($res);die;
    }

    public function actionPipeBendingWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Bending',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model     = new \app\models\Bending(); 
        }
        $res       = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->pipe_bending_anomaly($model,true);
        }
        echo json_encode($res);die;
    }
    public function actionPipeCuttingWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Cutting',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model     = new \app\models\Cutting(); 
        }
        $res       = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->pipe_cutting_anomaly($model,true);
        }
        echo json_encode($res);die;
    }
    public function actionWeldingWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Welding',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model     = new \app\models\Welding(); 
        }
        $res       = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->welding_anomaly($model,true);
        }
        echo json_encode($res);die;
    }
    public function actionWeldingParameterWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Parameter',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model     = new \app\models\Parameter(); 
        }
        $res       = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            // $res = Yii::$app->anomaly->welding_param_anomaly($model,'\app\models\Parameter()',true); //as per client says anomaly section has been turn off
            $res = array('status'=>false);
        }
        echo json_encode($res);die;
    }
    public function actionWeldingNdtWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Ndt',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model     = new \app\models\Ndt(); 
        }
        $res       = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->welding_ndt_anomaly($model,'\app\models\Ndt()',true);
        }
        echo json_encode($res);die;
    }
    public function actionWeldingWeldrepairWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Weldingrepair',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model     = new \app\models\Weldingrepair(); 
        }
        $res       = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->welding_repair_anomaly($model,'\app\models\Weldingrepair()',true);
        }
        echo json_encode($res);die;
    }
    public function actionWeldingProductionWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Production',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model     = new \app\models\Production(); 
        }
        $res       = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->welding_production_anomaly($model,'\app\models\Production()',true);
        }
        echo json_encode($res);die;
    }
    public function actionWeldingCoatingrepairWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Coatingrepair',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model     = new \app\models\Coatingrepair(); 
        }
        $res       = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            // $res = Yii::$app->anomaly->welding_coatingrepair_anomaly($model,'\app\models\Coatingrepair()',true); //as per client says anomaly section has been turn off
            $res = array('status'=>false);
        }
        echo json_encode($res);die;
    }

    public function actionCivilTrenchingWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Trenching',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model = new \app\models\Trenching(); 
        }
        $res = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            // $res = Yii::$app->anomaly->civil_trenching_anomaly($model,'\app\models\Trenching',true); //as per client says anomaly section has been turn off
        }
        echo json_encode($res);die;
    }

    public function actionCivilLoweringWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Lowering',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model = new \app\models\Lowering(); 
        }
        $res = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            // $res = Yii::$app->anomaly->civil_lowering_anomaly($model,'\app\models\Lowering',true); //as per client says anomaly section has been turn off
        }
        echo json_encode($res);die;
    }

    public function actionCivilBackfillingWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Backfilling',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model = new \app\models\Backfilling(); 
        }
        $res = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            // $res = Yii::$app->anomaly->civil_backfilling_anomaly($model,'\app\models\Backfilling',true); //as per client says anomaly section has been turn off
        }
        echo json_encode($res);die;
    }

    public function actionCivilReinstatementWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Reinstatement',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model = new \app\models\Reinstatement(); 
        }
        $res = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            // $res = Yii::$app->anomaly->civil_reinstatement_anomaly($model,'\app\models\Reinstatement',true); //as per client says anomaly section has been turn off
        }
        echo json_encode($res);die;
    }
    public function actionPrecomCleanguageWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Cleangauge',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model     = new \app\models\Cleangauge(); 
        }
        $res       = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->precom_cleangauge_anomaly($model,'\app\models\Cleangauge()',true);
        }
        echo json_encode($res);die;
    }
    public function actionPrecomHydrotestingWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Hydrotesting',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model     = new \app\models\Hydrotesting(); 
        }
        $res       = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->precom_hydrotesting_anomaly($model,'\app\models\Hydrotesting()',true);
        }
        echo json_encode($res);die;
    }

    public function actionCabStringingWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\CabStringing',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model = new \app\models\CabStringing(); 
        }
        $res = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->cable_stringing_anomaly($model,true);
        }
        echo json_encode($res);die;
    }

    public function actionCabSplicingWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\CabSplicing',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model = new \app\models\CabSplicing(); 
        }
        $res = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->cable_splicing_anomaly($model,true);
        }
        echo json_encode($res);die;
    }
    public function actionCableWarning($EditId=0){
        if(!empty($EditId)){
            $model 			= Yii::$app->general->getModelData('\app\models\Cable',$EditId); 
            if(isset($model['status']) && $model['status'] == false){
		    	return $this->redirect(['create']); 
            }            
        }else{
            $model = new \app\models\Cable(); 
        }
        $res = array('status'=>false);
        if($model->load(Yii::$app->request->post())){
            $res = Yii::$app->anomaly->cable_anomaly($model,true);
        }
        echo json_encode($res);die;
    }
}
