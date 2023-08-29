<div class="map-style <?php echo Yii::$app->controller->id == "sync" ? 'visual-p-0' : ''; ?>">
    <div id="map_canvas" class="<?php echo Yii::$app->controller->id == "sync" ? 'visual-w-100' : '';?>"></div>
    <?php if(Yii::$app->controller->id == "sync"){ ?>
    <div class="legend-collap-in"><button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button></div>
    <div id="legend" style="display:none;">
        <h5>
            <span>Legend</span>
            <button class="btn btn-default btn-sm pull-right legend-collap"><i class="fa fa-chevron-left"></i></button>
        </h5>
    </div>
    <?php } else { ?>
    <div id="legend"><h5>Legend</h5></div>
    <?php } ?>
</div>

<?php
$PipeLine = array();
$avg_lat = 0;
$avg_log = 0;

//Clear & Grade Section
if(isset($_GET['clear'])){
} else {
    $Clear = \app\models\Cleargrade::find()->active()->asArray()->all();
    if(!empty($Clear)){ 
        foreach($Clear as $k => $line){
            if (strpos($line['start_geo_location'], ',') === false && strpos($line['end_geo_location'], ',') === false) {
                continue;
            }
            $exp_from = explode(",", $line['start_geo_location']);
            $exp_to = explode(",", $line['end_geo_location']); 
            if(!empty($_GET['clear']) && $_GET['clear']=="checked"){

            }else{     
                
                
                $infoBox  = '<div class="card map-card">
                <div class="card-block pt-3">
                    <div class="clearfix">
                        <h5 class="text-bold-500 primary">Clear & Grade</h5>                                
                    </div>
                    <p>Report       : '.\yii\helpers\Html::a($line['report_number'],['/pipe/pipe-cleargrade/create/','EditId'=>$line['id']],['target'=>'_blank','data-pjax'=>0]).'</p>
                    <p>From Kp           : '.$line['start_kp'].'</p>
                    <p>To Kp           : '.$line['end_kp'].'</p>
                </div>
            </div>';
                array_push($PipeLine,
                    array(
                        'kp_point' =>  $infoBox,
                        'lat' => $exp_from[0],
                        'log' => $exp_from[1],
                        'end_lat' => $exp_to[0],
                        'end_log' => $exp_to[1],
                        "color" => "orange",
                        
                        "strokeWeight" => 12,
                        "kp"=>"KP-".$line['start_kp']
                    )
                );
                array_push($PipeLine,
                    array(
                        'kp_point' =>  $infoBox,
                        'lat' => $exp_to[0],
                        'log' => $exp_to[1],
                        'end_lat' => $exp_to[0],
                        'end_log' => $exp_to[1],
                        "color" => "orange",
                        "strokeWeight" => 12,
                        
                        "kp"=>"KP-".$line['end_kp']
                    )
                );
            }
                $avg_log = $exp_from[1];
                $avg_lat = $exp_from[0];            
        }
    }
}

if(isset($_GET['stringing'])){

}else{ 
    //Stringing Section
    $Stringing = \app\models\Stringing::find()->active()->asArray()->all();
    if(!empty($Stringing)){ 
        foreach($Stringing as $k => $line){
            if (strpos($line['geo_location'], ',') === false) {
                continue;
            }

            $exp_from = explode(",", $line['geo_location']);
            $exp_to = explode(",", $line['geo_location']);

            $infoBox  = '<div class="card map-card">
                            <div class="card-block pt-3">
                                <div class="clearfix">
                                    <h5 class="text-bold-500 primary">Stringing</h5>                                
                                </div>
                                <p>Report       : '.\yii\helpers\Html::a($line['report_number'],['/pipe/stringing/create','EditId'=>$line['id']],['target'=>'_blank','data-pjax'=>0]).'</p>
                                <p>KP           : '.$line['kp'].'</p>
                                <p>Pipe Number  : '.$line['pipe_number'].'</p>
                            </div>
                        </div>';

            array_push($PipeLine,
                array(
                    'kp_point' => $infoBox,
                    'lat' => $exp_from[0],
                    'log' => $exp_from[1],
                    'end_lat' => $exp_to[0],
                    'end_log' => $exp_to[1],
                    "color" => "#fd00ff",
                  
                    "strokeWeight" => 12,
                    "kp"=>"P-".$line['pipe_number']
                )
            );
        }
    }
}

