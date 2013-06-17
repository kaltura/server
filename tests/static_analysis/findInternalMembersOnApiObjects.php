<?php

require_once(dirname(__FILE__).'/../../api_v3/bootstrap.php');

class KalturaException
{
}

class KalturaClientException
{
}

function classHasRelevantName($str)
{
	return strpos($str, 'Kaltura') === 0 && strpos($str, '_') === false;
}

function classHasRelevantPath($str)
{
	return strpos($str, '/api_v3/') !== false || strpos($str, '/plugins/') !== false;
}

$inheritMapPath = kConf::get("cache_root_path") . '/scripts/inheritMap.cache';
if (!is_dir(dirname($inheritMapPath)))
{
	mkdir(dirname($inheritMapPath), 0777, true);
}

KalturaTypeReflector::setClassInheritMapPath($inheritMapPath);
$classMap = KAutoloader::getClassMap();
$classMap = array_filter($classMap, 'classHasRelevantPath');
$classMap = array_keys($classMap);
$classMap = array_filter($classMap, 'classHasRelevantName');

KalturaTypeReflector::setClassMap($classMap);

$apiClasses = KalturaTypeReflector::getSubClasses('KalturaObject');

$testCount = 0;
foreach ($apiClasses as $apiClass)
{
	$parents = class_parents($apiClass);
	if ($apiClass == 'KalturaTypedArray' || in_array('KalturaTypedArray', $parents))
		continue;

	$refClass = new ReflectionClass($apiClass);
	$properties = $refClass->getProperties();
	$propNames = array();
	foreach($properties as $property)
	{
		if ($property->isStatic())
			continue;
		$propNames[] = $property->name;
	}

	$refClass = new KalturaTypeReflector($apiClass);
	$apiPropNames = array_keys($refClass->getProperties());

	$internalProps = array_diff($propNames, $apiPropNames);
	if ($internalProps)
	{
		echo "Error: $apiClass has members that are not exposed via API: ".implode(',', $internalProps)."\n";
	}
	$testCount++;
}
echo "Done: tested {$testCount} classes\n";
