<?php

namespace app\modules\api\controllers;

use app\models\PipeSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\data\ActiveDataProvider;
use mikehaertl\wkhtmlto\Pdf;

class SyncController extends ActiveController
{
	public $modelClass = "";
	public function actions()
	{
		$actions = parent::actions();
		unset($actions['delete'], $actions['view']);
		$actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
		return $actions;
	}
	public function prepareDataProvider()
	{
		$searchModel = new PipeSearch();
		return $searchModel->search(\Yii::$app->request->queryParams);
	}
	public function behaviors()
	{
		$behaviors = parent::behaviors();
		$behaviors['authenticator'] = [
			'class' => CompositeAuth::className(),
			'authMethods' => [
				QueryParamAuth::className(),
			],
		];
		return $behaviors;
	}
	public function checkAccess($action, $model = null, $params = [])
	{
		Yii::$app->permission->checkAccess();
	}
	public function actionSyncall()
	{
		$Timestamp = time();
		$tableArray =  [
			'pipe' => '\app\models\Pipe',
			'pipe_reception' => '\app\models\Reception',
			'admin_landowner' => '\app\models\Landowner',
			'admin_line_list' => '\app\models\Line',
			'pipe_bending' => '\app\models\Bending',
			'pipe_cleargrade' => '\app\models\Cleargrade',
			'pipe_cuting' => '\app\models\Cutting',
			'pipe_stringing' => '\app\models\Stringing',
			'pipe_transfer' => '\app\models\PipeTransfer',
			'taxonomy' => '\app\models\Taxonomy',
			'taxonomy_value' => '\app\models\TaxonomyValue',
			'taxonomy_value_value' => '\app\models\TaxonomyValueValue',
			'defact_picture' => '\app\models\Picture',
			'welding' => '\app\models\Welding',
			'welding_parameter_check' => '\app\models\Parameter',
			'welding_coating_production' => '\app\models\Production',
			'welding_coating_repair' => '\app\models\Coatingrepair',
			'welding_ndt' => '\app\models\Ndt',
			'welding_repair' => '\app\models\Weldingrepair',
			'civil_trenching' => '\app\models\Trenching',
			'civil_lowering' => '\app\models\Lowering',
			'civil_backfilling' => '\app\models\Backfilling',
			'civil_reinstatement' => '\app\models\Reinstatement',
			'civil_special_crossings' => '\app\models\SpecialCrossings',
			'com_cathodic_protection' => '\app\models\Cathodicprotection',
			'com_clean_gauge' => '\app\models\Cleangauge',
			'com_hydrotesting' => '\app\models\Hydrotesting',
			'com_surveying' => '\app\models\Surveying',
			'cabling_drum' => '\app\models\Cable',
			'cabling_stringing' => '\app\models\CabStringing',
			'cabling_splicing' => '\app\models\CabSplicing',
			'user' => '\app\models\Employee',
			'safety_slam' => '\app\models\SafetySlam',
			'hazard_report' => '\app\models\Hazard',
			'map_part_question' => '\app\models\MapPartQuestion',
			'map_part_vehicle_inspection' => '\app\models\MapPartVehicleInspection',
			'vehicle_inspection' => '\app\models\VehicleInspection',
			'vehicle_schedule' => '\app\models\VehicleSchedule',
		];
		$InputData    =  Yii::$app->getRequest()->getBodyParams();
		$project_id   =  !empty($InputData['project_id']) ? $InputData['project_id'] : 0;

		foreach ($tableArray as $table => $item) {
			if ($table == 'taxonomy' || $table == 'taxonomy_value' || $table == 'taxonomy_value_value' || $table == 'defact_picture' || $table == 'user' || $table == 'map_part_question' || $table == 'map_part_vehicle_inspection') {
				if ($table == 'taxonomy') {
					$create[$table] = $item::find()->select(['id', 'name', 'child_value'])->where(1)->all();
				} else {
					$create[$table] = $item::find()->where(1)->all();
				}
			} else {
				$create[$table] = $item::find()->where([ 'is_deleted' => 0, 'project_id' => $project_id])->active()->all();
			}
		}
		$Data['create'] = $create;
		$res = ['data' => $Data, 'last_sync' => $Timestamp];
		// print_r($res);die;
		return $res;
	}
	public function checkAnomaly($model)
	{
		$Class =  get_class($model);
		switch ($Class) {
			case "app\models\Pipe":
				$model = Yii::$app->anomaly->pipe_anomaly($model);
				break;
			case "app\models\Reception":
				$model = Yii::$app->anomaly->pipe_reception_anomaly($model);
				break;
			case "app\models\PipeTransfer":
				$model = Yii::$app->anomaly->pipe_transfer_anomaly($model);
				break;
			case "app\models\Stringing":
				$model = Yii::$app->anomaly->pipe_stringing_anomaly($model);
				break;
			case "app\models\Bending":
				$model = Yii::$app->anomaly->pipe_bending_anomaly($model);
				break;
			case "app\models\Cutting":
				$model = Yii::$app->anomaly->pipe_cutting_anomaly($model);
				break;
			case "app\models\Cleargrade":
				// $model = Yii::$app->anomaly->pipe_cleargrade_anomaly($model);
				break;
			case "app\models\Welding":
				$model = Yii::$app->anomaly->welding_anomaly($model);
				break;
			case "app\models\Parameter":
				// $model = Yii::$app->anomaly->welding_param_anomaly($model,"");
				break;
			case "app\models\Ndt":
				$model = Yii::$app->anomaly->welding_ndt_anomaly($model, "");
				break;
			case "app\models\Weldingrepair":
				$model = Yii::$app->anomaly->welding_repair_anomaly($model, "");
				break;
			case "app\models\Production":
				$model = Yii::$app->anomaly->welding_production_anomaly($model, "");
				break;
			case "app\models\Coatingrepair":
				// $model = Yii::$app->anomaly->welding_coatingrepair_anomaly($model,"");
				break;
			case "app\models\Trenching":
				// $model = Yii::$app->anomaly->civil_trenching_anomaly($model,"");
				break;
			case "app\models\Lowering":
				// $model = Yii::$app->anomaly->civil_lowering_anomaly($model,"");
				break;
			case "app\models\Backfilling":
				// $model = Yii::$app->anomaly->civil_backfilling_anomaly($model,"");
				break;
			case "app\models\Reinstatement":
				// $model = Yii::$app->anomaly->civil_reinstatement_anomaly($model,"");
				break;
			case "app\models\Cathodicprotection":
				$model = Yii::$app->anomaly->precom_cathodic_anomaly($model, "");
				break;
			case "app\models\Cleangauge":
				$model = Yii::$app->anomaly->precom_cleangauge_anomaly($model, "");
				break;
			case "app\models\Hydrotesting":
				$model = Yii::$app->anomaly->precom_hydrotesting_anomaly($model, "");
				break;
			case "app\models\Cable":
				$model = Yii::$app->anomaly->cable_anomaly($model, "");
				break;
			case "app\models\CabStringing":
				$model = Yii::$app->anomaly->cable_stringing_anomaly($model, "");
				break;
			case "app\models\CabSplicing":
				$model = Yii::$app->anomaly->cable_splicing_anomaly($model, "");
				break;
			default:
				$model = $model;
		}

		return $model;
	}
	protected function userUpdateProject($ProjectId)
	{

		$Employee = \app\models\Employee::find()->where(['id' => Yii::$app->user->identity->id])->one();
		if (!empty($ProjectId) && !empty($Employee)) {
			$Employee->project_id = $ProjectId;
			$Employee->save();
		}
	}