//Welding Section
if(isset($_GET['welding'])){

}else{
    $welding =  \app\models\Welding::find()->active()->asArray()->all();
    if(!empty($welding)){  
        
        foreach($welding as $k=>$line){  
            if (strpos($line['geo_location'], ',') === false) {
                continue;
            }
            $Secondwelding =  \app\models\Welding::find()->where(['pipe_number'=>$line['next_pipe']])->active()->asArray()->one();
            $exp_from = explode(",",$line['geo_location']);
            $exp_to   = !empty($Secondwelding) ? explode(",", $Secondwelding['geo_location']) : explode(",", $line['geo_location']);

            $htmlReport= Yii::$app->general->getCombineReportNo($line['kp'],$line['weld_number']);

            $infoBox  = '<div class="card map-card">
            <div class="card-block pt-3">
                <div class="clearfix">
                    <h5 class="text-bold-500 primary">Welding</h5>                                
                </div>
                <p>KP           : '.$line['kp'].' Weld Number  : '.$line['weld_number'].'</p>'.$htmlReport.
           ' </div>
        </div>';

            array_push($PipeLine,array('kp_point'=>$infoBox,
            'lat'=>$exp_from[0],
            'log'=>$exp_from[1],
            'end_lat'=>$exp_to[0],
            'end_log'=>$exp_to[1],
            "color"=>"white",
            "kp"=>"W-".$line['weld_number'],
          
            "strokeWeight"=>10)
            );
        }
        
    }
}


//Welding Parameter Section
if(isset($_GET['parameter'])){

}else{
    $Parameter = \app\models\Parameter::find()->select(['welding_parameter_check.report_number as p_report_number','welding_parameter_check.id as p_id','welding_parameter_check.weld_number as p_weld_number','welding_parameter_check.kp as p_kp','welding.*'])
                ->leftJoin('welding','welding_parameter_check.weld_number = welding.weld_number AND welding_parameter_check.kp = welding.kp AND welding_parameter_check.project_id= welding.project_id AND welding.is_active = 1 AND welding.project_id='.Yii::$app->user->identity->project_id.' AND welding.is_deleted=0')
                ->active()->asArray()->all();
    if(!empty($Parameter)){ 
        $last =array();
        foreach($Parameter as $k => $line){
            if (strpos($line['geo_location'], ',') === false) {
                continue;
            }
            
            $Secondwelding =  \app\models\Welding::find()->where(['pipe_number'=>$line['next_pipe']])->active()->asArray()->one();
            $exp_from = explode(",",$line['geo_location']);
            $exp_to   = !empty($Secondwelding) ? explode(",", $Secondwelding['geo_location']) : explode(",", $line['geo_location']);
            
            $htmlReport= Yii::$app->general->getCombineReportNo($line['kp'],$line['weld_number']);
            
            $infoBox  = '<div class="card map-card">
                            <div class="card-block pt-3">
                                <div class="clearfix">
                                    <h5 class="text-bold-500 primary">Parameter Check</h5>
                                </div>
                                <p>KP           : '.$line['p_kp'].' Weld Number  : '.$line['weld_number'].'</p>'.$htmlReport.
                            '</div>
                        </div>';

            array_push($PipeLine,
                array(
                    'kp_point' => $infoBox,
                    'lat' => $exp_from[0],
                    'log' => $exp_from[1],
                    'end_lat' => $exp_to[0],
                    'end_log' => $exp_to[1],
                    "color" => "green",
                    "strokeWeight" => 12,
                    "kp"=>"W-".$line['weld_number'],
                )
            );
        }
    }
}

//Welding NDT Section
if(isset($_GET['ndt'])){

}else{
    $Ndt = \app\models\Ndt::find()->select(['welding.geo_location','welding_ndt.id as n_id','welding_ndt.report_number as p_report_number','welding_ndt.kp as n_kp','welding.*'])
            ->leftJoin('welding','welding_ndt.weld_number = welding.weld_number AND welding_ndt.kp = welding.kp AND welding_ndt.project_id= welding.project_id AND welding.is_active = 1 AND welding.project_id='.Yii::$app->user->identity->project_id.' AND welding.is_deleted=0')
            ->active()->asArray()->all();
    if(!empty($Ndt)){ 
        $last =array();
        foreach($Ndt as $k => $line){
            if (strpos($line['geo_location'], ',') === false) {
                continue;
            }

            $htmlReport= Yii::$app->general->getCombineReportNo($line['kp'],$line['weld_number']);

            $Secondwelding =  \app\models\Welding::find()->where(['pipe_number'=>$line['next_pipe']])->active()->asArray()->one();
            $exp_from = explode(",",$line['geo_location']);
            $exp_to   = !empty($Secondwelding) ? explode(",", $Secondwelding['geo_location']) : explode(",", $line['geo_location']);
           
            $infoBox  = '<div class="card map-card">
            <div class="card-block pt-3">
                <div class="clearfix">
                    <h5 class="text-bold-500 primary">NDT Testing</h5>                                
                </div>
               <p>KP           : '.$line['n_kp'].' Weld Number  : '.$line['weld_number'].'</p>'.$htmlReport.
            '</div>
        </div>';
           
            array_push($PipeLine,
                array(
                    'kp_point' =>  $infoBox,
                    'lat' => $exp_from[0],
                    'log' => $exp_from[1],
                    'end_lat' => $exp_to[0],
                    'end_log' => $exp_to[1],
                    "color" => "blue",
                    "strokeWeight" => 12,
                    "kp"=>"W-".$line['weld_number'],
                )
            );
            $last =  $exp_to;
        }
    }
}

