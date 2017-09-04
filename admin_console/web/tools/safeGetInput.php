<?php

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