	protected function userUpdateLanguage($langCode)
	{
		$Employee = \app\models\Employee::find()->where(['id' => Yii::$app->user->identity->id])->one();
		if (!empty($langCode) && !empty($Employee)) {
			$Employee->lang = $langCode;
			$Employee->save();
		}
	}

	public function actionSyncFailed()
	{
		$InputData 		= Yii::$app->getRequest()->getBodyParams();

		$model = new \app\models\SyncFailed;
		$model->user_id 	= Yii::$app->user->identity->id;
		$model->project_id  = Yii::$app->user->identity->project_id;
		$model->request 	= !empty($InputData['request']) ? json_encode($InputData['request']) : "Request is not In Request.";
		$model->response 	= !empty($InputData['response']) ? json_encode($InputData['response']) : "response is not In Request.";
		$model->error 	=	  !empty($InputData['error']) ? json_encode($InputData['error']) : "error is not In Request.";
		$model->save(false);
		return ['status' => true];
	}
	public function actionSave()
	{
		$success_id  = [];
		$Data = [];

		//####################  tables #####################
		$tableArray  = [
			'taxonomy_value' => '\app\models\TaxonomyValue',
			'taxonomy_value_value' => '\app\models\TaxonomyValueValue',
			'admin_landowner' => '\app\models\Landowner',
			'admin_line_list' => '\app\models\Line',
			'pipe' => '\app\models\Pipe',
			'pipe_reception' => '\app\models\Reception',
			'pipe_cleargrade' => '\app\models\Cleargrade',
			'pipe_stringing' => '\app\models\Stringing',
			'pipe_cuting' => '\app\models\Cutting',
			'pipe_bending' => '\app\models\Bending',
			'pipe_transfer' => '\app\models\PipeTransfer',
			'defact_picture' => '\app\models\Picture',
			'welding' => '\app\models\Welding',
			'welding_parameter_check' => '\app\models\Parameter',
			'welding_coating_production' => '\app\models\Production',
			'welding_coating_repair' => '\app\models\Coatingrepair',
			'welding_ndt' => '\app\models\Ndt',
			'welding_repair' => '\app\models\Weldingrepair',
			'civil_trenching' => '\app\models\Trenching',
			'civil_lowering' => '\app\models\Lowering',
			'civil_backfilling' => '\app\models\Backfilling',
			'civil_reinstatement' => '\app\models\Reinstatement',
			'civil_special_crossings' => '\app\models\SpecialCrossings',
			'com_cathodic_protection' => '\app\models\Cathodicprotection',
			'com_clean_gauge' => '\app\models\Cleangauge',
			'com_hydrotesting' => '\app\models\Hydrotesting',
			'com_surveying' => '\app\models\Surveying',
			'cabling_drum' => '\app\models\Cable',
			'cabling_stringing' => '\app\models\CabStringing',
			'cabling_splicing' => '\app\models\CabSplicing',
			'safety_slam' => '\app\models\SafetySlam',
			'hazard_report' => '\app\models\Hazard',
			'map_part_question' => '\app\models\MapPartQuestion',
			'map_part_vehicle_inspection' => '\app\models\MapPartVehicleInspection',
			'vehicle_inspection' => '\app\models\VehicleInspection',
			'vehicle_schedule' => '\app\models\VehicleSchedule',
		];
		$InputData 		= Yii::$app->getRequest()->getBodyParams();
		$project_id     =  !empty($InputData['project_id']) ? $InputData['project_id'] : 0;
		$langCode     	=  !empty($InputData['lang_code']) ? $InputData['lang_code'] : '';

		if (!empty($langCode)) {
			$this->userUpdateLanguage($langCode);
		}

		if (!empty($InputData['timestamp'])) {
			//################### Created Data ###################		
			foreach ($tableArray as $table => $item) {
				if ($table == "taxonomy_value" || $table == "taxonomy_value_value" || $table == "defact_picture" || $table == 'map_part_question' || $table == 'map_part_vehicle_inspection') {
					$create[$table] = ArrayHelper::map($item::find()->select(['id'])->where(
						[
							'AND',
							['>', 'created_at', $InputData['timestamp']],
							['>', 'updated_at', $InputData['timestamp']]
						]
					)->asArray()->all(), 'id', 'id');
				} else {
					$create[$table] = ArrayHelper::map($item::find()->select(['id'])->where(
						[
							'AND',
							['>', 'created_at', $InputData['timestamp']],
							['>', 'updated_at', $InputData['timestamp']],
							['=', 'project_id', $project_id]
						]
					)->asArray()->all(), 'id', 'id');
				}
			}
			$Data['create'] = $create;
		}
		$Timestamp = time();
		//####################  Input Data #####################
		// this array is make to maintaine squence as per insertion order in db
		$tableSyncAry = [
			'admin_line_list', 'admin_landowner', 'pipe', 'pipe_reception', 'pipe_cleargrade', 'pipe_stringing', 'pipe_transfer',

			'pipe_bending', 'pipe_cuting', 'welding', 'welding_parameter_check', 'welding_ndt', 'welding_repair', 'welding_coating_production', 'welding_coating_repair', 'civil_trenching', 'civil_lowering', 'civil_backfilling', 'civil_reinstatement', 'civil_special_crossings', 'com_cathodic_protection', 'com_clean_gauge', 'com_hydrotesting', 'com_surveying', 'cabling_drum', 'cabling_stringing', 'cabling_splicing', 'defact_picture',

			'hazard_report', 'safety_slam', 'map_part_question', 'vehicle_inspection', 'vehicle_schedule', 'map_part_vehicle_inspection'
		];

		$defectDefaultArray = [
			'Reception' => 'pipe_reception',
			'Cleargrade' => 'pipe_cleargrade',
			'Stringing' => 'pipe_stringing',
			'PipeTransfer' => 'pipe_transfer',
			'Bending' => 'pipe_bending',
			'Cutting' => 'pipe_cuting',
			'Welding' => 'welding',
			'Parameter' => 'welding_parameter_check',
			'Ndt' => 'welding_ndt',
			'Weldingrepair' => 'welding_repair',
			'Production' => 'welding_coating_production',
			'Coatingrepair' => 'welding_coating_repair',
			'Trenching' => 'civil_trenching',
			'Lowering' => 'civil_lowering',
			'Backfilling' => 'civil_backfilling',
			'Reinstatement' => 'civil_reinstatement',
			'SpecialCrossings' => 'civil_special_crossings',
			'Cathodicprotection' => 'com_cathodic_protection',
			'Cleangauge' => 'com_clean_gauge',
			'Hydrotesting' => 'com_hydrotesting',
			'Surveying' => 'com_surveying',
			'Cable' => 'cabling_drum',
			'CabStringing' => 'cabling_stringing',
			'CabSplicing' => 'cabling_splicing',
			'Line' => 'admin_line_list',
			'Landowner' => 'admin_landowner',
		];
		$defectIds = [];
		$updateIds = [];
		if (!empty($InputData['data'])) {

			foreach ($tableSyncAry as $table) {
				$TableIds = [];
				if (!isset($InputData['data'][$table])) continue;
				$rows = $InputData['data'][$table];

				foreach ($rows as $key => $row) {
					if (!empty($row['project_id'])) {
						$this->userUpdateProject($row['project_id']);
					}
					$model	= !empty($row["action_update"]) ? $tableArray[$table]::find()->where(['id' => $row['id']])->one() : new  $tableArray[$table];
					if ($table == "defact_picture") {
						if (!empty($model->id)) {
							$model->is_deleted = 1;
							$model->updated_at = $Timestamp;
							$model->save();
							if (file_exists(Yii::$app->basePath . '/web/images/' . $model->image)) {
								!unlink(Yii::$app->basePath . '/web/images/' . $model->image);
							}
						} else {

							if (!empty($defectDefaultArray[$row['section_type']]) && !empty($success_id[$defectDefaultArray[$row['section_type']]])) {
								foreach ($success_id[$defectDefaultArray[$row['section_type']]] as $ele) {
									if ($ele['oldId'] == $row['section_id']) {
										$row['section_id'] = $ele['newId'];
									}
								}
							}
							$model = $this->ImageUpload($row, $Timestamp);
							if (!empty($model)) {
								array_push($TableIds, array('newId' => $model->id, 'oldId' => $row['id']));
								array_push($defectIds, $model->id);
							}
						}
					} else {
						$updateIds[$table] = [];
						if (!empty($model)) {
							foreach ($row as $col => $val) {
								if ($col != "id" && $col != "action_update" && $col != "is_anomally" && $col != "why_anomally" && $col != "is_active") {
									$model->$col = $val;
								}
							}
							$model = $this->checkAnomaly($model);

							if (!empty($row["action_update"])) {
								$model->updated_at = $Timestamp;
							} else {
								$model->created_at = $Timestamp;
							}

							if ($model->save() && empty($row["action_update"])) {
								//for updation of map vehicle questions
								// if($table == "vehicle_inspection"){
								// 	$getMapVehiData = \app\models\MapPartVehicleInspection::find()->where(['inspection_id' => $row['id']])->all();
								// 	if(!empty($getMapVehiData)){
								// 		foreach($getMapVehiData as $mapVehicle){
								// 			$mapVehicle->inspection_id = $model->id;
								// 			$mapVehicle->save(false);
								// 		}
								// 	}
								// }
								array_push($TableIds, array('newId' => $model->id, 'oldId' => $row['id']));
								//ankit logic
								array_push($updateIds[$table], $model->id);
							} else if (!empty($model->errors)) {

								$modelSyncFailed = new \app\models\SyncFailed;
								$modelSyncFailed->user_id 	= Yii::$app->user->identity->id;
								$modelSyncFailed->project_id  = Yii::$app->user->identity->project_id;
								$modelSyncFailed->request 	= !empty($InputData) ? json_encode($InputData) : "Request is not In Request.";
								$modelSyncFailed->response 	= json_encode($model->errors);
								$modelSyncFailed->error 		= "error in saving record.";
								$modelSyncFailed->save(false);
							}
						}
					}
				}
				$success_id[$table] = $TableIds;
			}
			foreach ($success_id as $tbl => $datas) {
				if ($tbl ==  "welding") {
					foreach ($datas as $v) {
						$oldId = $v['oldId'];
						$newId = $v['newId'];
						$Weld = \app\models\Welding::find()->where(['id' => $newId])->one();
						if ($Weld) {
							//#### update NDT #######
							\app\models\Ndt::updateAll(
								[
									'main_weld_id' => $newId,
								],
								[
									'main_weld_id' => $oldId,
									'kp' => $Weld->kp,
									'weld_number' => $Weld->weld_number,
									'project_id' => $Weld->project_id
								]
							);
							//#### update Repair #######
							\app\models\Weldingrepair::updateAll(
								[
									'main_weld_id' => $newId,
								],
								[
									'main_weld_id' => $oldId,
									'kp' => $Weld->kp,
									'weld_number' => $Weld->weld_number,
									'project_id' => $Weld->project_id
								]
							);
						}
					}
				}
			}
		}

		if (!empty($InputData['data'])) {
			//################### Udpdated Data ###################
			foreach ($tableArray as $table => $item) {
				if ($table == "defact_picture") {
					$update[$table] = $item::find()->where(
						[
							'OR',
							[
								'AND',
								['<=', 'created_at', $InputData['timestamp']],
								['>=', 'updated_at', $InputData['timestamp']]
							],
							[
								'IN',
								'id',
								$defectIds
							]

						]
					)->asArray()->all();
				} else {
					if ($table == "taxonomy_value" || $table == "taxonomy_value_value" || $table == 'map_part_question') {
						$update[$table] = $item::find()->where(['AND', ['<=', 'created_at', $InputData['timestamp']], ['>=', 'updated_at', $InputData['timestamp']]])->asArray()->all();
					} else if ($table == 'map_part_vehicle_inspection') {
						$newMapVehicleIds = [];
						if (!empty($success_id[$table])) {
							foreach ($success_id[$table] as $key => $val) {
								$newMapVehicleIds[] = $val['newId'];
							}
						}
						if (!empty($newMapVehicleIds)) {
							$update[$table] = $item::find()->where([
								'OR',
								[
									'AND',
									['<=', 'created_at', $InputData['timestamp']],
									['>=', 'updated_at', $InputData['timestamp']],
								],
								[
									'IN',
									'id',
									$newMapVehicleIds
								]
							])->asArray()->all();
						} else {
							$update[$table] = $item::find()->where([
								'AND',
								['<=', 'created_at', $InputData['timestamp']],
								['>=', 'updated_at', $InputData['timestamp']],
							])->asArray()->all();
						}
					} else {
						//ankit logic
						if (isset($updateIds[$table])) {
							$update[$table] = $item::find()->where([
								'OR',
								[
									'AND',
									['<=', 'created_at', $InputData['timestamp']],
									['>=', 'updated_at', $InputData['timestamp']],
									['=', 'project_id', $project_id]
								],
								[
									'IN',
									'id',
									$updateIds[$table]
								]
							])->asArray()->all();
						} else {
							$update[$table] = $item::find()->where([
								'AND',
								['<=', 'created_at', $InputData['timestamp']],
								['>=', 'updated_at', $InputData['timestamp']],
								['=', 'project_id', $project_id]
							])->asArray()->all();
						}
					}
				}
			}

			$Data['update'] = $update;
		}
		if (!empty($Data['create'])) {
			//################### Created Data ###################		
			foreach ($Data['create'] as $table => $item) {
				if ($table == "taxonomy_value" || $table == "taxonomy_value_value" || $table == "defact_picture" || $table == 'map_part_question' || $table == 'map_part_vehicle_inspection') {
					$Data['create'][$table] = $tableArray[$table]::find()->where(['IN', 'id', $item])->asArray()->all();
				} else {
					$Data['create'][$table] = $tableArray[$table]::find()->where(['AND', ['IN', 'id', $item], ['=', 'project_id', $project_id]])->asArray()->all();
				}
			}
		}

		$deleteAry = [];
		$deletedRows = \app\models\DeletedItem::find()->where(['>=', 'updated_at', $InputData['timestamp']])->asArray()->all();
		if (!empty($deletedRows)) {
			foreach ($deletedRows as $key => $value) {
				if (!isset($deleteAry[$value['table_name']])) {
					$deleteAry[$value['table_name']] = [];
				}
				$deleteAry[$value['table_name']][] = $value['table_id'];
			}
		}
		$Data['delete'] = $deleteAry;
		$res = ['ids' => $success_id, 'data' => $Data, 'last_sync' => $Timestamp];
		return $res;
	}
	public function ImageUpload($row, $Timestamp)
	{
		$ImgModel =  array();
		if (!empty($row['local_path'])) {

			$baseImage   = $row['local_path'];
			$sectionType = $row['section_type'];
			$sectionId   = $row['section_id'];

			$imgdata 	 = base64_decode($baseImage);

			$f 			 = finfo_open();
			$mimeType    = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
			$ext		 = explode('/', $mimeType);

			$x 			 = !empty($ext[1]) ? $ext[1] : 'jpeg';
			$filename    = time() . '_' . rand(0000, 9999) . '.' . $x;
			$path = \Yii::$app->basePath . '/web/images/' . $filename;
			$saveFile = $this->base64ToJpeg($baseImage, $path);

			if ($saveFile != "") {
				$model 				 = new \app\models\Picture();
				$model->section_id   = $sectionId;
				$model->image 		 = $filename;
				$model->mime_type 	 = $mimeType;
				$model->section_type = $sectionType;
				$model->updated_at   = $Timestamp;
				$model->created_at   = $Timestamp;
				$model->save(false);
			}

			return $model;
		}
		return false;
	}