//Welding Production Section
if(isset($_GET['coating'])){

}else{
    $Production = \app\models\Production::find()->select(['welding.geo_location',
    'welding_coating_production.id as c_id','welding_coating_production.report_number as p_report_number','welding_coating_production.kp as c_kp','welding.*'])
                ->leftJoin('welding','welding_coating_production.weld_number = welding.weld_number AND welding_coating_production.kp = welding.kp AND welding_coating_production.project_id= welding.project_id AND welding.is_active = 1 AND welding.project_id='.Yii::$app->user->identity->project_id.' AND welding.is_deleted=0')
                ->active()->asArray()->all();
    if(!empty($Production)){
        $last =array();
        foreach($Production as $k => $line){
            if (strpos($line['geo_location'], ',') === false) {
                continue;
            }
            $htmlReport= Yii::$app->general->getCombineReportNo($line['kp'],$line['weld_number']);
           
            $Secondwelding = \app\models\Welding::find()->where(['pipe_number'=>$line['next_pipe']])->active()->asArray()->one();
            $exp_from = explode(",",$line['geo_location']);
            $exp_to   = !empty($Secondwelding) ? explode(",", $Secondwelding['geo_location']) : explode(",", $line['geo_location']);
            
            $infoBox  = '<div class="card map-card">
            <div class="card-block pt-3">
                <div class="clearfix">
                    <h5 class="text-bold-500 primary">Coating Production</h5>                                
                </div>               
                <p>KP           : '.$line['c_kp'].' Weld Number  : '.$line['weld_number'].'</p>'.$htmlReport.
            '</div>
        </div>';
            array_push($PipeLine,
                array(
                    'kp_point' => $infoBox,
                    'lat' => $exp_from[0],
                    'log' => $exp_from[1],
                    'end_lat' => $exp_to[0],
                    'end_log' => $exp_to[1],
                    "color" => "#009da0",
                    "kp"=>"W-".$line['weld_number'],
                    "strokeWeight" => 12
                )
            );
            $last =  $exp_to;
        }
    }
}

//Civil Trenching Section
if(isset($_GET['trenching'])){

}else{
    $Trenching = \app\models\Trenching::find()->active()->asArray()->all();
    if(!empty($Trenching)){ 
        foreach($Trenching as $k => $ele){  
            $list = Yii::$app->general->getAllWeldData($ele['from_kp'],$ele['from_weld'],$ele['to_kp'],$ele['to_weld']);            
            if(!empty( $list)){
                foreach($list as $k=> $r){ 

                        $htmlReport= Yii::$app->general->getCombineReportNo($r['kp'],$r['weld_number']);
                        $Fromwelding = \app\models\Welding::find()->select(['geo_location'])
                            ->where(['kp'=>$r['kp'],'weld_number'=>$r['weld_number']])
                            ->active()
                            ->asArray()
                            ->one();

                        $ToKp   = isset($list[$k+1]['kp'])?$list[$k+1]['kp']:$r['kp'];
                        $ToWeld = isset($list[$k+1]['weld_number'])?$list[$k+1]['weld_number']:$r['weld_number'];
                        
                        $Towelding = \app\models\Welding::find()->select(['geo_location'])
                            ->where(['kp'=>$ToKp,'weld_number'=>$ToWeld])
                            ->active()
                            ->asArray()
                            ->one();              

                        if(isset($Fromwelding['geo_location']) && isset($Towelding['geo_location'])){
                            if (strpos($Fromwelding['geo_location'], ',') === false && strpos($Towelding['geo_location'], ',') === false) {
                                continue;
                            }
                            $exp_from = explode(",", $Fromwelding['geo_location']);
                            $exp_to = explode(",", $Towelding['geo_location']);

                           
                            $infoBox  = '<div class="card map-card">
                            <div class="card-block pt-3">
                                <div class="clearfix">
                                    <h5 class="text-bold-500 primary">Trenching</h5>                                
                                </div>
                                <p>KP           : '.$r['kp'].' Weld Number  : '.$r['weld_number'].'</p>'.$htmlReport.
                           '</div>
                        </div>';
                            array_push($PipeLine,
                                array(
                                    'kp_point' => $infoBox,
                                    'lat' => $exp_from[0],
                                    'log' => $exp_from[1],
                                    'end_lat' => $exp_to[0],
                                    'end_log' => $exp_to[1],
                                    "color" => "#f21706",
                                    "strokeWeight" => 12
                                )
                            );
                        }
                }
            }

        }
    }
}

