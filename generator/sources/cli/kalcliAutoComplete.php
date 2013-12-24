<?php
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================

require_once(dirname(__file__) . '/kalcliSwitches.php');

define('PROPERTY_DELIMITER', ':');
define('ASSIGN_DELIMITER', '=');
define('FILENAME_DELIMITER', '@');

// String/completion utility functions
function stripSuffix($str, $postfix)
{
	if (substr($str, -strlen($postfix)) === $postfix)
		return substr($str, 0, -strlen($postfix));
	return null;
}

function startsWith($str, $prefix)
{
	return (substr($str, 0, strlen($prefix)) === $prefix);
}

function findPosition($autoComp, $serviceMap, $strCompFunc)
{
	$left = 0;
	$right = count($serviceMap);
	while ($left < $right)
	{
		$mid = floor(($right + $left) / 2);
		if (call_user_func($strCompFunc, $serviceMap[$mid], $autoComp) < 0)
			$left = $mid + 1;
		else
			$right = $mid;		
	}
	return $left;
}

function addPrefix($strArr, $prefix)
{
	$result = array();
	foreach ($strArr as $curStr)
		$result[] = $prefix . $curStr;
	return $result;
}

function filterCompletions($autoCompWord, $completions, $strCompFunc = 'strcmp')
{
	usort($completions, $strCompFunc);

	$startPos = findPosition($autoCompWord, $completions, $strCompFunc);
	$endPos = findPosition($autoCompWord . "\xff", $completions, $strCompFunc);

	return array_slice($completions, $startPos, $endPos - $startPos);
}

function printCompletions($autoCompWord, $completions, $finalComp = false, $filterCompletions = true, $strCompFunc = 'strcmp')
{
	global $compLinePos;

	if ($filterCompletions)
		$completions = filterCompletions($autoCompWord, $completions, $strCompFunc);
	
	if (!$completions)
		exit(1);
	
	if (count($completions) == 1)
	{
		$explodedResult = explode('__', reset($completions));
		$completions = array($explodedResult[0]);

		if (!$finalComp)
			echo substr($completions[0], $compLinePos) . "_\n";		// add another dummy completion option
	}
	
	foreach ($completions as $curComp)
		echo substr($curComp, $compLinePos) . "\n";
	exit(0);
}

// Api schema utility functions
function getApiSchemaData($fileName, $errorResult)
{
	$fullFileName = dirname(__file__).$fileName;
	if (!file_exists($fullFileName))
		return $errorResult;
	return unserialize(file_get_contents($fullFileName));
}

function getServiceIds()
{
	return getApiSchemaData("/services/services.map", array());
}

function getActionNames($serviceId)
{
	return getApiSchemaData("/services/{$serviceId}.service", array());
}

function getDerivedTypesList($objectName)
{
	$objectInfo = getApiSchemaData("/objects/{$objectName}.object", array('derivedTypes' => array()));
	return $objectInfo['derivedTypes'];
}

function getTypePropertiesList($objectName)
{
	$objectInfo = getApiSchemaData("/objects/{$objectName}.object", array('properties' => array()));
	return $objectInfo['properties'];
}

function getObjectPropertyAttribute($objectName, $propertyName, $attributeName)
{
	$properties = getTypePropertiesList($objectName);
	if (!isset($properties[$propertyName]))
		return null;
	$propertyInfo = $properties[$propertyName];
	if (!isset($propertyInfo[$attributeName]))
		return null;
	return $propertyInfo[$attributeName];
}

function getEnumValueList($enumType)
{
	return getApiSchemaData("/enums/{$enumType}.enum", array());
}

function getActionParameters($serviceId, $actionName)
{
	return getApiSchemaData("/actions/{$serviceId}/{$actionName}.action", array());
}

function getActionParameterAttribute($serviceId, $actionName, $paramName, $attributeName)
{
	$params = getActionParameters($serviceId, $actionName);
	if (!isset($params[$paramName]))
		return null;
	$paramInfo = $params[$paramName];
	if (!isset($paramInfo[$attributeName]))
		return null;
	return $paramInfo[$attributeName];
}

// Completion functions
function getTypeName($typeName)
{
	switch ($typeName)
	{
		case 'bigint':
			return "int";
		case 'bool':
		case 'int':
		case 'float':
		case 'string':
		case 'file':
		case 'array':
			return $typeName;	
		default:
			return 'object';
	}
}

