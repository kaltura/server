<?php 
require_once("../../../bootstrap.php"); 
require_once("helpers.php");

$node = (isset($_GET['node']) ? $_GET['node'] : 'main');

// get cache file name
$cachePath = kConf::get("cache_root_path").'/testmeDoc';
$cacheFilePath = "$cachePath/$node.js.cache";

// display left pane
if (file_exists($cacheFilePath))
{
	print file_get_contents($cacheFilePath);
	die;
}


ob_start();

	$list = array();
	
	if($node == 'main')
	{
		$list[] = array(
			'text' => 'General',
			'id' => 'general',
			'leaf' => false,
			'allowDrag' => false,
			'cls' => 'folder',
			'iconCls' => 'icon-docs-folder',
			'expanded' => false,
			'children' => array(
				array(
					'text' => 'Overview',
					'id' => 'page=overview',
					'leaf' => true,
					'allowDrag' => false,
					'iconCls' => 'icon-doc',
					'cls' => 'file'
				),
				array(
					'text' => 'Terminology',
					'id' => 'page=terminology',
					'leaf' => true,
					'allowDrag' => false,
					'iconCls' => 'icon-doc',
					'cls' => 'file'
				),
				array(
					'text' => 'Request/Response structure',
					'qtip' => 'Request/Response',
					'id' => 'page=inout',
					'leaf' => true,
					'allowDrag' => false,
					'iconCls' => 'icon-doc',
					'cls' => 'file'
				),
				array(
					'text' => 'Multi-Request',
					'id' => 'page=multirequest',
					'leaf' => true,
					'allowDrag' => false,
					'iconCls' => 'icon-doc',
					'cls' => 'file'
				),
				array(
					'text' => 'Notifications',
					'id' => 'page=notifications',
					'leaf' => true,
					'allowDrag' => false,
					'iconCls' => 'icon-doc',
					'cls' => 'file'
				)
			)
		);
		
		$list[] = array(
			'text' => 'Reference',
			'id' => 'reference',
			'leaf' => false,
			'allowDrag' => false,
			'iconCls' => 'icon-docs-folder',
			'cls' => 'folder',
			'children' => array(
				array(
					'text' => 'Services',
					'id' => 'services',
					'leaf' => false,
					'allowDrag' => false,
					'iconCls' => 'icon-docs-folder',
					'cls' => 'folder'
				),
				array(
					'text' => 'Objects',
					'id' => 'objects',
					'leaf' => false,
					'allowDrag' => false,
					'iconCls' => 'icon-docs-folder',
					'cls' => 'folder'
				),
				array(
					'text' => 'Filters',
					'id' => 'filters',
					'leaf' => false,
					'allowDrag' => false,
					'iconCls' => 'icon-docs-folder',
					'cls' => 'folder'
				),
				array(
					'text' => 'Arrays',
					'id' => 'arrays',
					'leaf' => false,
					'allowDrag' => false,
					'iconCls' => 'icon-docs-folder',
					'cls' => 'folder'
				),
				array(
					'text' => 'Enumerators',
					'id' => 'enums',
					'leaf' => false,
					'allowDrag' => false,
					'iconCls' => 'icon-docs-folder',
					'cls' => 'folder'
				)
			)
		);
	}
	else
	{
		$config = new Zend_Config_Ini("../../../config/testme.ini", null, array('allowModifications' => true));
		$config = KalturaPluginManager::mergeConfigs($config, 'testme');
		$indexConfig = $config->get('testmedoc');
		
		$include = $indexConfig->get("include");
		$exclude = $indexConfig->get("exclude");
		$additional = $indexConfig->get("additional");
			
		$clientGenerator = new DummyForDocsClientGenerator();
		$clientGenerator->setIncludeOrExcludeList($include, $exclude);
		$clientGenerator->setAdditionalList($additional);
		$clientGenerator->load();
		
		switch($node)
		{
			case 'services':
				$services = $clientGenerator->getServices();
				
				foreach($services as $serviceName => $serviceReflector)
				{
					/* @var $serviceReflector KalturaServiceReflector */
			
					$deprecated = $serviceReflector->isDeprecated() ? ' (deprecated)' : '';
					$serviceId = $serviceReflector->getServiceId();
					$service = array(
						'text' => $serviceName . $deprecated,
						'qtip' => $serviceName,
						'id' => "service=$serviceId",
						'leaf' => false,
						'allowDrag' => false,
						'iconCls' => 'icon-service',
						'cls' => 'folder',
						'expanded' => false,
						'children' => array()
					);
					
					$actions = $serviceReflector->getActions();
					foreach($actions as $actionId => $actionName)
					{
						$action = $serviceReflector->getActionInfo($actionId);
						$deprecated = $action->deprecated ? ' (deprecated)' : '';
						$service['children'][] = array(
							'text' => $actionId . $deprecated,
							'qtip' => "$serviceName::$actionId",
							'id' => "service=$serviceId&action=$actionId",
							'leaf' => true,
							'allowDrag' => false,
							'iconCls' => 'icon-action',
							'cls' => 'file'
						);
					}
			
					$list[] = $service;
				}
				break;
		
			case 'enums':
				$objects = $clientGenerator->getStringEnums();
				
				foreach($objects as $type => $objectReflector)
				{
					/* @var $objectReflector KalturaTypeReflector */
			
					$deprecated = $objectReflector->isDeprecated() ? ' (deprecated)' : '';
					$list[] = array(
						'text' => $type . $deprecated,
						'qtip' => $type,
						'id' => "object=$type",
						'leaf' => true,
						'allowDrag' => false,
						'iconCls' => 'icon-object',
						'cls' => 'file'
					);
				}
				
			case 'objects':
			case 'arrays':
			case 'filters':
				$getter = "get{$node}";
				$objects = $clientGenerator->$getter();
				
				foreach($objects as $type => $objectReflector)
				{
					/* @var $objectReflector KalturaTypeReflector */
			
					$deprecated = $objectReflector->isDeprecated() ? ' (deprecated)' : '';
					$list[] = array(
						'text' => $type . $deprecated,
						'qtip' => $type,
						'id' => "object=$type",
						'leaf' => true,
						'allowDrag' => false,
						'iconCls' => 'icon-object',
						'cls' => 'file'
					);
				}
				break;
		}
	}
	
	echo json_encode($list);
	
$out = ob_get_contents();
ob_end_clean();
print $out;

kFile::setFileContent($cacheFilePath, $out);