if(isset($_GET['lowring'])){

}else{
    $Lowering = \app\models\Lowering::find()->active()->asArray()->all();
    if(!empty($Lowering)){ 
        foreach($Lowering as $k => $ele){  
            $list = Yii::$app->general->getAllWeldData($ele['from_kp'],$ele['from_weld'],$ele['to_kp'],$ele['to_weld']);            
            if(!empty( $list)){
                foreach($list as $k=> $r){ 

                        $htmlReport= Yii::$app->general->getCombineReportNo($r['kp'],$r['weld_number']);
                        $Fromwelding = \app\models\Welding::find()->select(['geo_location'])
                            ->where(['kp'=>$r['kp'],'weld_number'=>$r['weld_number']])
                            ->active()
                            ->asArray()
                            ->one();

                        $ToKp   = isset($list[$k+1]['kp'])?$list[$k+1]['kp']:$r['kp'];
                        $ToWeld = isset($list[$k+1]['weld_number'])?$list[$k+1]['weld_number']:$r['weld_number'];
                        
                        $Towelding = \app\models\Welding::find()->select(['geo_location'])
                            ->where(['kp'=>$ToKp,'weld_number'=>$ToWeld])
                            ->active()
                            ->asArray()
                            ->one();              

                        if(isset($Fromwelding['geo_location']) && isset($Towelding['geo_location'])){
                            if (strpos($Fromwelding['geo_location'], ',') === false && strpos($Towelding['geo_location'], ',') === false) {
                                continue;
                            }
                            $exp_from = explode(",", $Fromwelding['geo_location']);
                            $exp_to = explode(",", $Towelding['geo_location']);

                           
                            $infoBox  = '<div class="card map-card">
                            <div class="card-block pt-3">
                                <div class="clearfix">
                                    <h5 class="text-bold-500 primary">Lowering</h5>                                
                                </div>
                                <p>KP           : '.$r['kp'].' Weld Number  : '.$r['weld_number'].'</p>'.$htmlReport.
                                
                           '</div>
                        </div>';
                            array_push($PipeLine,
                                array(
                                    'kp_point' => $infoBox,
                                    'lat' => $exp_from[0],
                                    'log' => $exp_from[1],
                                    'end_lat' => $exp_to[0],
                                    'end_log' => $exp_to[1],
                                    "color" => "#616161",
                                    "strokeWeight" => 12
                                )
                            );
                        }
                }
            }

        }
    }
}

if(isset($_GET['backfilling'])){

}else{
    $Backfilling = \app\models\Backfilling::find()->active()->asArray()->all();
    if(!empty($Backfilling)){ 
        foreach($Backfilling as $k => $ele){  
            $list = Yii::$app->general->getAllWeldData($ele['from_kp'],$ele['from_weld'],$ele['to_kp'],$ele['to_weld']);            
            if(!empty( $list)){
                foreach($list as $k=> $r){ 

                        $htmlReport= Yii::$app->general->getCombineReportNo($r['kp'],$r['weld_number']);
                        $Fromwelding = \app\models\Welding::find()->select(['geo_location'])
                            ->where(['kp'=>$r['kp'],'weld_number'=>$r['weld_number']])
                            ->active()
                            ->asArray()
                            ->one();

                        $ToKp   = isset($list[$k+1]['kp'])?$list[$k+1]['kp']:$r['kp'];
                        $ToWeld = isset($list[$k+1]['weld_number'])?$list[$k+1]['weld_number']:$r['weld_number'];
                        
                        $Towelding = \app\models\Welding::find()->select(['geo_location'])
                            ->where(['kp'=>$ToKp,'weld_number'=>$ToWeld])
                            ->active()
                            ->asArray()
                            ->one();              

                        if(isset($Fromwelding['geo_location']) && isset($Towelding['geo_location'])){
                            if (strpos($Fromwelding['geo_location'], ',') === false && strpos($Towelding['geo_location'], ',') === false) {
                                continue;
                            }
                            $exp_from = explode(",", $Fromwelding['geo_location']);
                            $exp_to = explode(",", $Towelding['geo_location']);

                           
                            $infoBox  = '<div class="card map-card">
                            <div class="card-block pt-3">
                                 <div class="clearfix">
                                    <h5 class="text-bold-500 primary">Backfilling</h5>                                
                                 </div>
                                 <p>KP           : '.$r['kp'].' Weld Number  : '.$r['weld_number'].'</p>'.$htmlReport.
                                
                           '</div>
                        </div>';
                            array_push($PipeLine,
                                array(
                                    'kp_point' => $infoBox,
                                    'lat' => $exp_from[0],
                                    'log' => $exp_from[1],
                                    'end_lat' => $exp_to[0],
                                    'end_log' => $exp_to[1],
                                    "color" => "#2196f3",
                                    "strokeWeight" => 12
                                )
                            );
                        }
                }
            }

        }
    }
}