	public function base64ToJpeg($base64_string, $output_file)
	{
		// open the output file for writing
		$ifp = fopen($output_file, 'wb');
		fwrite($ifp, base64_decode($base64_string));
		// clean up the file resource
		fclose($ifp);
		return $output_file;
	}

	public function getPdf($url, $pType)
	{
		$pdf = new Pdf([
			'commandOptions' => [
				'useExec' => true,
				'escapeArgs' => false,
				'procOptions' => array(
					// This will bypass the cmd.exe which seems to be recommended on Windows
					'bypass_shell' => true,
					// Also worth a try if you get unexplainable errors
					'suppress_errors' => true,
				),
			],
		]);
		$globalOptions = array(
			'no-outline', // Make Chrome not complain
			// Default page options
			'page-size' => 'A4',
			'javascript-delay' => 5000,
			'user-style-sheet' => Yii::getAlias('@webroot') . '/css/pdf.css'
		);

		$url = str_replace(Yii::$app->request->hostInfo, 'http://127.0.0.1', $url);
		// $pdf->addPage($url);

		$pdf->setOptions($globalOptions);
		$pdf->addPage($url);

		$pdf->binary = '/usr/bin/wkhtmltopdf';
		// $pdf->binary = 'C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe';

		$filename = time() . '_' . $pType . '.pdf';
		if (!$pdf->saveAs(Yii::getAlias('@webroot') . '/pdf/' . $filename)) {
			echo 'Could not create PDF: ' . $pdf->getError();
			die;
			//throw new Exception('Could not create PDF: '.$pdf->getError());
		} else {
			return Yii::$app->request->hostInfo . Yii::getAlias('@web') . '/pdf/' . $filename;
		}
	}

