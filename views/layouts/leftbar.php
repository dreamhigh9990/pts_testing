<?php
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Menu;
use yii\widgets\Pjax;
$PipeAnomaly = 14;

$loggedUser = '';
if(!empty(Yii::$app->user->identity->fullname)){
	$loggedUser = Yii::$app->user->identity->fullname;
}
?>

<div data-active-color="white" data-background-color="purple-bliss" data-image="<?php echo Yii::getAlias('@web'); ?>/app-assets/img/sidebar-bg/01.jpg" class="app-sidebar">
	<div class="sidebar-header">
		<div class="logo clearfix">
			<a href="<?= Url::to(['/']); ?>" class="logo-text float-left">
				<div class="logo-img">
					<img src="<?php echo Yii::getAlias('@web'); ?>/app-assets/img/logo.png"/>
				</div>
				<span class="text align-middle">Pipeline</span>
			</a>
			<!-- <a id="sidebarToggle" href="javascript:;" class="nav-toggle d-none d-sm-none d-md-none d-lg-block">
				<i data-toggle="collapsed" class="ft-toggle-left toggle-icon"></i>
			</a> -->
			<a id="sidebarClose" href="javascript:;" class="nav-close d-block d-md-block d-lg-none d-xl-none">
				<i class="ft-x"></i>
			</a>
		</div>
	</div>
	<div class="sidebar-content">
		<div class="nav-container">
		<?= Menu::widget([
				'options' => [
					'class' => 'navigation navigation-main',
					'id'=>'main-menu-navigation',
					'data-menu'=>'menu-navigation'
				],
				'itemOptions'=>[
					'class' => 'nav-item has-sub',
				],
				'encodeLabels' => false,
				'items' => [
					
					[
						'label' => '<i class="icon-graph"></i><span data-i18n="" class="menu-title"> '.Yii::$app->trans->getTrans('Pipe').'</span>',
						'visible'=>Yii::$app->user->identity->type != "Safety",	
						'url' => '#',
						'items' => [
							['label' => Yii::$app->trans->getTrans('Pipe'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/pipe/pipe/create']],
							['label' => Yii::$app->trans->getTrans('Reception'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/pipe/reception/create']],							
							['label' => Yii::$app->trans->getTrans('Stringing'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/pipe/stringing/create']],
							['label' => Yii::$app->trans->getTrans('Transfer'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/pipe/pipe-transfer/create']],
							['label' => Yii::$app->trans->getTrans('Bending'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/pipe/bending/create']],
							['label' => Yii::$app->trans->getTrans('Cutting'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/pipe/cutting/create']],
						],
					],
					[
						'label' => '<i class="icon-link"></i><span data-i18n="" class="menu-title"> '.Yii::$app->trans->getTrans('Welding').'</span>',
						'url' => '#',
						'visible'=>Yii::$app->user->identity->type != "Safety",	
						'items' => [
							['label' => Yii::$app->trans->getTrans('Welding'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/welding/welding/create']],
							['label' => Yii::$app->trans->getTrans('Parameter Check'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/welding/parameter/create']],
							['label' => Yii::$app->trans->getTrans('NDT'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/welding/ndt/create']],
							['label' => Yii::$app->trans->getTrans('Weld Repair'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/welding/weldingrepair/create']],
						],
					],
					[
						'label' => '<i class="icon-layers"></i><span data-i18n="" class="menu-title"> '.Yii::$app->trans->getTrans('Coating').'</span>',
						'url' => '#',
						'visible' => Yii::$app->user->identity->type != "Safety",	
						'items' => [							
							['label' => Yii::$app->trans->getTrans('Coating Production'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/welding/production/create']],
							['label' => Yii::$app->trans->getTrans('Coating Repair'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/welding/coatingrepair/create']],	
						],
					],
					[
						'label' => '<i class="icon-home"></i><span data-i18n="" class="menu-title"> '.Yii::$app->trans->getTrans('Civil').'</span>',
						'url' => '#',
						'visible'=>Yii::$app->user->identity->type != "Safety",	
						'items' => [
							['label' => Yii::$app->trans->getTrans('Clear & Grade'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/pipe/pipe-cleargrade/create']],
							['label' => Yii::$app->trans->getTrans('Trenching'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/civil/trenching/create']],
							['label' => Yii::$app->trans->getTrans('Lowering'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/civil/lowering/create']],
							['label' => Yii::$app->trans->getTrans('Backfilling'), 'options' => ['class' => 'sub-label-txt'], 'url' =>  ['/civil/backfilling/create']],
							['label' => Yii::$app->trans->getTrans('Reinstatement'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/civil/reinstatement/create']],
							['label' => Yii::$app->trans->getTrans('Special Crossings'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/civil/special-crossings/create']],
						],
					],
					[
						'label' => '<i class="fa fa-cogs"></i><span data-i18n="" class="menu-title"> '.Yii::$app->trans->getTrans('Pre Commissioning').'</span>','url' => '#',
						'visible'=>Yii::$app->user->identity->type != "Safety",	
						'items' => [
							['label' => Yii::$app->trans->getTrans('Cathodic Protection'), 'options' => ['class' => 'sub-label-txt'], 'url' =>  ['/precommissioning/cathodicprotection/create']],
							['label' => Yii::$app->trans->getTrans('Clean Gauge'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/precommissioning/cleangauge/create']],
							['label' => Yii::$app->trans->getTrans('Hydro Testing'), 'options' => ['class' => 'sub-label-txt'], 'url' =>['/precommissioning/hydrotesting/create']],
							['label' => Yii::$app->trans->getTrans('DCVG Surveying'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/precommissioning/surveying/create']],
						],
					],
					[
						'label' => '<i class="ft-activity"></i><span data-i18n="" class="menu-title"> '.Yii::$app->trans->getTrans('Cable').'</span>',
						'visible'=>Yii::$app->user->identity->type != "Safety",	
						'url' => '#',
						'items' => [
							['label' => Yii::$app->trans->getTrans('Drum'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/cabling/cable/create']],
							['label' => Yii::$app->trans->getTrans('Stringing'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/cabling/cab-stringing/create']],
							['label' => Yii::$app->trans->getTrans('Splicing'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/cabling/cab-splicing/create']],
						],
					],
					[
						'label' => '<i class="ft-file"></i><span data-i18n="" class="menu-title"> '.Yii::$app->trans->getTrans('Report').'</span>',
						'visible'=>Yii::$app->user->identity->type != "Safety",	
						'url' => '#',
						'items' => [
							['label' => Yii::$app->trans->getTrans('Heat Report'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/report/report/index','model'=>'PipeSearch']],
							['label' => Yii::$app->trans->getTrans('Visual Progress'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/report/report/visual-progress']],
							['label' => Yii::$app->trans->getTrans('Open End Summary'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/report/report/index','model'=>'WeldingSearch']],
							['label' => Yii::$app->trans->getTrans('Clearance'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/report/report/clearance']],
							['label' => Yii::$app->trans->getTrans('Review Summary'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/report/report/review-report','model'=>'ReceptionSearch']],
							['label' => Yii::$app->trans->getTrans('Weld Book'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/report/report/weldbook-report']],
							['label' => Yii::$app->trans->getTrans('Daily Production'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/report/report/production']],
							// [
							// 	'label' => 'Welder Analysis',
							// 	'url' => '#',
							// 	'items' => [
							// 		['label' => 'Overall', 'options'=> ['class'=>''], 'url' => ['/report/report/welder-overall']],
							// 		['label' => 'Combined', 'options'=> ['class'=>''], 'url' => ['/report/report/welder-combine']],
							// 		['label' => 'Individual', 'options'=> ['class'=>''], 'url' => ['/report/report/welder-detail']],
							// 	]
							// ],							
						],
					],
					[
						'label' => '<i class="icon-settings"></i><span data-i18n="" class="menu-title sub-label-txt"> '.Yii::$app->trans->getTrans('Administration').'</span>',
						'visible'=>Yii::$app->user->identity->type != "Safety",	
						'url' => "#",		
						'visible'=>Yii::$app->user->identity->type == "Admin" || Yii::$app->user->identity->type == "QA Manager",			
						'items' => [						
							['label' => Yii::$app->trans->getTrans('Projects'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/admin/project/create'],'visible'=>(Yii::$app->user->identity->type == "Admin"),],
							['label' => Yii::$app->trans->getTrans('User'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/admin/employee/create'],'visible'=>(Yii::$app->user->identity->type == "Admin" || Yii::$app->user->identity->type == "QA Manager"),],							
							['label' => Yii::$app->trans->getTrans('Catalogue'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/admin/taxonomy/create'],'visible'=>(Yii::$app->user->identity->type == "Admin" || Yii::$app->user->identity->type == "QA Manager"),],
							['label' => Yii::$app->trans->getTrans('Change Brand Logo'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/admin/setting/create'],'visible'=>(Yii::$app->user->identity->type == "Admin"),],
							['label' => Yii::$app->trans->getTrans('Line List'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/admin/line/create']],
							['label' => Yii::$app->trans->getTrans('Landowner'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/admin/landowner/create']],							
							// ['label' => 'Sync Log Report', 'options'=> ['class'=>''], 'url' => ['/admin/log/index']],
							['label' => Yii::$app->trans->getTrans('Anomaly'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/admin/anomaly/index','model'=>'PipeSearch']],
							['label' => Yii::$app->trans->getTrans('Database Manager'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/db-manager/default/index'], 'visible' => (Yii::$app->user->identity->type == "Admin")],
						],
					],	
					[
						'label' => '<i class="icon-user"></i><span data-i18n="" class="menu-title sub-label-txt"> '.Yii::$app->trans->getTrans('Safety Management').'</span>',
						'url' => "#",		
						'visible'=>Yii::$app->user->identity->type == "Admin" || Yii::$app->user->identity->type == "Safety",			
						'items' => [						
							['label' => Yii::$app->trans->getTrans('SLAM Report'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/report/safety/slam']],
							['label' => Yii::$app->trans->getTrans('Hazard Report'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/report/safety/hazard']],
							[
								'label' => Yii::$app->trans->getTrans('Vehicle Pre-Start'),
								'url' => '#',
								'items' => [
									['label' => Yii::$app->trans->getTrans('Vehicle Schedule'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/vehicle/schedule/create']],
									['label' => Yii::$app->trans->getTrans('Vehicle Inspection'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/vehicle/inspection/create']],
									['label' => Yii::$app->trans->getTrans('Plant Dashboard'), 'options' => ['class' => 'sub-label-txt'], 'url' => ['/vehicle/plant/dashboard']],
								]
							],
						],
					],
					
					[
						'label' => '<i class="ft-power mr-2"></i><span data-i18n="" class="menu-title">'.Yii::$app->trans->getTrans('Logout').'</span>',
						'url' => ['/site/logout'],
						'items' => [							
							['label' => Yii::$app->trans->getTrans('Logout').' ('.$loggedUser.')', 'options' => ['class' => 'sub-label-txt'], 'url' => ['/site/logout']],
						],
					],
				],
			]);
		?>
		</div>
		<?php Pjax::begin(['id'=>"listproject"]); ?>
			<?php if(Yii::$app->user->identity->type == "Admin"){ ?>
			<div class="list-project">
				<?php $projects = Yii::$app->general->TaxonomyDrop(4,true);?>
				<?= Html::dropDownList('project',Yii::$app->user->identity->project_id, $projects, ['prompt'=>Yii::$app->trans->getTrans('Please select'),'id'=>'basicSelect','class'=>'custom-select cz-sidebar-width float-right']) ?>
				<i class="fa fa-building-o lang-icons"></i>
			</div>
			<?php } else { ?>
			<div class="list-project">
				<span class="spn-prj pull-right">
					<?php
						$projectData = Yii::$app->general->getTaxoDataFromId(Yii::$app->user->identity->project_id);
						echo $projectData['value'];
					?>
				</span>
				<i class="fa fa-building-o lang-icons"></i>
			</div>
			<?php } ?>
		<?php Pjax::end(); ?>
		
		<?php Pjax::begin(['id' => 'listlanguage']); ?>
			<div class="list-language">
				<?php $langs = ['en' => 'English', 'fr' => 'French'];?>
				<?= Html::dropDownList('languages', Yii::$app->user->identity->lang, $langs, ['prompt' => Yii::$app->trans->getTrans('Please select'), 'id' => 'langSelect', 'class' => 'custom-select cz-sidebar-width float-right']) ?>
				<i class="fa fa-globe lang-icons"></i>
			</div>
		<?php Pjax::end(); ?>
	</div>
    <div class="sidebar-background"></div>
</div>