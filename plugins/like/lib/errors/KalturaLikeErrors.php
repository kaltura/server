<?php
/**
 * @package plugins.attachment
 * @subpackage api.errors
 */
class KalturaLikeErrors extends KalturaErrors
{
	const USER_LIKE_FOR_ENTRY_ALREADY_EXISTS = "USER_LIKE_FOR_ENTRY_ALREADY_EXISTS;;This user already likes this entry";
	
	const USER_LIKE_FOR_ENTRY_NOT_FOUND = "USER_LIKE_FOR_ENTRY_NOT_FOUND;;This user does not like this entry";
}