	//Heat Report
	public function actionHeatReport()
	{
		$queryParam = [
			'model' => 'PipeSearch',
			'download' => 1,
			'weldBook' => 0,
			'app' => true
		];
		$searchModel = '\\app\models\\PipeSearch';
		$searchModel = new $searchModel;
		$file = $searchModel->search($queryParam);
		$exp = explode('/', $file);
		$filename = end($exp);
		$filePath = \Yii::getAlias('@webroot') . '/excel/' . $filename;

		$mime = mime_content_type($filePath);
		return array('url' => $file, 'mime' => $mime);
	}

	//Visual Progress Report
	public function actionVisual()
	{
		$html = $this->render('@app/modules/report/views/report/Visualprograss');
		echo $html;
		die;
	}

	//Open End Summary Report
	public function actionOpenEndSummary()
	{
		$pipes = \app\models\Welding::find()->select(['next_pipe', 'pipe_number'])->active()->asArray()->all();
		$pipeNumber = ArrayHelper::map($pipes, 'pipe_number', 'pipe_number');
		$nextPipe = ArrayHelper::map($pipes, 'next_pipe', 'next_pipe');

		$openEndPipeList = array_merge(array_diff($pipeNumber, $nextPipe), array_diff($nextPipe, $pipeNumber));

		$query = \app\models\Welding::find()->where(['OR', ['IN', 'pipe_number', $openEndPipeList], ['IN', 'next_pipe', $openEndPipeList]])->active();
		$file = Yii::$app->general->globalDownload($query, true);

		$exp = explode('/', $file);
		$filename = end($exp);
		$filePath = \Yii::getAlias('@webroot') . '/excel/' . $filename;

		$mime = mime_content_type($filePath);
		return array('url' => $file, 'mime' => $mime);
	}

	//Weld Book Report
	public function actionWeldBook()
	{
		$queryParam = [
			'model' => 'WeldingSearch',
			'download' => 1,
			'weldBook' => 1,
			'app' => true
		];
		$searchModel = '\\app\models\\WeldingSearch';
		$searchModel = new $searchModel;
		$file = $searchModel->search($queryParam);

		$exp = explode('/', $file);
		$filename = end($exp);
		$filePath = \Yii::getAlias('@webroot') . '/excel/' . $filename;

		$mime = mime_content_type($filePath);
		return array('url' => $file, 'mime' => $mime);
	}

