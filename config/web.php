<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$config = [
    'name'=>'Pipeline Management System',
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone' => 'Australia/Melbourne',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to  
            // use your own export download action or custom translation 
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ],
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
        'pipe' => [
            'class' => 'app\modules\pipe\Module',
        ],
        'welding' => [
            'class' => 'app\modules\welding\Module',
        ],
        'api' => [
            'class' => 'app\modules\api\Module',
        ],
        'civil' => [
            'class' => 'app\modules\civil\Module',
        ],
        'cabling' => [
            'class' => 'app\modules\cabling\Module',
        ],
        'precommissioning' => [
            'class' => 'app\modules\precommissioning\Module',
        ],
        'report' => [
            'class' => 'app\modules\report\Module',
        ],
        'vehicle' => [
            'class' => 'app\modules\vehicle\Module',
        ],
        'db-manager' => [
            'class' => 'bs\dbManager\Module',
            // path to directory for the dumps
            'path' => '@app/backups',
            // list of registerd db-components
            'dbList' => ['db'],
            'as access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        // 'roles' => ['admin'],
                    ],
                ],
            ],
        ],
    ],
    'defaultRoute' => 'site/login',
    'components' => [
        'general' => [ 
            'class' => 'app\components\General',
        ],
        'weld' => [ 
            'class' => 'app\components\Weld',
        ],
		'anomaly' => [ 
            'class' => 'app\components\Anomaly',
        ],
        'export' => [
            'class' => 'app\components\Export'
        ],
        'trans' => [
            'class' => 'app\components\Translation'
        ],
        'request' => [
            'cookieValidationKey' => '2IpygZ1mH4gVZxbn6gHCBG7myA34Q4PN',
            'enableCsrfValidation' => false,
			'parsers' => ['application/json' => 'yii\web\JsonParser']
        ],
		'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                if(Yii::$app->controller->module->id == "api"){
                    $response = $event->sender;
                    $Log               = new \app\models\Log;
                    if (!empty($response->data) && $response->data !== null) {
                        $Log->request      = json_encode(Yii::$app->getRequest()->getBodyParams());
                        $Log->access_token = !empty($_GET['access-token'])?$_GET['access-token']:"";
                        $Log->response     = json_encode($response);
                        $Log->action       = Yii::$app->controller->action->id;
                        $Log->user_id      = !empty(Yii::$app->user->identity->id) ? Yii::$app->user->identity->id : 0;
                        $Log->project_id   = !empty(Yii::$app->user->identity->project_id) ? Yii::$app->user->identity->project_id : 0;
                        if(isset($response->data['status']) && $response->data['status'] == false){ 
                            $Log->status    = 'Error';
                            $Log->error =!empty($response->data['message'])?$response->data['message']:$response->data;

                            $response->data = [
                                'status' => false,
                                'data' => "",
                                'message'=>!empty($response->data['message'])?$response->data['message']:$response->data
                            ];
                        }else{
                            $Log->status    =  $response->isSuccessful==false ? 'Error':"Success";
                            $Log->error     =  $response->isSuccessful==false ?  isset($response->data['message']) ? $response->data['message'] : json_encode($response->data) : "";
                            $response->data = [
                                'status' => $response->isSuccessful,
                                'data' => $response->data,
                                'message'=>$response->isSuccessful==false ? isset($response->data['message']) ? $response->data['message']: $response->data : ""
                            ];
                        }
                    }else{
                        $Log->status    = 'Error';
                        $Log->error =!empty($response->data['message'])?$response->data['message']:$response->data;
                        $response->data = [
                            'status' => false,
                            'data' => !empty($response->data)?$response->data:"",
                            'message' => 'Response data is blank,please try after some time'
                        ];
                    }
                    //$Log->save(false):"";

                    $response->statusCode = 200;
                    $headers = Yii::$app->response->headers;
                    $headers->set('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE,OPTIONS');
                    $headers->set('Access-Control-Allow-Headers', 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
                    $headers->set('Access-Control-Allow-Origin', '*');
                }
            }
		],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => true,
            'rules' => [
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'dosamigos\google\maps\MapAsset' => [
                    'options' => [
                        'key' => 'AIzaSyA4zKcrlkRzPpnvnrHQH480LPGi_OmRfJA',
                        'libraries' => 'places',
                        'v' => '3.exp',
                        'sensor'=> 'false'
                    ]
                ]
            ]
        ],
        // 'view' => [
		// 	'class' => '\rmrevin\yii\minify\View',
		// 	'enableMinify' => !YII_DEBUG,
		// 	'concatCss' => true, // concatenate css
		// 	'minifyCss' => true, // minificate css
		// 	'concatJs' => true, // concatenate js
		// 	'minifyJs' => true, // minificate js
		// 	'minifyOutput' => true, // minificate result html page
		// 	'webPath' => '@web', // path alias to web base
		// 	'basePath' => '@webroot', // path alias to web base
		// 	'minifyPath' => '@webroot/minify', // path alias to save minify result
		// 	'jsPosition' => [ \yii\web\View::POS_END ], // positions of js files to be minified
		// 	'forceCharset' => 'UTF-8', // charset forcibly assign, otherwise will use all of the files found charset
		// 	'expandImports' => true, // whether to change @import on content
		// 	'compressOptions' => ['extra' => true], // options for compress
		// 	'excludeFiles' => [
        //     	'jquery.js', // exclude this file from minification
        //     	'app-[^.].js', // you may use regexp
        //     ],
        //     'excludeBundles' => [
        //     	\app\helloworld\AssetBundle::class, // exclude this bundle from minification
        //     ],
		// ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['103.250.139.67', '::1'],
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}
return $config;