function addPropertySuffix($propertyDetails)
{
	$result = $propertyDetails['name'];
	switch (getTypeName($propertyDetails['type']))
	{
	case 'object':
	case 'array':
		return $result . PROPERTY_DELIMITER;

	case 'file':
		return $result . ASSIGN_DELIMITER . FILENAME_DELIMITER;
		
	default:
		return $result . ASSIGN_DELIMITER;
	}
}

function getEnumValueCompletion($constDetails)
{
	$constName = $constDetails['name'];
	$constValue = $constDetails['value'];
	return "{$constValue}__({$constName})";
}

function getPropertyAttribute($serviceName, $actionName, $paramName, $attributeName)
{
	global $paramValues;

	$prevColon = strrpos($paramName, PROPERTY_DELIMITER);
	if ($prevColon === false)
		return getActionParameterAttribute($serviceName, $actionName, $paramName, $attributeName);

	$prevParam = substr($paramName, 0, $prevColon);
	$propertyName = substr($paramName, $prevColon + 1);
	if (is_numeric($propertyName) && $attributeName == 'type')
		return getPropertyAttribute($serviceName, $actionName, $prevParam, 'arrayType');
	
	$objectTypeField = $prevParam.PROPERTY_DELIMITER."objectType";
	if (!isset($paramValues[$objectTypeField]))
		return null;
	return getObjectPropertyAttribute($paramValues[$objectTypeField], $propertyName, $attributeName);
}

function getNextArrayIndex($paramName)
{
	global $paramValues;

	if (isset($paramValues[$paramName.PROPERTY_DELIMITER.'-']))
		return -1;
		
	for ($curIndex = 0; 
		isset($paramValues[$paramName.PROPERTY_DELIMITER.$curIndex.PROPERTY_DELIMITER.'objectType']); 
		$curIndex++);
	return $curIndex;
}
	
function getParameterNameCompletions($serviceName, $actionName, $autoCompWord, &$finalComp)
{
	global $paramValues;
	
	$finalComp = false;

	$assignPos = strpos($autoCompWord, ASSIGN_DELIMITER);
	if ($assignPos !== false)
	{
		$paramName = substr($autoCompWord, 0, $assignPos);
		$objectProperty = stripSuffix($paramName, PROPERTY_DELIMITER."objectType");
		if ($objectProperty)
		{
			$baseType = getPropertyAttribute($serviceName, $actionName, $objectProperty, 'type');
			if ($baseType)
			{
				$possibleTypes = getDerivedTypesList($baseType);
				$finalComp = true;
				return addPrefix($possibleTypes, $paramName.ASSIGN_DELIMITER);
			}
		}
		else
		{
			$enumType = getPropertyAttribute($serviceName, $actionName, $paramName, 'enumType');
			if ($enumType)
			{
				$enumValues = array_map('getEnumValueCompletion', getEnumValueList($enumType));
				$finalComp = true;
				return addPrefix($enumValues, $paramName.ASSIGN_DELIMITER);
			}
			else if ($assignPos + 1 < strlen($autoCompWord) && $autoCompWord[$assignPos + 1] == FILENAME_DELIMITER)
			{
				$prefix = $paramName.ASSIGN_DELIMITER.FILENAME_DELIMITER;
				$fileStartPos = $assignPos + 2;
				if ($assignPos + 2 < strlen($autoCompWord) && $autoCompWord[$assignPos + 2] == FILENAME_DELIMITER)
				{
					$prefix .= FILENAME_DELIMITER;
					$fileStartPos++;
				}
					
				// upload file name completion
				$fileName = substr($autoCompWord, $fileStartPos);
				$completions = null;
				exec("compgen -f -- '{$fileName}'", $completions);
				$finalComp = true;
				return addPrefix($completions, $prefix);
			}
		}
	}
	else 
	{
		$colonPos = strrpos($autoCompWord, PROPERTY_DELIMITER);
		if ($colonPos !== false)
		{
			$paramName = substr($autoCompWord, 0, $colonPos);
			$paramType = getPropertyAttribute($serviceName, $actionName, $paramName, 'type');
			if ($paramType == 'array')
			{
				$nextIndex = getNextArrayIndex($paramName);
				if ($nextIndex == 0)
				{
					$completions = array('-', '0'.PROPERTY_DELIMITER."objectType".ASSIGN_DELIMITER);
				}
				else
				{
					$completions = array();
					for ($curIndex = 0; $curIndex < $nextIndex; $curIndex++)
					{
						$completions[] = $curIndex.PROPERTY_DELIMITER;
					}
					$completions[] = $nextIndex.PROPERTY_DELIMITER."objectType".ASSIGN_DELIMITER;
				}
				$finalComp = startsWith($autoCompWord, $paramName.PROPERTY_DELIMITER.'-');
				return addPrefix($completions, $paramName.PROPERTY_DELIMITER);
			}
			else if ($paramType && getTypeName($paramType) == 'object')
			{
				$objectTypeField = $paramName.PROPERTY_DELIMITER."objectType";
				if (!isset($paramValues[$objectTypeField]))
				{
					$completions = array($objectTypeField . ASSIGN_DELIMITER);
					return $completions;
				}
				else
				{
					$objectType = $paramValues[$objectTypeField];
					$propertyList = array_map('addPropertySuffix', getTypePropertiesList($objectType));
					return addPrefix($propertyList, $paramName.PROPERTY_DELIMITER);
				}
			}
		}
		else
		{
			$completions = array_map('addPropertySuffix', getActionParameters($serviceName, $actionName));
			return $completions;
		}
	}
	return array();
}