//Civil Reinstatement Section
if(isset($_GET['reinstatement'])){

}else{
    $Reinstatement = \app\models\Reinstatement::find()->active()->asArray()->all();
    if(!empty($Reinstatement)){ 
        foreach($Reinstatement as $k => $ele){  
            $list = Yii::$app->general->getAllWeldData($ele['from_kp'],$ele['from_weld'],$ele['to_kp'],$ele['to_weld']);            
            if(!empty( $list)){
                foreach($list as $k=> $r){ 

                        $htmlReport= Yii::$app->general->getCombineReportNo($r['kp'],$r['weld_number']);
                        $Fromwelding = \app\models\Welding::find()->select(['geo_location'])
                            ->where(['kp'=>$r['kp'],'weld_number'=>$r['weld_number']])
                            ->active()
                            ->asArray()
                            ->one();

                        $ToKp   = isset($list[$k+1]['kp'])?$list[$k+1]['kp']:$r['kp'];
                        $ToWeld = isset($list[$k+1]['weld_number'])?$list[$k+1]['weld_number']:$r['weld_number'];
                        
                        $Towelding = \app\models\Welding::find()->select(['geo_location'])
                            ->where(['kp'=>$ToKp,'weld_number'=>$ToWeld])
                            ->active()
                            ->asArray()
                            ->one();              

                        if(isset($Fromwelding['geo_location']) && isset($Towelding['geo_location'])){
                            if (strpos($Fromwelding['geo_location'], ',') === false && strpos($Towelding['geo_location'], ',') === false) {
                                continue;
                            }
                            $exp_from = explode(",", $Fromwelding['geo_location']);
                            $exp_to = explode(",", $Towelding['geo_location']);

                           
                            $infoBox  = '<div class="card map-card">
                            <div class="card-block pt-3">
                                <div class="clearfix">
                                    <h5 class="text-bold-500 primary">Reinstatement</h5>                                
                                </div>
                                <p>KP           : '.$r['kp'].' Weld Number  : '.$r['weld_number'].'</p>'.$htmlReport.
                                
                           '</div>
                        </div>';
                            array_push($PipeLine,
                                array(
                                    'kp_point' => $infoBox,
                                    'lat' => $exp_from[0],
                                    'log' => $exp_from[1],
                                    'end_lat' => $exp_to[0],
                                    'end_log' => $exp_to[1],
                                    "color" => "#7b1fa2",
                                    "strokeWeight" => 12
                                )
                            );
                        }
                }
            }

        }
    }
}
if(isset($_GET['cathodic'])){

}else{
    $Cathodicprotection = \app\models\Cathodicprotection::find()->active()->asArray()->all();
    if(!empty($Cathodicprotection)){ 
        foreach($Cathodicprotection as $k => $ele){  
            $list = Yii::$app->general->getAllWeldData($ele['from_kp'],$ele['from_weld'],$ele['to_kp'],$ele['to_weld']);            
            if(!empty( $list)){
                foreach($list as $k=> $r){ 

                        $htmlReport= Yii::$app->general->getCombineReportNo($r['kp'],$r['weld_number']);
                        $Fromwelding = \app\models\Welding::find()->select(['geo_location'])
                            ->where(['kp'=>$r['kp'],'weld_number'=>$r['weld_number']])
                            ->active()
                            ->asArray()
                            ->one();

                        $ToKp   = isset($list[$k+1]['kp'])?$list[$k+1]['kp']:$r['kp'];
                        $ToWeld = isset($list[$k+1]['weld_number'])?$list[$k+1]['weld_number']:$r['weld_number'];
                        
                        $Towelding = \app\models\Welding::find()->select(['geo_location'])
                            ->where(['kp'=>$ToKp,'weld_number'=>$ToWeld])
                            ->active()
                            ->asArray()
                            ->one();              

                        if(isset($Fromwelding['geo_location']) && isset($Towelding['geo_location'])){
                            if (strpos($Fromwelding['geo_location'], ',') === false && strpos($Towelding['geo_location'], ',') === false) {
                                continue;
                            }
                            $exp_from = explode(",", $Fromwelding['geo_location']);
                            $exp_to = explode(",", $Towelding['geo_location']);

                           
                            $infoBox  = '<div class="card map-card">
                            <div class="card-block pt-3">
                                <div class="clearfix">
                                    <h5 class="text-bold-500 primary">Cathodic Protection</h5>                                
                                </div>
                                <p>KP           : '.$r['kp'].' Weld Number  : '.$r['weld_number'].'</p>'.$htmlReport.
                                
                           '</div>
                        </div>';
                            array_push($PipeLine,
                                array(
                                    'kp_point' => $infoBox,
                                    'lat' => $exp_from[0],
                                    'log' => $exp_from[1],
                                    'end_lat' => $exp_to[0],
                                    'end_log' => $exp_to[1],
                                    "color" => "#DDD",
                                    "strokeWeight" => 12
                                )
                            );
                        }
                }
            }

        }
    }
}
if(isset($_GET['cleangauge'])){

}else{
    $Cleangauge = \app\models\Cleangauge::find()->active()->asArray()->all();
    if(!empty($Cleangauge)){ 
        foreach($Cleangauge as $k => $ele){  
            $list = Yii::$app->general->getAllWeldData($ele['from_kp'],$ele['from_weld'],$ele['to_kp'],$ele['to_weld']);            
            if(!empty( $list)){
                foreach($list as $k=> $r){ 

                        $htmlReport= Yii::$app->general->getCombineReportNo($r['kp'],$r['weld_number']);
                        $Fromwelding = \app\models\Welding::find()->select(['geo_location'])
                            ->where(['kp'=>$r['kp'],'weld_number'=>$r['weld_number']])
                            ->active()
                            ->asArray()
                            ->one();

                        $ToKp   = isset($list[$k+1]['kp'])?$list[$k+1]['kp']:$r['kp'];
                        $ToWeld = isset($list[$k+1]['weld_number'])?$list[$k+1]['weld_number']:$r['weld_number'];
                        
                        $Towelding = \app\models\Welding::find()->select(['geo_location'])
                            ->where(['kp'=>$ToKp,'weld_number'=>$ToWeld])
                            ->active()
                            ->asArray()
                            ->one();              

                        if(isset($Fromwelding['geo_location']) && isset($Towelding['geo_location'])){
                            if (strpos($Fromwelding['geo_location'], ',') === false && strpos($Towelding['geo_location'], ',') === false) {
                                continue;
                            }
                            $exp_from = explode(",", $Fromwelding['geo_location']);
                            $exp_to = explode(",", $Towelding['geo_location']);

                           
                            $infoBox  = '<div class="card map-card">
                            <div class="card-block pt-3">
                                <div class="clearfix">
                                    <h5 class="text-bold-500 primary">Cleangauge</h5>                                
                                </div>
                                <p>KP           : '.$r['kp'].' Weld Number  : '.$r['weld_number'].'</p>'.$htmlReport.
                                
                           '</div>
                        </div>';
                            array_push($PipeLine,
                                array(
                                    'kp_point' => $infoBox,
                                    'lat' => $exp_from[0],
                                    'log' => $exp_from[1],
                                    'end_lat' => $exp_to[0],
                                    'end_log' => $exp_to[1],
                                    "color" => "#00f92c",
                                    "strokeWeight" => 12
                                )
                            );
                        }
                }
            }

        }
    }
}

