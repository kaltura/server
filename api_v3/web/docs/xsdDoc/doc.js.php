<?php 
require_once("../../../bootstrap.php"); 

// get cache file name
$cachePath = kConf::get("cache_root_path").'/xsdDoc';
$cacheFilePath = "$cachePath/main.js.cache";

// display left pane
if (file_exists($cacheFilePath))
{
	print file_get_contents($cacheFilePath);
	die;
}


ob_start();

	$list = array(
		array(
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
				)
			)
		)
	);
		
	$config = new Zend_Config_Ini("../../../config/testme.ini", null, array('allowModifications' => true));
	$config = KalturaPluginManager::mergeConfigs($config, 'xsddoc');
	$indexConfig = $config->get('xsddoc');
	
	$exclude = explode(',', $indexConfig->get("exclude"));
	$schemaReflector = KalturaTypeReflectorCacher::get('KalturaSchemaType');
	$schemas = $schemaReflector->getConstants();
	$schemaItems = array();
	
	foreach($schemas as $schema)
	{
		$schemaItems[] = array(
			'text' => $schema->getDescription(),
			'id' => 'type=' . $schema->getDefaultValue(),
			'leaf' => true,
			'allowDrag' => false,
			'iconCls' => 'icon-schema',
			'cls' => 'file'
		);
	}
		
	$list[] = array(
		'text' => 'Reference',
		'id' => 'reference',
		'leaf' => false,
		'allowDrag' => false,
		'iconCls' => 'icon-docs-folder',
		'cls' => 'folder',
		'children' => $schemaItems
	);
	
	echo json_encode($list);
	
$out = ob_get_contents();
ob_end_clean();
print $out;

kFile::setFileContent($cacheFilePath, $out);