// break the input args into comp words & comp line
$delimPos = array_search('-=-', $argv);
$compWords = array_slice($argv, 2, $delimPos - 2);
$compLine = array_slice($argv, $delimPos + 1);

// translate the comp words index into a compline index
$compWordsIndex = $argv[1];
$compLineIndex = 1;
$compLinePos = 0;
$compWordsCount = count($compWords);

for ($curIndex = 1; $curIndex <= $compWordsIndex; $curIndex++)
{
	if ($curIndex >= $compWordsCount)
	{
		$compLineIndex++;
		$compLinePos = 0;
		break;
	}
	
	for (;;)
	{
		$compLinePos = strpos($compLine[$compLineIndex], $compWords[$curIndex], $compLinePos);
		if ($compLinePos === false)
		{
			$compLineIndex++;
			$compLinePos = 0;
			continue;
		}
		
		if ($curIndex < $compWordsIndex || in_array($compWords[$curIndex], array(':', '=')))	// if comp words ends with :/= we shouldn't output the :/=
		{
			$compLinePos += strlen($compWords[$curIndex]);
		}
		break;
	}
}

// get the completion word
$autoCompWord = '';
if (isset($compLine[$compLineIndex]))
	$autoCompWord = trim($compLine[$compLineIndex]);
	
// build a dictionary of previously assigned parameters
$paramValues = array();
$compLineCount = count($compLine);
for ($curIndex = 3; $curIndex < $compLineCount; $curIndex++)
{
	$explodedParam = explode(ASSIGN_DELIMITER, $compLine[$curIndex], 2);
	if (count($explodedParam) == 2)
		$paramValues[$explodedParam[0]] = $explodedParam[1];
}

// adjust compLineIndex according to number of previous switches
$previousArgs = array_slice($compLine, 0, $compLineIndex);
$compLine = array_merge(KalturaCommandLineParser::stripCommandLineSwitches($commandLineSwitches, $previousArgs), array_slice($compLine, $compLineIndex));
$compLineIndex = count(KalturaCommandLineParser::stripCommandLineSwitches($commandLineSwitches, $previousArgs)) + 1;

switch ($compLineIndex)
{
case 1:		// service name / switch
	$completions = getServiceIds();
	$completions = array_merge($completions, KalturaCommandLineParser::getAllCommandLineSwitches($commandLineSwitches));
	printCompletions($autoCompWord, $completions, true, true, 'strcasecmp');
	break;

case 2:		// action name
	$completions = getActionNames($compLine[0]);
	printCompletions($autoCompWord, $completions, true, true, 'strcasecmp');
	break;
	
default:	// parameters
	$finalComp = false;
	$curAutoCompWord = $autoCompWord;
	$completions = array();
	for (;;)
	{
		$newCompletions = getParameterNameCompletions($compLine[0], $compLine[1], $curAutoCompWord, $finalComp);
		if (!$newCompletions)
			break;
		$completions = filterCompletions($autoCompWord, $newCompletions);
		if (count($completions) != 1 || $finalComp)
			break;
		$curAutoCompWord = $completions[0];
		if (substr($curAutoCompWord, -1) == FILENAME_DELIMITER)
			break;		// don't automatically load all file completions (usually a lot..)
	}
	printCompletions($autoCompWord, $completions, $finalComp, false);
	break;
}