//Precom Hydrotesting Section
if(isset($_GET['hydro'])){

}else{
    $Hydrotesting = \app\models\Hydrotesting::find()->active()->asArray()->all();
    if(!empty($Hydrotesting)){ 
        foreach($Hydrotesting as $k => $ele){  
            $list = Yii::$app->general->getAllWeldData($ele['from_kp'],$ele['from_weld'],$ele['to_kp'],$ele['to_weld']);            
            if(!empty( $list)){
                foreach($list as $k=> $r){ 

                        $htmlReport= Yii::$app->general->getCombineReportNo($r['kp'],$r['weld_number']);
                        $Fromwelding = \app\models\Welding::find()->select(['geo_location'])
                            ->where(['kp'=>$r['kp'],'weld_number'=>$r['weld_number']])
                            ->active()
                            ->asArray()
                            ->one();

                        $ToKp   = isset($list[$k+1]['kp'])?$list[$k+1]['kp']:$r['kp'];
                        $ToWeld = isset($list[$k+1]['weld_number'])?$list[$k+1]['weld_number']:$r['weld_number'];
                        
                        $Towelding = \app\models\Welding::find()->select(['geo_location'])
                            ->where(['kp'=>$ToKp,'weld_number'=>$ToWeld])
                            ->active()
                            ->asArray()
                            ->one();              

                        if(isset($Fromwelding['geo_location']) && isset($Towelding['geo_location'])){
                            if (strpos($Fromwelding['geo_location'], ',') === false && strpos($Towelding['geo_location'], ',') === false) {
                                continue;
                            }
                            $exp_from = explode(",", $Fromwelding['geo_location']);
                            $exp_to = explode(",", $Towelding['geo_location']);

                           
                            $infoBox  = '<div class="card map-card">
                            <div class="card-block pt-3">
                                <div class="clearfix">
                                    <h5 class="text-bold-500 primary">Hydrotesting</h5>                                
                                </div>
                                <p>KP           : '.$r['kp'].' Weld Number  : '.$r['weld_number'].'</p>'.$htmlReport.
                                
                           '</div>
                        </div>';
                            array_push($PipeLine,
                                array(
                                    'kp_point' => $infoBox,
                                    'lat' => $exp_from[0],
                                    'log' => $exp_from[1],
                                    'end_lat' => $exp_to[0],
                                    'end_log' => $exp_to[1],
                                    "color" => "#25cfec",
                                    "strokeWeight" => 12
                                )
                            );
                        }
                }
            }

        }
    }
}

