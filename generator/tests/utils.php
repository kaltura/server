<?php

function fixSlashes($str)
{
	return str_replace('\\', '/', $str);
}

function endsWith($str, $postfix) 
{
	if (is_array($postfix))
	{
		foreach ($postfix as $curPostfix)
		{
			if (substr($str, -strlen($curPostfix)) === $curPostfix)
				return true;
		}
		return false;
	}
	return (substr($str, -strlen($postfix)) === $postfix);
}

function listDir($path)
{
	$result = array();
	$dir = dir($path);
	while (false !== ($file = $dir->read()))
	{
		if($file[0] == '.')
			continue;
		$result[] = $file;
	}
	$dir->close();
	return $result;
}

function executeCommand($exe, $params = null)
{
	if ($exe)
	{
		$cmd = "\"{$exe}\"";
		if ($params)
			$cmd .= " {$params}";
	}
	else
		$cmd = $params;

	echo "Running {$cmd}\n";
	system($cmd);
}

function executeCommandFrom($path, $exe, $params = null)
{
	chdir($path);
	executeCommand($exe, $params);
}

function addPrefix($arr, $prefix)
{
	$result = array();
	foreach ($arr as $curStr)
	{
		$result[] = $prefix . $curStr;
	}
	return $result;
}

function replaceInFile($path, $search, $replace)
{
	$originalData = file_get_contents($path);
	$newData = str_replace($search, $replace, $originalData);
	if ($newData === $originalData)
		return;
	echo "updating {$path}...\n";
	file_put_contents($path, $newData);
}

function replaceInFolder($path, $includeSuffixes, $excludeSuffixes, $search, $replace)
{
	$fileList = listDir($path);
	foreach ($fileList as $curFile)
	{
		$curPath = "{$path}/{$curFile}";
		if (is_dir($curPath))
			replaceInFolder($curPath, $includeSuffixes, $excludeSuffixes, $search, $replace);
		else
		{
			if ($includeSuffixes && !endsWith($curPath, $includeSuffixes))
				continue;

			if ($excludeSuffixes && endsWith($curPath, $excludeSuffixes))
				continue;

			replaceInFile($curPath, $search, $replace);
		}
	}
}

