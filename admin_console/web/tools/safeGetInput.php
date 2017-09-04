<?php

CONST SECRET_PATTERN = '/[^a-z0-9]/';
CONST ENTRY_ID_PATTERN = '/[^a-z0-9_]/';
CONST INTEGER_ONLY_PATTERN = '/[^0-9]/';
CONST HTML_VERSION_PATTERN = '/[^v0-9.]/';

function safeGetInput($filedName, $pattern)
{
	$fieldValue = $_GET[$filedName];
	if (preg_match($pattern, $fieldValue))
	{
		return '';
	}
	
	return $fieldValue;
}

?>
