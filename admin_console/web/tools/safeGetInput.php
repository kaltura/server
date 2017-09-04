<?php

CONST SECRET_PATTERN = '/[^a-z0-9]/';
CONST ENTRY_ID_PATTERN = '/[^a-z0-9_]/';
CONST INTEGER_ONLY_PATTERN = '/[^0-9]/';
CONST HTML_VERSION_PATTERN = '/[^v0-9.]/';

function safeGetInput($fieldName, $pattern)
{
	$fieldValue = $_GET[$fieldName];
	if (preg_match($pattern, $fieldValue))
	{
		return '';
	}
	
	return $fieldValue;
}

?>