//Precom Surveying Section
if(isset($_GET['DCVG'])){

}else{
    $Surveying = \app\models\Surveying::find()->active()->asArray()->all();
    if(!empty($Surveying)){ 
       
        foreach($Surveying as $k => $line){
            if (strpos($line['geo_location'], ',') === false ) {
                continue;
            }
            $exp_from = explode(",",$line['geo_location']);
            $exp_to = explode(",",$line['geo_location']);

            $infoBox  = '<div class="card map-card" >
            <div class="card-block pt-3">
                <div class="clearfix">
                    <h5 class="text-bold-500 primary">DCVG Testing</h5>                                
                </div>
                <p>Report       : '.\yii\helpers\Html::a($line['report_number'],['/precommissioning/surveying/create','EditId'=>$line['id']],['target'=>'_blank','data-pjax'=>0]).'</p>
                <p> KP           : '.$line['kp'].'</p>
            </div>
        </div>';

            array_push($PipeLine,
                array(
                    'kp_point' => $infoBox,
                    'lat' => $exp_from[0],
                    'log' => $exp_from[1],
                    'end_lat' => $exp_to[0],
                    'end_log' => $exp_to[1],
                    "color" => "#f9ff00",
                    "strokeWeight" => 12
                )
            );
        }
    }
}

$notstarted = $clear = $stringing = $welding = $parameter = $ndt = $coating = $trenching = $lowring = $backfilling = $reinstatement = $cathodic = $cleangauge = $hydro = $DCVG = "checked";

$notstarted  = !empty($_GET['notstarted']) ? "" : "checked";
$clear       = !empty($_GET['clear']) ? "" : "checked";
$stringing  = !empty($_GET['stringing']) ? "" : "checked";
$welding  = !empty($_GET['welding']) ? "" : "checked";
$parameter  = !empty($_GET['parameter']) ? "" : "checked";
$ndt  = !empty($_GET['ndt']) ? "" : "checked";
$coating  = !empty($_GET['coating']) ? "" : "checked";
$trenching  = !empty($_GET['trenching']) ? "" : "checked";
$lowring  = !empty($_GET['lowring']) ? "" : "checked";
$backfilling  = !empty($_GET['backfilling']) ? "" : "checked";
$reinstatement  = !empty($_GET['reinstatement']) ? "" : "checked";
$cathodic  = !empty($_GET['cathodic']) ? "" : "checked";
$cleangauge  = !empty($_GET['cleangauge']) ? "" : "checked";
$hydro  = !empty($_GET['hydro']) ? "" : "checked";
$DCVG  = !empty($_GET['DCVG']) ? "" : "checked";

