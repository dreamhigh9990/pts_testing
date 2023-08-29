<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use miloschuman\highcharts\Highcharts;
?>
<section id="basic-form-layouts">
		<div class="row">
			<div class="col-sm-12">
				<div class="content-header"></div>
			</div>
		</div>
		<div class="row match-height">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title" id="basic-layout-form">
                            WELDER ANALYSIS - OVERALL REPAIRS REPORT
                            <button class="btn btn-raised btn-white btn-min-width mr-1 mb-1 black pull-right" onclick="printDiv();">Print Report</button>
                        </h4>
					</div>
					<div class="card-body">	
                        <div class="production-data" id="print-body">
                            <div class="table-summary">
                                <div class="clearfix">
                                    <div class="logo-appro">
                                    <?= \yii\helpers\Html::img(Yii::$app->general->logo(), ['alt'=>'some', 'class'=>'appro-logo']);?>             </div>
                                        <div class="appro-title">
                                            <h1>OVERALL REPAIRS REPORT</h1>
                                        </div>
                                    </div>
                                    <div class="title-blueBG"></div>
                                    <div class="ntd-summary-main row"> 
                                            <div class="col-md-6">                                           
                                            <?php
                                                          // print_r($data['mainline']);die;     
                                                                  echo Highcharts::widget([
                                                                    'options' => [
                                                                        'chart'=> [
                                                                            'type'=> 'bar'
                                                                        ],
                                                                        'title'=> [
                                                                            'text'=> 'Main Line - No of Repair Vs Welder  | Total Repair = '.array_sum($data['mainline']['count'])
                                                                        ],
                                                                        'xAxis'=> [
                                                                            'categories'=> $data['mainline']['name'],
                                                                            'title'=> [
                                                                                'text'=> null
                                                                            ]
                                                                        ],
                                                                        'yAxis'=> [                                                                           
                                                                            'title'=> [
                                                                                'text'=> 'Repair Count',
                                                                                'align'=> 'high'
                                                                            ],
                                                                            'labels'=> [
                                                                                'overflow'=> 'justify'
                                                                            ]
                                                                        ],
                                                                        'plotOptions'=> [
                                                                            'bar'=> [
                                                                                'dataLabels'=> [
                                                                                    'enabled'=> true
                                                                                ]
                                                                            ]
                                                                        ],
                                                                        'credits'=> [
                                                                            'enabled'=> false
                                                                        ],
                                                                        'series'=> [
                                                                             [
                                                                                 'name'=>'Repair Count',
                                                                                'data'=> $data['mainline']['count']
                                                                             ],
                                                                        ]
                                                                    ]
                                                                ]);
                                                            
                                                            ?>                       
                                            </div>
                                            <div class="col-md-6">                                           
                                            <?php
                                                          // print_r($data['mainline']);die;     
                                                                  echo Highcharts::widget([
                                                                    'options' => [
                                                                        'chart'=> [
                                                                            'type'=> 'bar'
                                                                        ],
                                                                        'title'=> [
                                                                            'text'=> 'Tie Line - No of Repair rate Vs Welder  | Total Repair = '.array_sum($data['tieline']['count'])
                                                                        ],
                                                                        'xAxis'=> [
                                                                            'categories'=> $data['tieline']['name'],
                                                                            'title'=> [
                                                                                'text'=> null
                                                                            ]
                                                                        ],
                                                                        'yAxis'=> [                                                                           
                                                                            'title'=> [
                                                                                'text'=> 'Repair Count',
                                                                                'align'=> 'high'
                                                                            ],
                                                                            'labels'=> [
                                                                                'overflow'=> 'justify'
                                                                            ]
                                                                        ],
                                                                        'tooltip'=> [
                                                                            'valueSuffix'=> ' %'
                                                                        ],
                                                                        'plotOptions'=> [
                                                                            'bar'=> [
                                                                                'dataLabels'=> [
                                                                                    'enabled'=> true
                                                                                ]
                                                                            ]
                                                                        ],
                                                                        'credits'=> [
                                                                            'enabled'=> false
                                                                        ],
                                                                        'series'=> [
                                                                             [
                                                                                 'name'=>'Repair Rate',
                                                                                'data'=> $data['tieline']['rate']
                                                                             ],
                                                                        ]
                                                                    ]
                                                                ]);
                                                            
                                                            ?>                       
                                            </div>
                                       
                                    </div>
                                    <div class="ntd-summary-main row"> 
                                            <div class="col-md-12">                                           
                                    <?php
                                                // print_r($data['mainline']);die;     
                                                echo Highcharts::widget([
                                                'options' => [
                                                    'chart'=> [
                                                        'type'=> 'bar'
                                                    ],
                                                    'title'=> [
                                                        'text'=> 'Main Line - Weld Bead Defect Positions'
                                                    ],
                                                    'xAxis'=> [
                                                        'categories'=> $data['defect_position']
                                                    ],
                                                    'yAxis'=> [
                                                        'min'=> 0,
                                                        'title'=> [
                                                            'text'=> ''
                                                        ]
                                                    ],
                                                    'legend'=> [
                                                        'reversed'=> true
                                                    ],
                                                    'plotOptions'=> [
                                                        'series'=> [
                                                            'stacking'=> 'normal'
                                                        ]
                                                    ],
                                                    'credits'=> [
                                                        'enabled'=> false
                                                    ],
                                                    'series'=> $defectPosArray,
                                                ]
                                                
                                            ]);
                                        
                                                ?>                       
                                            </div>
                                            <div class="col-md-12">                                           
                                             <?php
                                                          // print_r($data['mainline']);die;     
                                                                  echo Highcharts::widget([
                                                                    'options' => [
                                                                        'chart'=> [
                                                                            'type'=> 'bar'
                                                                        ],
                                                                        'title'=> [
                                                                            'text'=> 'Tie Line - Weld Bead Defect Positions'
                                                                        ],
                                                                        'xAxis'=> [
                                                                            'categories'=> $data['defect_position']
                                                                        ],
                                                                        'yAxis'=> [
                                                                            'min'=> 0,
                                                                            'title'=> [
                                                                                'text'=> ''
                                                                            ]
                                                                        ],
                                                                        'legend'=> [
                                                                            'reversed'=> true
                                                                        ],
                                                                        'plotOptions'=> [
                                                                            'series'=> [
                                                                                'stacking'=> 'normal'
                                                                            ]
                                                                        ],
                                                                        'credits'=> [
                                                                            'enabled'=> false
                                                                        ],
                                                                        'series'=> $defectPosArrayTie,
                                                                    ]
                                                                    
                                                                ]);
                                                            
                                                            ?>                       
                                            </div>
                                </div>
				            </div>
		            	</div>
                    </div>
		</section>