	public function actionProduction()
	{
		$filterType = 'all';
		$dateRange = '';
		$html = '';
		if (!empty($_GET['filterType'])) {
			$filterType = $_GET['filterType'];
			if ($filterType == 'weekly') {
				$dateRange = !empty($_GET['weekRange']) ? $_GET['weekRange'] : "";
				$html = $this->render('@app/modules/report/views/report/production/_weeklyproductiondata', ['filterType' => $filterType, 'dateRange' => $dateRange]);
			} else if ($filterType == 'daily') {
				$dateRange = !empty($_GET['dailyRange']) ? $_GET['dailyRange'] : "";
				$html = $this->render('@app/modules/report/views/report/production/_dailyproductiondata', ['filterType' => $filterType, 'dateRange' => $dateRange]);
			} else if ($filterType == 'all') {
				$html = $this->render('@app/modules/report/views/report/production/_overallproductiondata', ['filterType' => $filterType, 'dateRange' => $dateRange]);
			}
		}
		echo $html;
	}

	//Overall Production Report
	public function actionOverallProduction()
	{
		$url = $this->getPdf(Url::toRoute(['/api/sync/production', 'access-token' => $_GET['access-token'], 'filterType' => 'all'], 'http'), 'Overlall_Production');

		$exp = explode('/', $url);
		$filename = end($exp);
		$filePath = \Yii::getAlias('@webroot') . '/pdf/' . $filename;

		$mime = mime_content_type($filePath);
		return array('url' => $url, 'mime' => $mime);
		die;
	}

	//Weekly Production Report
	public function actionWeeklyProduction()
	{
		$url = $this->getPdf(urldecode(Url::toRoute(['/api/sync/production', 'access-token' => $_GET['access-token'], 'filterType' => 'weekly', 'weekRange' => $_GET['weekRange']], 'http')), 'Weekly_Production');

		$exp = explode('/', $url);
		$filename = end($exp);
		$filePath = \Yii::getAlias('@webroot') . '/pdf/' . $filename;

		$mime = mime_content_type($filePath);
		return array('url' => $url, 'mime' => $mime);
		die;
	}

	//Daily Production Report
	public function actionDailyProduction()
	{
		$url = $this->getPdf(urldecode(Url::toRoute(['/api/sync/production', 'access-token' => $_GET['access-token'], 'filterType' => 'daily', 'dailyRange' => $_GET['dailyRange']], 'http')), 'Daily_Production');

		$exp = explode('/', $url);
		$filename = end($exp);
		$filePath = \Yii::getAlias('@webroot') . '/pdf/' . $filename;

		$mime = mime_content_type($filePath);
		return array('url' => $url, 'mime' => $mime);
		die;
	}

	public function actionWelderAnalysis()
	{
		if (!empty($_GET['filterType'])) {
			$type = $_GET['filterType'];
			if ($type == "all") {
				$url = $this->getPdf(urldecode(Url::toRoute(['/api/sync/overall-analysis', 'access-token' => $_GET['access-token']], 'http')), 'Overall_Welder_Analysis');

				$exp = explode('/', $url);
				$filename = end($exp);
				$filePath = \Yii::getAlias('@webroot') . '/pdf/' . $filename;

				$mime = mime_content_type($filePath);
				return array('url' => $url, 'mime' => $mime);
			} else if ($type == "combine") {
				$date = !empty($_GET['date']) ? $_GET['date'] : date('Y-m-d');
				$url = $this->getPdf(urldecode(Url::toRoute(['/api/sync/combine-analysis', 'access-token' => $_GET['access-token'], 'date' => $date], 'http')), 'Combine_Welder_Analysis');

				$exp = explode('/', $url);
				$filename = end($exp);
				$filePath = \Yii::getAlias('@webroot') . '/pdf/' . $filename;

				$mime = mime_content_type($filePath);
				return array('url' => $url, 'mime' => $mime);
			} else if ($type == "individual") {
				$date = !empty($_GET['date']) ? $_GET['date'] : date('Y-m-d');
				$welderName = !empty($_GET['welderName']) ? $_GET['welderName'] : '';
				$url = $this->getPdf(urldecode(Url::toRoute(['/api/sync/individual-analysis', 'access-token' => $_GET['access-token'], 'date' => $date, 'welderName' => $welderName], 'http')), 'Individual_Welder_Analysis');

				$exp = explode('/', $url);
				$filename = end($exp);
				$filePath = \Yii::getAlias('@webroot') . '/pdf/' . $filename;

				$mime = mime_content_type($filePath);
				return array('url' => $url, 'mime' => $mime);
			}
			die;
		}
	}