$this->registerJs('
    function initializeReportMap() {
        var icons = {
            // notstarted: {
            //     name: " Activity Not Started",
            //     icon:  "#0fe1d9b5",
            //     checkbox:"<input '.$notstarted.' type=\'checkbox\' class=\'label-checkbox\' value=\'notstarted\' >"
            // },
            clear: {
                name: "Clear Grade",
                icon:  "orange",
                  checkbox:"<input '.$clear.' type=\'checkbox\' class=\'label-checkbox\' value=\'clear\'>"
            },
            stringing: {
                name: "Stringing",
                icon:  "#fd00ff",
                  checkbox:"<input '.$stringing.' type=\'checkbox\' class=\'label-checkbox\' value=\'stringing\'>"
            },
            welding: {
                name: "Welding",
                icon:  "white",
                  checkbox:"<input '.$welding.' type=\'checkbox\' class=\'label-checkbox\' value=\'welding\'>"
            },
            parameter: {
                name: "Parameter",
                icon:  "green",
                  checkbox:"<input '.$parameter.' type=\'checkbox\' class=\'label-checkbox\' value=\'parameter\'>"
            },
            ndt: {
                name: "Ndt",
                icon:  "blue",
                  checkbox:"<input '.$ndt.' type=\'checkbox\' class=\'label-checkbox\' value=\'ndt\'>"
            },
            coating: {
                name: "Coating",
                icon:  "#009da0",
                  checkbox:"<input '.$coating.' type=\'checkbox\' class=\'label-checkbox\' value=\'coating\'>"
            },
            trenching: {
                name: "Trenching",
                icon:  "#f21706",
                  checkbox:"<input '.$trenching.' type=\'checkbox\' class=\'label-checkbox\' value=\'trenching\'>"
            },
            lowring: {
                name: "Lowering",
                icon:  "#616161",
                  checkbox:"<input '.$lowring.' type=\'checkbox\' class=\'label-checkbox\' value=\'lowring\'>"
            },
            backfilling: {
                name: "Backfilling",
                icon:  "#2196f3",
                  checkbox:"<input '.$backfilling.' type=\'checkbox\' class=\'label-checkbox\' value=\'backfilling\'>"
            },
            reinstatement: {
                name: "Reinstatement",
                icon:  "#7b1fa2",
                  checkbox:"<input '.$reinstatement.' type=\'checkbox\' class=\'label-checkbox\' value=\'reinstatement\'>"
            },
            cathodic: {
                name: "Cathodic Protection",
                icon:  "#DDD",
                  checkbox:"<input '.$cathodic.' type=\'checkbox\' class=\'label-checkbox\' value=\'cathodic\'>"
            },
            cleangauge: {
                name: "Clean & Guage",
                icon:  "#00f92c",
                  checkbox:"<input '.$cleangauge.' type=\'checkbox\' class=\'label-checkbox\' value=\'cleangauge\'>"
            },
            hydro: {
                name: "Hydro Testing",
                icon:  "#25cfec",
                  checkbox:"<input '.$hydro.' type=\'checkbox\' class=\'label-checkbox\' value=\'hydro\'>"
            },
            DCVG: {
                name: "DCVG Surveying",
                icon:  "#f9ff00",
                  checkbox:"<input '.$DCVG.' type=\'checkbox\' class=\'label-checkbox\' value=\'DCVG\'>"
            }
        };
        var KpPoint    =   '.json_encode($PipeLine).'
        var lat_center =   '.$avg_lat.'
        var log_center =   '.$avg_log.'

        var map = new google.maps.Map(document.getElementById("map_canvas"), { 
            center: new google.maps.LatLng(lat_center, log_center),
            zoom: 18,
            mapTypeId: google.maps.MapTypeId.SATELLITE 
        });

        var infowindow = new google.maps.InfoWindow();    
        var legend = document.getElementById("legend");
        for (var key in icons) {
            var type = icons[key];
            var name = type.name;
            var icon = type.icon;
            var checkbox = type.checkbox;
            var div = document.createElement("div");
            div.innerHTML = "<div class=\"row col-md-12\"><span class=\"map-lable pull-left clearfix\">"+checkbox+"</span><span class=\"icon color-d\" style=\"background-color:"+icon+"\"></span><span class=\"map-lable\">"+name+"</span></div>";
            legend.appendChild(div);
        }
        var divButton = document.createElement("div");
        divButton.innerHTML = "<div class=\"row col-md-12\"><button class=\"btn btn-sm btn-success legend-button mt-2 mb-0\">Apply</button></div>";
        legend.appendChild(divButton);

        map.controls[google.maps.ControlPosition.LEFT_TOP].push(legend);
      

        var marker, i;  
        for (i = 0; i < KpPoint.length; i++) {       
            var PipePath   =  [ new google.maps.LatLng(KpPoint[i].lat, KpPoint[i].log),new google.maps.LatLng(KpPoint[i].end_lat, KpPoint[i].end_log),];
            var pipeCoordinate = new google.maps.Polyline({
                path: PipePath,
                strokeColor: KpPoint[i].color,
                strokeOpacity: 5,
                strokeWeight: KpPoint[i].strokeWeight
            });
            pipeCoordinate.setMap(map); 
        
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(KpPoint[i].lat, KpPoint[i].log),
                map: map,
                icon: pinSymbol( KpPoint[i].color),
                label: {
                    text: KpPoint[i].kp,
                    color: "#000",
                    fontSize: "18px",
                    fontWeight: "bold",
                    strokeColor: "#000000",
                    background:"red"
                  },
                  labelOrigin : new google.maps.Point(90, 90)
            });   
        
            google.maps.event.addListener(marker, "click", (function(marker, i) {
                return function() {
                    infowindow.setContent(KpPoint[i].kp_point);
                    infowindow.open(map, marker);
                }
            })(marker, i));    
        }
    }
    if($(".map-style").length !== 0){
        initializeReportMap();
    }
    function pinSymbol(color) {
        return {
            path: "M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z M -2,-30 a 2,2 0 1,1 4,0 2,2 0 1,1 -4,0",
            fillColor: color,
            fillOpacity: 1,
            strokeColor: "#000",
            strokeWeight: 2,
            scale: 1,
       };
    }
');
?>