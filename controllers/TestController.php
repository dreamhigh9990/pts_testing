<?php
namespace app\controllers;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT');
class TestController extends Controller
{
   public $enableCsrfValidation = false;
   public function actionList(){
    $request = Yii::$app->request;
    $pageno = $request->post('pageno');
    $perpage    = !empty($request->post('perpage'))?$request->post('perpage'):10;
    $start = !empty($pageno)?($pageno-1)*$perpage:0;
    $totalentries   =   \app\models\Pipe::find()->count();
    $PipeList = \app\models\Pipe::find()->select(['pipe_number','wall_thikness','yeild_strength','heat_number','length','weight','od'])        
    ->offset($start)   
    ->limit($perpage) 
    ->asArray() 
    ->all();
    $data = array(
        'thead'=>array('Pipe Number','Wall Thickness','Yield Stength','Heat Number','Length','Weight','Od'),
        'tbody'=>$PipeList,
        'tfoot'=>array(
            'pager'=>array('totalentries'=>      $totalentries,
                           'perpage'     =>      $perpage,
                           'totalpage'   =>      ceil($totalentries/$perpage),
                           'currentpage' =>      $pageno
                    )
        )
    );
    echo json_encode($data);die;
   }
   public function actionReception(){
    $request = Yii::$app->request;
    $pageno = $request->post('pageno');
    $perpage    = !empty($request->post('perpage'))?$request->post('perpage'):10;
    $search = !empty($request->post('search'))?$request->post('search'):"";
    
  //  $perpage    =   10;
    $start = !empty($pageno)?($pageno-1)*$perpage:0;
    $totalentries   =   \app\models\Reception::find()->where(['LIKE','pipe_number',$search])->count();
    $PipeList = \app\models\Reception::find()->select(['pipe_number','report_number','location','created_at'])  
    ->where(['LIKE','pipe_number',$search])
    ->offset($start)   
    ->limit($perpage) 
    ->asArray() 
    ->all();
    $data = array(
        'thead'=>array('Pipe Number','Report Number','Location','Date'),
        'tbody'=>$PipeList,
        'tfoot'=>array(
            'pager'=>array('totalentries'=>      $totalentries,
                           'perpage'     =>      $perpage,
                           'totalpage'   =>      ceil($totalentries/$perpage),
                           'currentpage' =>      $pageno
                    )
        )
    );
    echo json_encode($data);die;
   }
}
