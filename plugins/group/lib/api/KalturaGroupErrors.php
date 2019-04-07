<?php
/**
 * @package plugins.group
 * @subpackage errors
 */

class KalturaGroupErrors extends KalturaErrors
{
	const INVALID_GROUP_ID = "INVALID_GROUP_ID;ID;Invalid group id - @ID@";
	const DUPLICATE_GROUP_BY_ID= "DUPLICATE_GROUP_BY_ID;ID;Group with id [@ID@] already exists in system";
}