	public function actionOverallAnalysis()
	{
		$WeldData    = array('defect_position' => array('Root OS', 'Root TS', 'Hot OS', 'Hot TS', 'Fill OS', 'Fill TS', 'Cap OS', 'Cap TS'));
		// $welderList  = ArrayHelper::map(\app\models\TaxonomyValue::find()->where(['taxonomy_id'=>7,'project_id'=>Yii::$app->user->identity->project_id])->asArray()->all(),'value','value');
		$welderList  = Yii::$app->general->TaxonomyDrop(7);
		//$ndtDefectList = ArrayHelper::map(\app\models\TaxonomyValue::find()->where(['taxonomy_id'=>9,'project_id'=>Yii::$app->user->identity->project_id])->asArray()->all(),'id','value');       
		$ndtDefectList = array_values(Yii::$app->general->TaxonomyDrop(9, true));

		if (!empty($welderList)) {
			$mainLineWelder = array();
			$tieLineWelder =  array();
			foreach ($welderList as $welder) {
				$weldingMain =  \app\models\Weldingrepair::find()
					->innerJoin('welding', 'welding.weld_number = welding_repair.weld_number AND welding.kp = welding_repair.kp 
                    AND welding.project_id  = welding_repair.project_id AND welding_repair.is_active = welding.is_active
                    AND welding_repair.is_deleted = welding.is_deleted')
					->where(['welding_repair.welder' => $welder, 'welding.line_type' => 'Main Line'])->active()->count();

				$WeldData['mainline']['name'][]  = $welder;
				$WeldData['mainline']['count'][] = (float)$weldingMain;

				$weldingTie =  \app\models\Welding::find()->where([
					'AND',
					[
						'OR',
						['root_os' => $welder],
						['root_ts' => $welder],
						['hot_os' => $welder],
						['hot_ts' => $welder],
						['fill_os' => $welder],
						['fill_ts' => $welder],
						['cap_os' => $welder],
						['cap_ts' => $welder],
					],
					['line_type' => 'Tie Line']
				])->active()->count();

				$weldingRepairTie = \app\models\Weldingrepair::find()
					->innerJoin('welding', 'welding.weld_number = welding_repair.weld_number AND welding.kp = welding_repair.kp 
                AND welding.project_id  = welding_repair.project_id AND welding_repair.is_active = welding.is_active
                AND welding_repair.is_deleted = welding.is_deleted')
					->where(['welding_repair.welder' => $welder, 'welding.line_type' => 'Tie Line'])->active()->count();

				$WeldData['tieline']['name'][]  = '(Repair = ' . $weldingRepairTie . ')/(Weld =' . $weldingTie . ') | ' . $welder;
				$WeldData['tieline']['count'][] = $weldingRepairTie;
				$WeldData['tieline']['rate'][]  = !empty($weldingTie) ? ($weldingRepairTie / $weldingTie) * 100 : 0;
			}
		}

		/******************** Main Line *************************/
		$weldingDataListMainLine = \app\models\Welding::find()->where(['line_type' => 'Main Line'])->active()->asArray()->all();
		$fMainArray = array();
		$fTieArray = array();
		$defectPosArray = array();
		if (!empty($weldingDataListMainLine)) {
			$CountOfDefectMain = array();
			foreach ($weldingDataListMainLine as $wData) {
				$defactsData = !empty($wData['ndt_defects']) ? json_decode($wData['ndt_defects'], true) : array();
				if (!empty($defactsData)) {
					foreach ($defactsData as $defect) {
						foreach ($WeldData['defect_position'] as $position) {
							if ($position == $defect['defect_position']) {
								$CountOfDefectMain[$position][] = $defect['defects'];
							} else {
								$CountOfDefectMain[$position][] = 0;
							}
						}
					}
				}
			}

			if (!empty($CountOfDefectMain)) {
				foreach ($CountOfDefectMain as $key => $countDef) {
					$fMainArray[$key] = array_count_values($countDef);
				}
			}

			$finalMainArray = array();
			if (!empty($ndtDefectList)) {
				foreach ($ndtDefectList as $ndtDef) {
					$finalMainArray = array();
					foreach ($fMainArray as $fMain) {
						if (array_key_exists($ndtDef, $fMain)) {
							$finalMainArray[] = $fMain[$ndtDef];
						} else {
							$finalMainArray[] = 0;
						}
					}
					$defectPosArray[] = array(
						'name' => $ndtDef,
						'data' => $finalMainArray,
					);
				}
			}
		} else {
			if (!empty($ndtDefectList)) {
				foreach ($ndtDefectList as $ndtDef) {
					$emptyTieArray = array();
					for ($i = 0; $i < count($WeldData['defect_position']); $i++) {
						$emptyTieArray[] = 0;
					}
					$defectPosArray[] = array(
						'name' => $ndtDef,
						'data' => $emptyTieArray,
					);
				}
			}
		}

		/******************** Tie Line **************************/
		$weldingDataListTieLine = \app\models\Welding::find()->where(['line_type' => 'Tie Line'])->active()->asArray()->all();
		$fTieArray = array();
		$fTieArray = array();
		$defectPosArrayTie = array();
		if (!empty($weldingDataListTieLine)) {
			$CountOfDefectTie = array();
			foreach ($weldingDataListTieLine as $wData) {
				$defactsData = !empty($wData['ndt_defects']) ? json_decode($wData['ndt_defects'], true) : array();
				if (!empty($defactsData)) {
					foreach ($defactsData as $defect) {
						foreach ($WeldData['defect_position'] as $position) {
							if ($position == $defect['defect_position']) {
								$CountOfDefectTie[$position][] = $defect['defects'];
							} else {
								$CountOfDefectTie[$position][] = 0;
							}
						}
					}
				}
			}

			if (!empty($CountOfDefectTie)) {
				foreach ($CountOfDefectTie as $key => $countDef) {
					$fTieArray[$key] = array_count_values($countDef);
				}
			}

			$finalTieArray = array();
			if (!empty($ndtDefectList)) {
				foreach ($ndtDefectList as $ndtDef) {
					$finalTieArray = array();
					foreach ($fTieArray as $fTie) {
						if (array_key_exists($ndtDef, $fTie)) {
							$finalTieArray[] = $fTie[$ndtDef];
						} else {
							$finalTieArray[] = 0;
						}
					}
					$defectPosArrayTie[] = array(
						'name' => $ndtDef,
						'data' => $finalTieArray,
					);
				}
			}
		} else {
			if (!empty($ndtDefectList)) {
				foreach ($ndtDefectList as $ndtDef) {
					$emptyTieArray = array();
					for ($i = 0; $i < count($WeldData['defect_position']); $i++) {
						$emptyTieArray[] = 0;
					}
					$defectPosArrayTie[] = array(
						'name' => $ndtDef,
						'data' => $emptyTieArray,
					);
				}
			}
		}

		/*******************************************************/
		$html = $this->render('@app/modules/report/views/report/welder-overall', [
			'data' => $WeldData,
			'defectPosArray' => $defectPosArray,
			'defectPosArrayTie' => $defectPosArrayTie
		]);
		echo $html;
		die;
	}

	public function actionCombineAnalysis()
	{
		$date = empty($_GET['date']) ? date('Y-m-d') : $_GET['date'];
		$welderData = array();
		$WelderList = ArrayHelper::map(\app\models\TaxonomyValue::find()->where(['taxonomy_id' => 7])->asArray()->all(), 'value', 'value');
		if (!empty($WelderList)) {
			foreach ($WelderList as $welder_name) {
				//####################### Welding ####################################
				$TotalWeldCount = 0;
				$TotalWeldLength = 0;
				$Welding =  \app\models\Welding::find()->select([
					'welding.root_os',
					'welding.root_ts',
					'welding.hot_os',
					'welding.hot_ts',
					'welding.fill_os',
					'welding.fill_ts',
					'welding.cap_os',
					'welding.cap_ts',
					'pipe.od'
				])->leftJoin('pipe', 'welding.pipe_number=pipe.pipe_number 
				AND welding.project_id = pipe.project_id 
				AND welding.is_active = pipe.is_active')->where([
					'OR',
					['root_os' => $welder_name],
					['root_ts' => $welder_name],
					['hot_os' => $welder_name],
					['hot_ts' => $welder_name],
					['fill_os' => $welder_name],
					['fill_ts' => $welder_name],
					['cap_os' => $welder_name],
					['cap_ts' => $welder_name],
				])->andWhere(['between', 'welding.date', date('Y-m-d', strtotime("-1 year", strtotime($date))), $date])->active()->asArray()->all();

				if (!empty($Welding[0])) {
					foreach ($Welding as $Weld) {
						$WelderCount = 0;
						foreach ($Weld as $key => $v) {
							if ($v == $welder_name) {
								$WelderCount    =  $WelderCount + 1;
								$TotalWeldCount =  $TotalWeldCount + 1;
							}
						}
						$TotalWeldLength = $TotalWeldLength + $WelderCount * 3.14 * $Weld['od'];
					}
				}


				//####################### Welding Repair ####################################
				$WeldRepairData = \app\models\Weldingrepair::find()->select(['welding_repair.welder', 'pipe.od'])
					->leftJoin('welding', 'welding_repair.weld_number = welding.weld_number AND welding.project_id = welding_repair.project_id AND welding.is_active = welding_repair.is_active')
					->leftJoin('pipe', 'welding.pipe_number = pipe.pipe_number AND welding.project_id = pipe.project_id AND welding.is_active = pipe.is_active')
					->where(['welding_repair.welder' => $welder_name])
					->andWhere(['between', 'welding_repair.date', date('Y-m-d', strtotime("-1 year", strtotime($date))), $date])->active()->asArray()->all();

				$TotalWeldRepair = 0;
				$RepairLength = 0;
				if (!empty($WeldRepairData[0])) {
					foreach ($WeldRepairData as $repair) {
						$TotalWeldRepair = $TotalWeldRepair + 1;
						$RepairLength = $RepairLength + (3.14 * $repair['od']);
					}
				}
				array_push(
					$welderData,
					array(
						'WelderName' => $welder_name,
						'TotalWeldCount' => $TotalWeldCount,
						'WeldingLength' => $TotalWeldLength,
						'TotalWeldRepair' => $TotalWeldRepair,
						'RepairLength' => $RepairLength,
						'RepairRate' => !empty($TotalWeldCount) ? ($TotalWeldRepair / $TotalWeldCount) * 100 : 0
					)
				);
			}
			$html = $this->render('@app/modules/report/views/report/_weldercombinetable', ['data' => $welderData, 'date' => $date]);
			echo $html;
			die;
		}
	}

	public function actionIndividualAnalysis()
	{
		$date = !empty($_GET['date']) ? $_GET['date'] : date('Y-m-d');
		$welder_name = !empty($_GET['welderName']) ? $_GET['welderName'] : "";

		$data = \app\modules\report\controllers\ReportController::getWelderdata($welder_name, $date);

		$defectPosition = [
			'Root OS' => 'Root OS',
			'Root TS' => 'Root TS',
			'Hot OS' => 'Hot OS',
			'Hot TS' => 'Hot TS',
			'Fill OS' => 'Fill OS',
			'Fill TS' => 'Fill TS',
			'Cap OS' => 'Cap OS',
			'Cap TS' => 'Cap TS',
		];

		foreach ($defectPosition as $ele) {
			$data['NdtData'][] = (float) \app\models\Ndt::find()->where([
				'AND',
				['IN', 'weld_number', $data['WeldNumbers'], ['defect_position' => $ele]]
			])->active()->count();
		}

		// $ndtDefectList = ArrayHelper::map(\app\models\TaxonomyValue::find()->where(['taxonomy_id'=>9,'project_id'=>Yii::$app->user->identity->project_id])->asArray()->all(),'id','value');     

		$ndtDefectList = array_values(Yii::$app->general->TaxonomyDrop(9, true));

		$weldingDataMain =  \app\models\Welding::find()->where([
			'OR',
			['welding.root_os' => $welder_name],
			['welding.root_ts' => $welder_name],
			['welding.hot_os' => $welder_name],
			['welding.hot_ts' => $welder_name],
			['welding.fill_os' => $welder_name],
			['welding.fill_ts' => $welder_name],
			['welding.cap_os' => $welder_name],
			['welding.cap_ts' => $welder_name],
		])->active()->asArray()->all();

		$fMainArray = array();
		$defectPosArray = array();

		if (!empty($weldingDataMain)) {
			$CountOfDefectMain = array();
			foreach ($weldingDataMain as $wData) {
				$defactsData = !empty($wData['ndt_defects']) ? json_decode($wData['ndt_defects'], true) : array();
				if (!empty($defactsData)) {
					foreach ($defactsData as $defect) {
						foreach ($defectPosition as $position) {
							$currentPos = str_replace(' ', '_', strtolower($defect['defect_position']));
							if ($position == $defect['defect_position'] && $wData[$currentPos] == $welder_name) {
								$CountOfDefectMain[$position][] = $defect['defects'];
							} else {
								$CountOfDefectMain[$position][] = 0;
							}
						}
					}
				}
			}

			if (!empty($CountOfDefectMain)) {
				foreach ($CountOfDefectMain as $key => $countDef) {
					$fMainArray[$key] = array_count_values($countDef);
				}
			}

			$finalMainArray = array();
			if (!empty($ndtDefectList)) {
				foreach ($ndtDefectList as $ndtDef) {
					$finalMainArray = array();
					foreach ($fMainArray as $fMain) {
						if (array_key_exists($ndtDef, $fMain)) {
							$finalMainArray[] = $fMain[$ndtDef];
						} else {
							$finalMainArray[] = 0;
						}
					}
					$defectPosArray[] = array(
						'name' => $ndtDef,
						'data' => $finalMainArray,
					);
				}
			}
		} else {
			if (!empty($ndtDefectList)) {
				foreach ($ndtDefectList as $ndtDef) {
					$emptyTieArray = array();
					for ($i = 0; $i < count($defectPosition); $i++) {
						$emptyTieArray[] = 0;
					}
					$defectPosArray[] = array(
						'name' => $ndtDef,
						'data' => $emptyTieArray,
					);
				}
			}
		}

		$html = $this->render('@app/modules/report/views/report/welderDetail', [
			'data' => $data,
			'defectPosArray' => $defectPosArray,
			'defectPosition' => array('Root OS', 'Root TS', 'Hot OS', 'Hot TS', 'Fill OS', 'Fill TS', 'Cap OS', 'Cap TS')
		]);

		echo $html;
		die;
	}

	//KP to Weld List
	public function actionGetWeldNumber($kp)
	{
		$Kplist = \yii\helpers\ArrayHelper::map(\app\models\Welding::find()->select('weld_number')->where(['kp' => $kp])->active()->asArray()->all(), 'weld_number', 'weld_number');
		return $Kplist;
		die;
	}

	//Clearance Report
	public function actionClearance()
	{
		if (isset($_GET['first']) && $_GET['first']) {
			$Kplist = ArrayHelper::map(\app\models\Welding::find()->select('kp')->active()->groupBy('kp')->asArray()->all(), 'kp', 'kp');
			if (!empty($Kplist)) {
				return $Kplist;
			} else {
				return array();
			}
			die;
		}
		$url = $this->getPdf(urldecode(Url::toRoute(['/api/sync/get-clearance', 'access-token' => $_GET['access-token'], 'from_kp' => $_GET['from_kp'], 'from_weld' => $_GET['from_weld'], 'to_kp' => $_GET['to_kp'], 'to_weld' => $_GET['to_weld']], 'http')), 'Clearance');

		$exp = explode('/', $url);
		$filename = end($exp);
		$filePath = \Yii::getAlias('@webroot') . '/pdf/' . $filename;

		$mime = mime_content_type($filePath);
		return array('url' => $url, 'mime' => $mime);
		die;
	}

	public function actionGetClearance()
	{
		$from_kp = $_GET['from_kp'];
		$from_weld = $_GET['from_weld'];
		$to_kp = $_GET['to_kp'];
		$to_weld = $_GET['to_weld'];
		$data = array();

		$StartWelding = \app\models\Welding::find()->where(['kp' => $from_kp, 'weld_number' => $from_weld])->active()->asArray()->one();
		$data['startPipe'] = $StartWelding['pipe_number'];
		$data['startKp'] = $StartWelding['kp'];
		$data['startWeld'] = $StartWelding['weld_number'];

		$EndWelding = \app\models\Welding::find()->where(['kp' => $to_kp, 'weld_number' => $to_weld])->active()->asArray()->one();
		$data['endPipe'] = $EndWelding['next_pipe'];
		$data['endKp'] = $EndWelding['kp'];
		$data['endWeld'] = $EndWelding['weld_number'];

		//####################### Weld Data Received #####################
		$data['weldCheck'] = "No";
		$Prev = \app\models\Welding::find()->where(['pipe_number' => $StartWelding['pipe_number']])->active()->asArray()->one();

		if (!empty($Prev)) {
			$Next = \app\models\Welding::find()->where(['next_pipe' => $EndWelding['pipe_number']])->active()->asArray()->one();
			if (!empty($Next)) {
				$data['weldCheck'] = "Yes";
			}
		}

		//########################### Weld Data ######################
		$Welding = \app\models\Welding::find()->select(['weld_number', 'kp'])->where([
			'AND',
			['>=', 'kp', $from_kp],
			['<=', 'kp', $to_kp],
		])->active()->asArray()->all();

		$WeldingRecord = array();
		if (!empty($Welding)) {
			$start = 0;
			$end = 0;
			foreach ($Welding as $ele) {
				if ($ele['kp'] == $from_kp && $ele['weld_number'] == $from_weld) {
					$start = 1;
				}

				if ($start == 1 && $end == 0) {
					array_push($WeldingRecord, $ele);
				}
				if ($ele['kp'] == $to_kp && $ele['weld_number'] == $to_weld) {
					$end = 1;
				}
			}
		}
		//########################### Weld Ndt Received ######################
		$data['ndtCheck'] = "No";
		$NdtData = array();
		if (!empty($WeldingRecord)) {
			foreach ($WeldingRecord as $e) {
				$data['ndtCheck'] = "Yes";
				$Ndt = \app\models\Ndt::find()->where(['kp' => $e['kp'], 'weld_number' => $e['weld_number']])->active()->asArray()->one();
				if (!isset($Ndt)) {
					$data['ndtCheck'] = "No";
					break;
				} else {
					array_push($NdtData, $Ndt);
				}
			}
		}

		//########################### No outstanding repairs ######################
		$data['repairCheck'] = "No";
		if (!empty($WeldingRecord)) {
			$Rejected = array();
			foreach ($WeldingRecord as $e) {
				$Ndt = \app\models\Ndt::find()->select(['kp', 'weld_number'])->where(['kp' => $e['kp'], 'weld_number' => $e['weld_number'], 'outcome' => 'Rejected'])->active()->asArray()->one();
				if (!empty($Ndt)) {
					array_push($Rejected, $Ndt);
				}
			}
			if (!empty($Rejected)) {
				foreach ($Rejected as $e) {
					$data['repairCheck'] = "Yes";
					$Repair = \app\models\Weldingrepair::find()->select(['kp', 'weld_number'])->where(['kp' => $e['kp'], 'weld_number' => $e['weld_number']])->active()->asArray()->one();
					if (!isset($Repair)) {
						$data['repairCheck'] = "No";
						break;
					}
				}
			}
		}

		//########################### Weld Coating Production ######################
		$data['coatingCheck'] = "No";
		if (!empty($WeldingRecord)) {
			foreach ($WeldingRecord as $e) {
				$data['coatingCheck'] = "Yes";
				$Production = \app\models\Production::find()->where(['kp' => $e['kp'], 'weld_number' => $e['weld_number']])->active()->asArray()->one();
				if (!isset($Production)) {
					$data['coatingCheck'] = "No";
					break;
				}
			}
		}

		//########################### Weld Coating Production Accepted ######################
		$data['coatingAccepted'] = "No";
		if ($data['ndtCheck'] == "Yes") {
			if (!empty($NdtData)) {
				foreach ($NdtData as $e) {
					$data['coatingAccepted'] = "Yes";
					$Production = \app\models\Production::find()->where(['kp' => $e['kp'], 'weld_number' => $e['weld_number']])->active()->asArray()->one();
					if (!empty($Production)) {
						if ($Production['outcome'] == "Rejected") {
							$CoatingRepair = \app\models\Coatingrepair::find()->where(['kp' => $Production['kp'], 'weld_number' => $Production['weld_number']])->active()->asArray()->one();
							if (empty($CoatingRepair)) {
								$data['coatingAccepted'] = "No";
							}
						}
					} else {
						$data['coatingAccepted'] = "No";
						break;
					}
				}
			}
		}

		//########################### Weld AnomalyCheck ######################
		$data['anomalyCheck'] = "No";
		if (!empty($WeldingRecord)) {
			foreach ($WeldingRecord as $e) {
				$data['anomalyCheck'] = "Yes";
				$Welding = \app\models\Welding::find()->where(['kp' => $e['kp'], 'weld_number' => $e['weld_number'], 'is_anomally' => 'Yes'])->active()->one();
				if (!empty($Welding)) {
					$data['anomalyCheck'] = "No";
					break;
				}
				$Parameter = \app\models\Parameter::find()->where(['kp' => $e['kp'], 'weld_number' => $e['weld_number'], 'is_anomally' => 'Yes'])->active()->one();
				if (!empty($Parameter)) {
					$data['anomalyCheck'] = "No";
					break;
				}

				$Ndt = \app\models\Ndt::find()->where(['kp' => $e['kp'], 'weld_number' => $e['weld_number'], 'is_anomally' => 'Yes'])->active()->one();
				if (!empty($Ndt)) {
					$data['anomalyCheck'] = "No";
					break;
				}

				$Weldingrepair = \app\models\Welding::find()->where(['kp' => $e['kp'], 'weld_number' => $e['weld_number'], 'is_anomally' => 'Yes'])->active()->one();
				if (!empty($Weldingrepair)) {
					$data['anomalyCheck'] = "No";
					break;
				}

				$Production = \app\models\Production::find()->where(['kp' => $e['kp'], 'weld_number' => $e['weld_number'], 'is_anomally' => 'Yes'])->active()->one();
				if (!empty($Production)) {
					$data['anomalyCheck'] = "No";
					break;
				}

				$Coatingrepair = \app\models\Coatingrepair::find()->where(['kp' => $e['kp'], 'weld_number' => $e['weld_number'], 'is_anomally' => 'Yes'])->active()->one();
				if (!empty($Coatingrepair)) {
					$data['anomalyCheck'] = "No";
					break;
				}
			}
		}
		$html = $this->render('@app/modules/report/views/report/_clearanceForm', ['data' => $data]);
		echo $html;
		die;
	}
}
